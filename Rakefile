load 'build/Rakefile'

task :home => 'home.php'

file 'home.php' => 'home.haml' do
  puts "Creating home.php"
  `haml home.haml home.php`
end
