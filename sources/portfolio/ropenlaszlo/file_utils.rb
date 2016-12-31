# Author:: Oliver Steele
# Copyright:: Copyright (c) 2005-2006 Oliver Steele.  All rights reserved.
# License:: Ruby License.
 
module OpenLaszlo
  def self.rsync sources, target, options={}
    # TODO: could implement in this case with fileutils
    raise NotImplementedError unless cmd = which('rsync')
    args = []
    args << '--delete' if options[:delete]
    ENV['PATH'].split(':').any? {|p|File.exists?(File.join(p,'rsync'))}
    `#{cmd} -avz #{args.join(' ')} #{sources} #{target}`
  end
  
  # Returns pathname, pathname.bat, pathname.exe, or nil
  def self.which name
    extensions = ['']
    extensions += ['.exe', '.bat'] if windows?
    extensions.each do |ext|
      target = name+ext
      dir = ENV['PATH'].split(':').find {|p|File.exists?(File.join(p, target))}
      return File.join(dir, target) if dir
    end
    nil
  end
  
  def self.windows?
    RUBY_PLATFORM =~ /win/ and not RUBY_PLATFORM =~ /darwin/
  end
  
  class ::File
    # Returns true if +parent+ is the parent of +child+.
    # If the +indirect+ option is true, also returns true if
    # +parent+ is an ancestor of child.
    #
    #   File.contains?('/a', '/a/b') # -> true
    #   File.contains?('/a', '/b/c') # -> false
    #   File.contains?('/a', '/a/b/c') # -> false
    #   File.contains?('/a', '/a/b', :indirect => true) # -> true
    #   File.contains?('/a', '/a/b/c', :indirect => true) # -> true
    #   File.contains?('/a', '/a') # -> false
    #   File.contains?('/a', '/a', :indirect => true) # -> false
    def self.contains? parent, child, options={}
      parent = File.expand_path(parent)
      child = File.expand_path(child)
      File.dirname(child) == parent or
        options[:indirect] == true && child.index(parent + '/') == 0
    end
  end
end
