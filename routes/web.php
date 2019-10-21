<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Admin  routes  for user
Route::group([
    'namespace' => 'Admin',
    'prefix' => 'admin'
], function () {
    Auth::routes();
    Route::get('password', 'UserController@getPassword');
    Route::post('password', 'UserController@postPassword');
    Route::get('/', 'ResourceController@home')->name('home');
    Route::get('/home', 'ResourceController@home');
    Route::get('/dashboard', 'ResourceController@dashboard')->name('dashboard');
    Route::resource('banner', 'BannerResourceController');
    Route::post('/banner/destroyAll', 'BannerResourceController@destroyAll');


    Route::resource('system_page', 'SystemPageResourceController');
    Route::post('/system_page/destroyAll', 'SystemPageResourceController@destroyAll')->name('system_page.destroy_all');
    Route::get('/setting/company', 'SettingResourceController@company')->name('setting.company.index');
    Route::post('/setting/updateCompany', 'SettingResourceController@updateCompany');
    Route::get('/setting/station', 'SettingResourceController@station')->name('setting.station.index');
    Route::post('/setting/updateStation', 'SettingResourceController@updateStation');
    Route::get('/setting/protections', 'SettingResourceController@protections')->name('setting.protections.index');
    Route::post('/setting/updateProtections', 'SettingResourceController@updateProtections');

    Route::resource('link', 'LinkResourceController');
    Route::post('/link/destroyAll', 'LinkResourceController@destroyAll')->name('link.destroy_all');
    Route::resource('permission', 'PermissionResourceController');
    Route::resource('role', 'RoleResourceController');


    Route::group(['prefix' => 'page','as' => 'page.'], function ($router) {
        Route::resource('page', 'PageResourceController');
        Route::resource('category', 'PageCategoryResourceController');
    });
    Route::group(['prefix' => 'menu'], function ($router) {
        Route::get('index', 'MenuResourceController@index');
    });


    Route::post('/upload/{config}/{path?}', 'UploadController@upload')->where('path', '(.*)');

    Route::resource('admin_user', 'AdminUserResourceController');
    Route::post('/admin_user/destroyAll', 'AdminUserResourceController@destroyAll')->name('admin_user.destroy_all');
    Route::resource('permission', 'PermissionResourceController');
    Route::post('/permission/destroyAll', 'PermissionResourceController@destroyAll')->name('permission.destroy_all');
    Route::resource('role', 'RoleResourceController');
    Route::post('/role/destroyAll', 'RoleResourceController@destroyAll')->name('role.destroy_all');
    Route::get('logout', 'Auth\LoginController@logout');

    Route::resource('user', 'UserResourceController');
    Route::post('/user/destroyAll', 'UserResourceController@destroyAll')->name('user.destroy_all');

    Route::resource('region', 'RegionResourceController');
    Route::post('/region/destroyAll', 'RegionResourceController@destroyAll')->name('region.destroy_all');

    Route::resource('region_user', 'RegionUserResourceController');
    Route::post('/region_user/destroyAll', 'RegionUserResourceController@destroyAll')->name('region_user.destroy_all');
    
    Route::resource('shop', 'ShopResourceController');
    Route::post('/shop/destroyAll', 'ShopResourceController@destroyAll')->name('shop.destroy_all');
    Route::get('shop_import', 'ShopResourceController@import')->name('shop.import');
    Route::get('shop_export', 'ShopResourceController@export')->name('shop.export');

    Route::post('/shop_submit_import', 'ShopResourceController@submitImport')->name('shop.submit_import');
});

Route::group([
    'namespace' => 'Region',
    'prefix' => 'region',
    'as' => 'region.',
], function () {
    Auth::routes();
    Route::get('logout', 'Auth\LoginController@logout');
    Route::get('/', 'ResourceController@home')->name('home');
    Route::get('password', 'RegionUserController@getPassword');
    Route::post('password', 'RegionUserController@postPassword');

    Route::resource('shop', 'ShopResourceController');
    Route::post('/shop/destroyAll', 'ShopResourceController@destroyAll')->name('shop.destroy_all');
    Route::get('shop_export', 'ShopResourceController@export')->name('shop.export');

    Route::resource('user', 'UserResourceController');
    Route::post('/user/destroyAll', 'UserResourceController@destroyAll')->name('user.destroy_all');

    Route::resource('permission', 'PermissionResourceController');
    Route::post('/permission/destroyAll', 'PermissionResourceController@destroyAll')->name('permission.destroy_all');
    Route::resource('role', 'RoleResourceController');
    Route::post('/role/destroyAll', 'RoleResourceController@destroyAll')->name('role.destroy_all');

    //Route::post('/upload/{config}/{path?}', 'UploadController@upload')->where('path', '(.*)');
});

//Route::get('
///{slug}.html', 'PagePublicController@getPage');
/*
Route::group(
    [
        'prefix' => trans_setlocale() . '/admin/menu',
    ], function () {
    Route::post('menu/{id}/tree', 'MenuResourceController@tree');
    Route::get('menu/{id}/test', 'MenuResourceController@test');
    Route::get('menu/{id}/nested', 'MenuResourceController@nested');

    Route::resource('menu', 'MenuResourceController');
   // Route::resource('submenu', 'SubMenuResourceController');
});
*/

/*
Route::group([
    'namespace' => 'Pc',
    'as' => 'pc.',
], function () {
    Auth::routes();
    Route::get('/user/login','Auth\LoginController@showLoginForm');
    Route::get('/','HomeController@home')->name('home');


    Route::get('email-verification/index','Auth\EmailVerificationController@getVerificationIndex')->name('email-verification.index');
    Route::get('email-verification/error','Auth\EmailVerificationController@getVerificationError')->name('email-verification.error');
    Route::get('email-verification/check/{token}', 'Auth\EmailVerificationController@getVerification')->name('email-verification.check');
    Route::get('email-verification-required', 'Auth\EmailVerificationController@required')->name('email-verification.required');

    Route::get('verify/send', 'Auth\LoginController@sendVerification');
    Route::get('verify/{code?}', 'Auth\LoginController@verify');

});
*/