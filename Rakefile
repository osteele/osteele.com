desc "open the public site for login via sitekey"
task :open do
  sh "ssh osteele@osteele.com 'echo > osteele.com/sitekey'"
  puts "http://osteele.com/admin?user=oliver&sitekey=true"
end

desc "close the public site for login via sitekey"
task :close do
  sh "ssh osteele@osteele.com 'rm osteele.com/sitekey'"
end
