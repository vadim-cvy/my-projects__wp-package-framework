const path = require('path');

const browserSync = require('browser-sync').create();

browserSync.init({
    proxy: 'http://' + path.basename( path.resolve( __dirname, '../../../..' ) ),
});

module.exports = browserSync;