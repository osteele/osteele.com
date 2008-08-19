desc "open the public site for login via sitekey"
task :open do
  sh "ssh osteele@osteele.com 'echo > osteele.com/sitekey'"
  url = "http://osteele.com/admin?user=oliver&sitekey=true"
  puts url
  sh "open '#{url}'"
end

desc "close the public site for login via sitekey"
task :close do
  sh "ssh osteele@osteele.com 'rm osteele.com/sitekey'"
end
