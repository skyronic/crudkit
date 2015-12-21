module.exports = function(grunt) {

  var vendorRoot = "node_modules/";
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
          cwd: vendorRoot + 'admin-lte/dist/',
          src: [
            "img/boxed-bg.png",
              "img/boxed-bg.jpg",
              "img/default-50x50.gif",
              "img/icons.png"
          ],
          dest: buildRoot
        }, {
          expand: true,
          cwd: vendorRoot + 'font-awesome/',
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
          cwd: vendorRoot + 'admin-lte/dist/',
          src: ["img/*"],
          dest: tempRoot
        }, {
          expand: true,
          cwd: vendorRoot + 'font-awesome/',
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
      css: {
        options: {
          separator: "\n\n"
        },
        files: {
          "src/static/temp/css/crudkit-libs.min.css": [
            vendorRoot + "bootstrap/dist/css/bootstrap.min.css",
            vendorRoot + "admin-lte/dist/css/AdminLTE.min.css",
            vendorRoot + "admin-lte/dist/css/skins/skin-blue.css",
            vendorRoot + "font-awesome/css/font-awesome.min.css"
          ]
        }
      },
    },
    clean: {

    },
    replace: {
      sourceMaps: {
        src: ["src/static/temp/css/crudkit-libs.min.css"],
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

  grunt.registerTask('build', ['less', 'concat:css', 'replace', 'copy:temp']);
   grunt.registerTask('release', ['build', 'concat:build_css', 'concat:build_js', 'copy:build']);

};