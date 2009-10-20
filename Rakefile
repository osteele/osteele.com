load 'build/Rakefile'

task :default => :dry_run

task :banner => 'includes/footer-banner.php'

%w[services.php includes/footer-banner.php].each do |name|
  file name => name.sub(/\..*?$/, '.haml') do |t|
    puts "Creating #{t.name}"
    sh "haml #{t.prerequisites.first} #{t.name}"
  end
end

task :projects do
  puts `cd projects && rake`
end
