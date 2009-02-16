load 'build/Rakefile'

task :default => [:home, 'includes/footer-banner.php']

task :home => ['home.php', 'stylesheets/home.css']
task :banner => 'includes/footer-banner.php'

file 'home.php' => 'home.haml' do
  puts "Creating home.php"
  `haml home.haml home.php`
end

file 'stylesheets/home.css' => 'stylesheets/home.sass' do
  puts "Creating home.css"
  `sass stylesheets/home.sass stylesheets/home.css`
end

file 'includes/footer-banner.php' => 'includes/footer-banner.haml' do
  puts "Creating footer-banner.php"
  `haml includes/footer-banner.haml includes/footer-banner.php`
end

task :projects do
  puts `cd projects && rake`
end
