module.exports = function(grunt) {

  grunt.initConfig({
    watch: {
        lessFiles: {
            files: ["src/static/less/*.less"],
            tasks: ['less:main']
        }
    },
    copy:{

    },
    less: {
      main: {
        files: {
          "src/static/build/crudkit.css": "src/static/less/crudkit.less"
        }
      }
    },
    concat: {

    },
    clean: {

    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-clean');

  grunt.registerTask('default', ['']);

};