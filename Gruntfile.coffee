module.exports = (grunt) ->
  # Project configuration.
  grunt.initConfig(
    pkg: grunt.file.readJSON('package.json')
    requirejs:
      compile:
        options:
          baseUrl: 'src/javascript/vendor'
          mainConfigFile: "src/javascript/app.js"
          useStrict: false

    watch:
      scripts:
        files: ['**/*.js', 'src/app.js']
        options:
          interrupt: true
          spawn: false
        tasks: ['requirejs']
      sass:
        files: '**/*.scss'
        tasks: ['sass:dev']
    sass:
      dev:
        options:
          style: 'expanded'
          compass: false
        files:
          'css/style-v0.0.1.css': 'src/stylesheet/main.scss'
      dist:
        options:
          style: 'compressed'
          compass: false
        files:
          'style.min.css': 'src/stylesheet/main.scss'
  )

  # Load the plugin that provides the "requirejs" task.
  grunt.loadNpmTasks('grunt-requirejs')
  grunt.loadNpmTasks('grunt-contrib-jshint')
  grunt.loadNpmTasks('grunt-contrib-sass')
  grunt.loadNpmTasks('grunt-contrib-watch')

  # Default task(s).
  grunt.registerTask('default', ['requirejs', 'sass:dev', 'watch'])
  grunt.registerTask('build', ['requirejs', 'sass:dev'])