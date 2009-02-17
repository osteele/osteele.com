load 'build/Rakefile'

task :default => [:home, 'includes/footer-banner.php']

task :home => ['home.php', 'services.php', 'stylesheets/home.css']
task :banner => 'includes/footer-banner.php'

%w[home.php services.php includes/footer-banner.php].each do |name|
  file name => name.sub(/\..*?$/, '.haml') do |t|
    puts "Creating #{t.name}"
    sh "haml #{t.prerequisites.first} #{t.name}"
  end
end

file 'stylesheets/home.css' => 'stylesheets/home.sass' do
  puts "Creating stylesheets/home.css"
  `sass stylesheets/home.sass stylesheets/home.css`
end

task :projects do
  puts `cd projects && rake`
end
