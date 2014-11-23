var deploy = require('gulp-gh-pages');
var es = require('event-stream');
var gulp = require('gulp');
var livereload = require('gulp-livereload');
var replace = require('gulp-replace');
var shell = require('gulp-shell');
var spawn = require('child_process').spawn;

var handleError = function (err) {
    console.log(err.name, ' in ', err.plugin, ': ', err.message);
    this.emit('end');
};

// Watch
gulp.task('watch', [], function () {
    gulp.watch(['config/**/*.yml', 'config/*.php', 'src/MediaMine/**/*.php'], function () {

        console.log('Reload PHP workers');
        gulp.src('').pipe(shell('sudo docker-enter mediamine-nginx-container pkill -9 -f "consumer"'));
    });
});

gulp.task('default', [], function () {
});

gulp.task('build', [], function () {
});
