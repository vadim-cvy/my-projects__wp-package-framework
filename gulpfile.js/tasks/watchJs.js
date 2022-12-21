const gulp = require('gulp');

const pathes = require('../pathes');

const jsCompiler = require('./../compilers/jsCompiler');

gulp.task( 'watchJs', () =>
{
    gulp.watch(
        [
            'view/**/*.ts',
            'view/**/*.js',
            'view/**/*.vue',
        ],
        jsCompiler.compileJs.bind( null, false )
    );

    gulp.watch(
        [
            'framework/view/**/*.ts',
            'framework/view/**/*.js',
            'framework/view/**/*.vue',
        ],
        jsCompiler.compileJs.bind( null, true )
    );
});