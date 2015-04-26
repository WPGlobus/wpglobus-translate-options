/*jslint node: true */
module.exports = function (grunt) {

    'use strict';
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        wp_readme_to_markdown: {
            main: {
                files: {
                    'readme.md': 'readme.txt'
                },
            },
        },

        /**
         * @link https://github.com/gruntjs/grunt-contrib-uglify
         */
        uglify: {
            main: {
                files: [{
                    expand: true,
                    src: ['*.js', '!*.min.js', '!Gruntfile.js'],
                    ext: '.min.js'
                }]
            }
        }

    });

    grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('dist', ['wp_readme_to_markdown', 'uglify']);

};
