const gulp = require('gulp');

require('./tasks/watchCss');
require('./tasks/watchJs');
require('./tasks/watchPhp');

gulp.task( 'watch', gulp.parallel( 'watchPhp', 'watchCss', 'watchJs' ) );
