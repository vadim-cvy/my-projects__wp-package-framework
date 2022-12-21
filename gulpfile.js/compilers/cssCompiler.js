const fs           = require('fs');
const gulp         = require('gulp');
const sass         = require('gulp-sass')(require('sass'));
var sassGlob       = require('gulp-sass-glob');
const autoprefixer = require('gulp-autoprefixer');
const glob         = require('glob');
const path         = require('path');

const pathes = require('../pathes');

// const browserSync = require('../browserSync');

module.exports.compileCss = function( isFramework )
{
    const buildPath = pathes.getBuildRootPath( isFramework );

    // todo: make it work (dont remove js)
    // fs.rmSync( buildPath, {
    //     recursive: true,
    //     force: true,
    // });

    return gulp.src( pathes.getSrcRootPath( isFramework ) + '**/main.scss' )
        .pipe(sassGlob())
        .pipe(sass({
            outputStyle: 'compressed',
        }).on('error', sass.logError))
        .pipe(autoprefixer({overrideBrowserslist: [
            'ie >= 10',
            'ie_mob >= 10',
            'ff >= 30',
            'chrome >= 34',
            'Safari >= 7',
            'Opera >= 23',
            'ios >= 7',
            'Android >= 4.4',
            'bb >= 10'
        ]}))
        .pipe(gulp.dest( buildPath ))
        // .pipe(browserSync.stream())
}