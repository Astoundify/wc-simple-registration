'use strict';
module.exports = function(grunt) {

  grunt.initConfig({
    makepot: {
      reviews: {
        options: {
          type: 'wp-plugin'
        }
      }
    }
  });

  grunt.loadNpmTasks( 'grunt-wp-i18n' );

  grunt.registerTask( 'pot', [ 'makepot' ] );
};
