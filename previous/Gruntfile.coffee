module.exports = (grunt) ->
  grunt.initConfig

    clean:
      all: ['index.html', 'index-2008.html']

    coffeelint:
      gruntfile: 'Gruntfile.coffee'
      options: max_line_length: { value: 120 }

    connect:
      server: options: base: '.'

    jade:
      compile:
        files:
          'index.html': 'index.jade'
          'index-2008.html': 'index-2008.jade'
      options:
        pretty: true
        pretty$release: false

    shell:
      rsync:
        options: {stdout:true, stderr:true}
        command: 'rsync -aiz . osteele.com:/var/www/osteele.com --exclude-from .rsync-exclude --delete --delete-excluded'

    update:
      tasks: ['jade']

    watch:
      gruntfile:
        files: 'Gruntfile.coffee'
        tasks: ['coffeelint:gruntfile']
      jade:
        files: ['*.jade', 'home/*.jade']
        tasks: 'jade'

  require('load-grunt-tasks')(grunt)

  grunt.registerTask 'deploy', ['update', 'shell:rsync']
  grunt.registerTask 'default', ['update', 'connect', 'autowatch']
