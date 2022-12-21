const fs              = require('fs');
const gulp            = require('gulp');
const glob            = require('glob');
const path            = require('path');
const webpack         = require('webpack');
const webpackStream   = require('webpack-stream');
const VueLoaderPlugin = require('vue-loader/dist/plugin').default;
const mergeStream     = require('merge-stream');

const pathes = require('../pathes');

// const browserSync = require('../browserSync');

function getWebPackConfig( entryPoint, outputDirPath )
{
    const config = {
        entry: entryPoint,
        output: {
            filename: outputDirPath + '/main.js'
        },
        module: {
            rules: [
                {
                    test:    /\.tsx?$/,
                    use:     'ts-loader',
                    exclude: /node_modules/,
                },
                {
                    test: /\.vue$/,
                    use:  'vue-loader',
                    exclude: /node_modules/,
                },
                {
                    test: /\.css$/,
                    use: [
                        'vue-style-loader',
                        'css-loader'
                    ]
                }
            ],
        },
        resolve: {
            extensions: [ '.tsx', '.ts', '.js', '.vue' ],

            alias: {
                vue: 'vue/dist/vue.esm-bundler.js',
                'product-data-metabox': path.resolve( __dirname, '../../view/dashboard/blocks/product-data-metabox/js/' ),
                'dashboard-helpers': path.resolve( __dirname, '../../view/dashboard/helpers/js/' ),
            }
        },
        plugins: [
            new VueLoaderPlugin({}),
        ],
        mode: 'development',
        // todo: only for dev files
        devtool: 'inline-source-map',
    };

    return config;
}

exports.compileJs = function( isFramework )
{
    const buildPath = pathes.getBuildRootPath( isFramework );

    const srcRootPath = pathes.getSrcRootPath( isFramework );

    const entryPoints =
        glob.sync( srcRootPath + '**/main.ts' )
        .concat( glob.sync( srcRootPath + '**/main.js' ) );

    const streams = [];

    // todo: make it work (dont remove css)
    // fs.rmSync( buildPath, {
    //     recursive: true,
    //     force: true
    // });

    entryPoints.forEach( entryPoint =>
    {
        const outputDirPath = path.dirname( entryPoint )
            .replace( srcRootPath, '' )
            .replace( '/js/', '' );

        const webPackConfig = getWebPackConfig( entryPoint, outputDirPath );

        const stream = gulp.src( entryPoint )
            .pipe( webpackStream( webPackConfig ), webpack )
            .pipe( gulp.dest( buildPath ) );

        streams.push( stream );
    })

    const mergedStreams = mergeStream( streams );

    // return mergedStreams.pipe( browserSync.stream() );
}