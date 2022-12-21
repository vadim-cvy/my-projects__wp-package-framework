const gulp = require('gulp');

const cssCompiler = require('./../compilers/cssCompiler');

gulp.task( 'watchCss', () =>
{
    gulp.watch( 'view/**/*.scss', cssCompiler.compileCss.bind( null, false ) );

    gulp.watch( 'framework/view/**/*.scss', cssCompiler.compileCss.bind( null, true ) );
});