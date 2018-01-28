var gulp        = require('gulp'),
    browserSync = require('browser-sync').create(),
    sass        = require('gulp-sass'),
    sourcemaps  = require('gulp-sourcemaps'),
    notifier    = require('node-notifier');

var argv = require('yargs').argv;
const sassPaths = 'src/Template/**/*.scss';

/*
 * ---------------------------------------------------------------------------
 * DEV TASKS
 * ---------------------------------------------------------------------------
 */

// Static Server + watching scss/html files
gulp.task('dev', ['sass'], function() {
    browserSync.init({
        //server: "./app"
        proxy: argv.host || 'localhost:8080',
        notify: false,
    });
    gulp.watch(sassPaths, ['sass']);
    // gulp.watch("app/*.html").on('change', browserSync.reload);
});


// Compile sass into CSS & auto-inject into browsers
gulp.task('sass', function() {
    return gulp.src('src/Template/Layout/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest("webroot/css"))
        .pipe(browserSync.stream());
});



// default task
gulp.task('default', ['dev']);

// error handling
var onSassError = function (error) {
    console.error('\n*** Error compiling SASS ***\n\n' + error + '\n');
    notifier.notify({
        title: 'Gulp',
        message: 'Error compiling SASS',
        sound: 'Purr'
    });
    this.emit('end');
};
