const gulp = require('gulp');
const gutil = require('gulp-util');
const jspm = require('jspm');
const sass = require('gulp-sass');
const exec = require('child_process').exec;

gulp.task('default', function() {
  // place code for your default task here
});

gulp.task('jspm-bundle', function(){
	const builder = new jspm.Builder();
	builder.bundle(
		'lib/main.js - [lib/**/*] - [lib/**/*!hbs]',
		'assets/deps.js',
		{ mangle: false })
	.then(function(){
		gutil.log(gutil.colors.green('Bundling finished.'));
	})
	.catch(function(errorMessage) {
      gutil.log(gutil.colors.red(errorMessage));
      // Exit the build task on build error so that local server isn't spawned.
      throw errorMessage;
    });
});

gulp.task('jspm-build', function(){
  const builder = new jspm.Builder();
  builder.bundle(
    'lib/main.js',
    'assets/main-bundle.js',
    { minify: false, rootURL: './assets' }
  )
  .then(function(){
    gutil.log(gutil.colors.green('Build finished.'));
  })
  .catch(function(errorMessage) {
      gutil.log(gutil.colors.red(errorMessage));
      // Exit the build task on build error so that local server isn't spawned.
      throw errorMessage;
    });
});

gulp.task('jspm-prod-build', function(){
  const builder = new jspm.Builder();
  builder.bundle(
    'lib/main.js',
    'assets/main-bundle.js',
    { minify: true, rootURL: './assets' }
  )
  .then(function(){
    gutil.log(gutil.colors.green('Build finished.'));
  })
  .catch(function(errorMessage) {
      gutil.log(gutil.colors.red(errorMessage));
      // Exit the build task on build error so that local server isn't spawned.
      throw errorMessage;
    });
});


gulp.task('sass', function(){
  	gulp.src(['./sass/*'])
  	.on('error', gutil.log)
    .pipe(sass().on('error', sass.logError))
    .on('error', gutil.log)
    .pipe(gulp.dest('./assets/css'));

});

gulp.task('sass-watch', function(){
    gulp.watch('./sass/*', ['sass']);

});

