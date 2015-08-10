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
      plugin: ['css/**/*']
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

  // Install tasks.
  grunt.registerTask('install', ['bower', 'symlink']);

  // Compile tasks.
  grunt.registerTask('compile', ['clean', 'less']);

  // Default tasks.
  grunt.registerTask('default', function() {
    // Install bower components if the directory does not exist.
    if (!grunt.file.isDir('bower_components')) {
      grunt.task.run('install');
    }
    grunt.task.run('compile');
  });

};
