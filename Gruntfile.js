'use strict';
module.exports = function(grunt) {

	grunt.initConfig({
		makepot: {
			reviews: {
				options: {
					type: 'wp-plugin'
				}
			}
		},
		checktextdomain: {
			standard: {
				options:{
					force: true,
					text_domain: 'woocommerce-simple-registration',
					create_report_file: false,
					correct_domain: true,
					keywords: [
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d', 
						'esc_attr_e:1,2d', 
						'esc_attr_x:1,2c,3d', 
						'_ex:1,2c,3d',
						'_n:1,2,4d', 
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d'
					]
				},
				files: [{
					src: ['**/*.php','!node_modules/**'],
					expand: true,
				}],
			},
		},
	});

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );

	grunt.registerTask( 'pot', [ 'makepot' ] );
};
