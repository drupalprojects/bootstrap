module.exports = function (grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    bower: {
      install: {
        options: {
          copy: false
        }
      }
    },
    clean: {
      css: ['css/**/*']
    },
    githooks: {
      install: {
        options: {
          template: '.githooks.js.hbs'
        },
        // Change to something else once the {{ hook }} variable can be used.
        // @see https://github.com/wecodemore/grunt-githooks/pull/40
        'pre-commit': 'pre-commit',
        'post-merge': 'post-merge',
        'post-checkout': 'post-checkout'
      }
    },
    less: {
      overrides: {
        src: 'starterkits/less/less/overrides.less',
        dest: 'css/overrides.css',
        options: {
          cleancss: true,
          compress: true
        }
      }
    },
    symlink: {
      options: {
        overwrite: true
      },
      less: {
        src: 'bower_components/bootstrap',
        dest: 'starterkits/less/bootstrap'
      }
    },
    watch: {
      plugin: {
        files: ['starterkits/less/**/*.less'],
        tasks: ['compile']
      }
    }
  });

  // Load the grunt plugins.
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-symlink');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-bower-task');
  grunt.loadNpmTasks('grunt-githooks');

  // Install tasks.
  grunt.registerTask('install', 'Installs the grunt project. NOTE: Only needs to be ran once and should have be done automatically via npm postinstall!', function () {
    // Install bower and setup symlinks.
    grunt.task.run(['githooks', 'bower', 'symlink']);

    // Ensure there are no files in the vendor paths that may conflict with
    // Drupal. @see https://www.drupal.org/node/2329453
    var files = grunt.file.expand(['node_modules/**/*.info', 'bower_components/**/*.info']);
    files.forEach(function(file) {
      grunt.file.delete(file, { force: true });
      grunt.log.verbose('Removed conflicting Drupal file "' + file.dest + '".');
    });
  });

  // Compile tasks.
  grunt.registerTask('compile', 'Compiles the base theme overrides CSS.', ['clean', 'less']);

  // Default tasks.
  grunt.registerTask('default', ['compile']);

};
