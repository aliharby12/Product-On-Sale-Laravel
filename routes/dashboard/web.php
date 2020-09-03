<?php


Route::group(
[
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
], function(){

  Route::prefix('dashboard')->middleware(['auth'])->name('dashboard.')->group(function(){

      Route::get('/index', 'DashboardController@index')->name('index');

			// users routes
			Route::resource('users', 'UserController')->except(['show']);

			// categories routes
			Route::resource('categories', 'CategoryController')->except(['show']);

  });
});
