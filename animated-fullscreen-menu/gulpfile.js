var gulp   			= require('gulp');
var sass   			= require('gulp-sass');
var clean  			= require('gulp-clean-css');
var uglify 			= require('gulp-uglify');

gulp.task('sass', function(){
	return gulp.src('frontend/src/sass/*.scss')
	.pipe(sass())
	.pipe(clean())
	.pipe(gulp.dest('frontend/css'))
});

gulp.task('scripts', function(){
	return gulp.src('frontend/src/js/*.js')
	.pipe(uglify())
	.pipe(gulp.dest('frontend/js'))
});

gulp.task('watch', function(){
	gulp.watch('frontend/src/sass/*.scss', gulp.series('sass') ); 
	gulp.watch('frontend/src/js/*.js', gulp.series('scripts') ); 
});

