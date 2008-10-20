$: << "#{File.dirname(__FILE__)}/../drupal2wordpress"
require 'migrate'

module Drupal
  class Node < DrupalDatabase
    def wp_node
      Wordpress::Post.find_by_post_title(title, :conditions => "post_status != 'inherit'")
    end
  end
end

def diff_content(title)
  dp = Drupal::Node.find_by_title(title)
  wp = dp.wp_node
  f1, f2 = Tempfile.new('post'), Tempfile.new('post')
  f1 << dp.current_revision.body
  f2 << wp.post_content
  puts `diff -b #{f1.path} #{f2.path}`.gsub("\n\n", '')
  f1.close
  f2.close
end

def compare_content
  Drupal::Node.find(:all, :conditions => "type = 'blog'").count do |dp|
    wp = dp.wp_node
    puts "Couldn't find #{dp.title}" unless wp
    next unless wp
    next if wp.post_content == dp.current_revision.body
    puts "Content differs: #{dp.title}"
    l1 = wp.post_content.split("\n")
    l2 = dp.current_revision.body.split("\n")
    l1.zip(l2).each do |a, b|
      next if a == b
      p a
      p b
      #break
    end
#     puts wp.post_content
#     puts dp.current_revision.body
    diff_content(dp.title)
  end
end

def geshi
  Wordpress::Post.find(:all).count do |wp|
    %w[post_content post_excerpt].each do |field|
      text = wp.attributes[field]
      next unless text =~ /\[code/
      puts wp.post_title unless wp.post_status == 'inherit'
      wp.attributes[field.intern] = text.
        gsub(/\[code(.*?)\](.*?)\[\/code\]/m, '<pre\1>\2</pre>').
        gsub(/(<pre\s+lang)uage=/, '\1=')
      wp.save!
    end
  end
end

def fix_typos
  wp = Wordpress::Post.find_by_post_title("My Git Workflow")
  wp.post_content = wp.post_content.
    sub(/(The <code>-a)<\/a>( option)/, '\1</code>\2')
  wp.save!
end

def museums
  ['Apple Dylan', 'Frost & Fire', 'Method Software', 'Pogo Joe', 'Quickdraw GX',
    'Sandpaper', 'Storyspace', 'Tiles'].each do |title|
    post = Wordpress::Post.find_by_post_title_and_post_type(title, 'post')
    raise "No #{title}" unless post
    post.post_type = 'page'
    post.save!
  end if false
end

def fix_aliases
  Drupal::Node.find(:all, :conditions => "type = 'blog'").count do |dp|
    aliases = Drupal::UrlAlias.find_aliases_for_node(dp)
    next unless aliases.any?
    wp = dp.wp_node
    puts "Couldn't find #{dp.title}" unless wp
    next unless wp
    date = wp.post_date
    terms = {
      :year => date.year,
      :monthnum => '%02d' % date.month,
      :postname => wp.post_name
    }
    wp_permalink = "archives/%year%/%monthnum%/%postname%".
      gsub(/%.+?%/) { |k| terms[k[/%(.+)%/, 1].intern] || raise("Unknown permalink term: #{k.inspect}") }
    dp_permalink = aliases.first.dst
    next if dp_permalink == wp_permalink
    pat = /^(.*?)([^\/]*)$/
    if dp_permalink[pat, 1] == wp_permalink[pat, 1]
      #puts "Renaming #{dp.title}: #{wp_permalink} -> #{dp_permalink}"
      wp.post_name = dp_permalink[pat, 2]
      wp.save!
      next
    end
    puts "Alias #{dp.title}: #{wp_permalink} != #{dp_permalink}"
  end
end

def repair
  geshi
  fix_typos
  fix_aliases
end

module Wordpress
  class Option < WordpressDatabase
    set_primary_key 'option_id'
    set_table_name Wordpress::TABLE_PREFIX + 'options'
  end
  
  module Options
    def self.[](key)
      option = Option.find_by_option_name(key)
      option && option.option_value
    end
    
    def self.[]=(key, value)
      option = Option.find_or_create_by_option_name(key)
      option.option_value = value
      option.save!
    end
  end
end

def change_site_home(from, to)
  %w[siteurl home].count do |key|
    value = Wordpress::Options[key]
    new_value = value.gsub(from, to)
    next if new_value == value
    puts "#{key}: #{value} -> #{new_value}"
    Wordpress::Options[key] = new_value
  end
end

def to_com
  change_site_home('osteele.dev', 'osteele.com')
  Wordpress::Options['upload_path'] = "/home/osteele/osteele.com/wp/wp-content/uploads"
end

def to_dev
  change_site_home('osteele.com', 'osteele.dev')
  Wordpress::Options['upload_path'] = "/Users/osteele/Sites/osteele.com/wp/wp-content/uploads"
end
