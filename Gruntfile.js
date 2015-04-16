module.exports = function(grunt) {

  grunt.initConfig({
    watch: {
    },
    copy:{

    },
    less: {
      main: {
        files: {
          "src/static/build/crudkit.css": "src/static/less/crudkit.less"
        }
      }
    }

  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-less');

  grunt.registerTask('default', ['']);

};