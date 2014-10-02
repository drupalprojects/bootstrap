module.exports = function (grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    clean: {
      plugin: ['dist/**/*']
    },
    concat: {
      plugin: {
        options: {
          banner: '(function ($, Drupal, window, undefined) {\n\n',
          footer: '\n})(window.jQuery, window.Drupal, window);\n'
        },
        src: ['src/js/bootstrap/**/*.js'],
        dest: 'src/js/<%= pkg.name %>.js'
      }
    },
    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      js: {
        src: [
          'package.json',
          'Gruntfile.js',
          'src/js/**/*.js'
        ]
      }
    },
    less: {
      overridesMin: {
        src: 'starterkits/less/less/overrides.less',
        dest: 'dist/css/overrides.min.css',
        options: {
          compress: true,
          cleancss: true
        }
      },
      overrides: {
        src: 'starterkits/less/less/overrides.less',
        dest: 'src/css/overrides.css'
      }
    },
    uglify: {
      options: {
        preserveComments: 'some'
      },
      bootstrap: {
        src: 'src/js/<%= pkg.name %>.js',
        dest: 'dist/js/<%= pkg.name %>.min.js'
      },
      bootstrap_admin: {
        src: 'src/js/<%= pkg.name %>.admin.js',
        dest: 'dist/js/<%= pkg.name %>.admin.min.js'
      },
      misc: {
        files: [{
          expand: true,
          cwd: 'src/js/misc',
          src: '**/*.js',
          dest: 'dist/js/misc',
          ext: '.min.js'
        }]
      },
      modules: {
        files: [{
          expand: true,
          cwd: 'src/js/modules',
          src: '**/*.js',
          dest: 'dist/js/modules',
          ext: '.min.js'
        }]
      }
    },
    watch: {
      plugin: {
        files: ['<%= jshint.js.src %>'],
        tasks: ['default']
      }
    }
  });

  // Load the grunt plugins.
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['jshint', 'clean', 'concat', 'uglify', 'less']);

};
