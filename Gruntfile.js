/*!
 * Main gruntfile for Butterfly.CMS assets
 * Homepage: https://wdmg.com.ua/
 * Author: Vyshnyvetskyy Alexsander (alex.vyshyvetskyy@gmail.com)
 * Copyright 2019 W.D.M.Group, Ukraine
 * Licensed under MIT
*/

module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            media: {
                options: {
                    sourceMap: true,
                    sourceMapName: 'assets/js/media.js.map'
                },
                files: {
                    'assets/js/media.min.js': ['assets/js/media.js']
                }
            }
        },
        sass: {
            style: {
                files: {
                    'assets/css/media.css': ['assets/scss/media.scss']
                }
            }
        },
        autoprefixer: {
            dist: {
                files: {
                    'assets/css/media.css': ['assets/css/media.css']
                }
            }
        },
        cssmin: {
            options: {
                mergeIntoShorthands: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    'assets/css/media.min.css': ['assets/css/media.css']
                }
            }
        },
        watch: {
            styles: {
                files: ['assets/scss/media.scss'],
                tasks: ['sass:style', 'cssmin'],
                options: {
                    spawn: false
                }
            },
            scripts: {
                files: ['assets/js/media.js'],
                tasks: ['uglify:media'],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify-es');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-css');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-autoprefixer');

    grunt.registerTask('default', ['uglify', 'sass', 'autoprefixer', 'cssmin', 'watch']);

};