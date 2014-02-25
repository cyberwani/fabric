module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    // set up sass task for initial sass processing
    // all scss partials/libs should be imported into style.scss
    sass: {
      dist: {
        options: {
          style: 'expanded', // we'll compress it later
          compass: true,
          loadPath: require('node-neat').includePaths
        },
        files: {
          'assets/css/build/styles.css': 'assets/sass/styles.scss'
        },
      }
    },

    // add in any missed browser prefixes
    autoprefixer: {
      options: {
        browsers: ['last 2 version']
      },
      no_dest: {
        src: 'assets/css/build/styles.css'
      }
    },

    // minify all of it
    cssmin: {
      combine: {
        files: {
          'assets/css/header_styles.css': 'assets/css/build/styles.css'
        }
      }
    },

    // lint your artisinal javascripts
    jshint: {
      beforeconcat: ['assets/js/source/main.js']
    },

    // concatenate any plugins or libraries in /vendor
    concat: {
      dist: {
        src: [
          'assets/js/vendor/*.js'
        ],
        dest: 'assets/js/source/plugins.js'
      }
    },

    // minify our two js files
    // these can be concatenated while preserving source order 
    // if you wanted to limit your HTTP requests further
    uglify: {
      dist: {
        files: {
          'assets/js/footer_plugins.min+jquery.js': 'assets/js/source/plugins.js',
          'assets/js/footer_main.min+plugins.js': 'assets/js/source/main.js'
        }
      }
    },

    // compress theme images
    // shrinks image files & returns them lighter,
    // newer and refreshed. This supports png, jpg, & gif.
    // If you have svgs too, you'll want to look
    // at something like grunt-svgmin:
    // https://github.com/sindresorhus/grunt-svgmin
    imagemin: {
      dynamic: {
        files: [{
          expand: true,
          cwd: 'assets/img/',
          src: ['**/*.{png,jpg,gif}'],
          dest: 'assets/img/'
        }]
      }
    },

    // Let's automate everything above this into one monster task!
    watch: {
      // automagically reload your page, because obviously
      // set it here once for all tasks
      options: {
        livereload: true,
      },
      // watch for saved changes in php files
      php: {
        files: ['views/*.php']
      },
      // javascript files
      scripts: {
        files: ['assets/js/source/*.js'],
        tasks: ['jshint', 'concat', 'uglify'], // order matters here
        options: {
          spawn: false
        }
      },
      // when there's a saved changes in sass files, process it
      sass: {
        options: {
          livereload: false
        },
        files: ['assets/sass/**/*.scss'],
        tasks: ['sass', 'autoprefixer', 'cssmin'], // order matters here, too!
      },

      // process image files
      images: {
        files: ['assets/img/**/*.{png,jpg,gif}', 'assets/img/*.{png,jpg,gif}'],
        tasks: ['imagemin'],
        options: {
          spawn: false,
        }
      },

      // when the processed file is overwritten, fire reload
      css: {
        files: ['assets/css/style.min.css'],
        options: {
          spawn: false
        }
      }

    },

    // livereload port
    connect: {
      server: {
        options: {
          port: 8000,
          base: './',
          livereload: true
        }
      }
    }

  });

  require('load-grunt-tasks')(grunt);

  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default Task is basically a rebuild
  grunt.registerTask('default', ['concat', 'uglify', 'sass', 'autoprefixer', 'cssmin', 'imagemin']);

  grunt.registerTask('dev', ['connect', 'watch']);

};