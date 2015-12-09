module.exports = function(grunt) {

  var vendorRoot = "src/static/vendor/";
  var buildRoot = "src/static/build/";
  var tempRoot = "src/static/temp/";
  grunt.initConfig({
    watch: {
      lessFiles: {
        files: ["src/static/less/*.less"],
        tasks: ['less:main']
      }
    },
    copy: {
      temp: {
        files: [{
          expand: true,
          cwd: vendorRoot + 'bootstrap/dist/',
          src: ["fonts/*"],
          dest: buildRoot
        }, {
          expand: true,
          cwd: vendorRoot + 'adminlte/dist/',
          src: [
            "img/boxed-bg.png",
              "img/boxed-bg.jpg",
              "img/default-50x50.gif",
              "img/icons.png"
          ],
          dest: buildRoot
        }, {
          expand: true,
          cwd: vendorRoot + 'fontawesome/',
          src: ["fonts/*"],
          dest: buildRoot
        }, ]
      },
      build: {
        files: [{
          expand: true,
          cwd: vendorRoot + 'bootstrap/dist/',
          src: ["fonts/*"],
          dest: tempRoot
        }, {
          expand: true,
          cwd: vendorRoot + 'adminlte/dist/',
          src: ["img/*"],
          dest: tempRoot
        }, {
          expand: true,
          cwd: vendorRoot + 'fontawesome/',
          src: ["fonts/*"],
          dest: tempRoot
        }]
      }
    },
    less: {
      main: {
        files: {
          "src/static/temp/crudkit.css": "src/static/less/crudkit.less"
        }
      }
    },
    concat: {
      build_css: {
        files: {
          "src/static/build/css/crudkit.min.css": [
            "src/static/temp/css/crudkit-libs.min.css",
            "src/static/temp/crudkit.css"
          ]
        }
      },
      build_js: {
        options: {
          separator: ";\n"
        },
        files: {
          "src/static/build/js/crudkit.min.js": [
            "src/static/temp/js/crudkit-libs.min.js",
            "src/static/js/app.js"
          ]
        }
      },
      css: {
        files: {
          "src/static/temp/css/crudkit-libs.min.css": [
            vendorRoot + "bootstrap/dist/css/bootstrap.min.css",
            vendorRoot + "angular-busy/dist/angular-busy.min.css",
            vendorRoot + "adminlte/dist/css/AdminLTE.min.css",
            vendorRoot + "adminlte/dist/css/skins/skin-blue.css",
            vendorRoot + "fontawesome/css/font-awesome.min.css",
            vendorRoot + "bootstrap3-dialog/dist/css/bootstrap-dialog.min.css"
          ]
        }
      },
      js: {
        options: {
          separator: ";\n"
        },
        files: {
          "src/static/temp/js/crudkit-libs.min.js": [
            vendorRoot + "jquery/dist/jquery.min.js",
            vendorRoot + "bootstrap/dist/js/bootstrap.min.js",
            vendorRoot + "lodash/lodash.min.js",
            vendorRoot + "angularjs/angular.min.js",
            vendorRoot + "angular-animate/angular-animate.min.js",
            vendorRoot + "angular-busy/dist/angular-busy.min.js",
            vendorRoot + "angular-filter/dist/angular-filter.min.js",
            vendorRoot + "bootstrap3-dialog/dist/js/bootstrap-dialog.min.js",
            vendorRoot + "jsurl/url.min.js",
            vendorRoot + "moment/min/moment.min.js",
            vendorRoot + "moment-timezone/builds/moment-timezone-with-data.min.js",
            vendorRoot + "adminlte/dist/js/app.min.js",
            "src/static/extra/ui-bootstrap-custom-0.13.0.min.js",
            "src/static/extra/ui-bootstrap-custom-tpls-0.13.0.min.js"
          ]
        }
      }
    },
    clean: {

    },
    replace: {
      sourceMaps: {
        src: ["src/static/temp/js/crudkit-libs.min.js", "src/static/temp/css/crudkit-libs.min.css"],
        overwrite: true,
        replacements: [{
          from: "sourceMappingURL",
          to: ""
        }]
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-text-replace');

  grunt.registerTask('build', ['less', 'concat:js', 'concat:css', 'replace', 'copy:temp']);
   grunt.registerTask('release', ['build', 'concat:build_css', 'concat:build_js', 'copy:build']);

};