load 'build/Rakefile'

task :default => [:home, 'includes/footer-banner.php']

task :home => 'home.php'

file 'home.php' => 'home.haml' do
  puts "Creating home.php"
  `haml home.haml home.php`
end

file 'includes/footer-banner.php' => 'includes/footer-banner.haml' do
  puts "Creating footer-banner.php"
  `haml includes/footer-banner.haml includes/footer-banner.php`
end
