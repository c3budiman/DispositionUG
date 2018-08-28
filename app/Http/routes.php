<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use Carbon\Carbon;

Route::get('/', 'authController@getRoot');
Route::get('daftar', 'regisController@getRegis');
Route::post('daftar', 'regisController@postRegis');
Route::get('login', ['as' => 'login', 'uses' => 'loginController@getlogin']);
Route::post('login', 'loginController@postLogin');
Route::get('logout', 'authController@logout');

Route::get('datediff',function() {
  return view('datediff');
});

Route::post('datediff',function(){
  $date1 = Input::get('date1');
  $date2 = Carbon::parse(Input::get('date2'));
  $diffis = $date2->diffInSeconds($date1);
  $diff = gmdate('H:i:s', $diffis);
  return $diff;
});


/*
|--------------------------------------------------------------------------
| Auth Roles
|--------------------------------------------------------------------------
|
| Ini route buat yang udah login aja any roles
|
*/
//@auth user
Route::get('myprofile', 'authController@getMyProfile');
Route::get('editprofile', 'authController@getEditProfile');
Route::put('editprofile', 'authController@UpdateProfile');
Route::get('support', 'authController@getSupport');

/*
|--------------------------------------------------------------------------
| Super Admin Roles
|--------------------------------------------------------------------------
|
| Ini route buat roles Super admin
|
*/
// Sidebar Part
Route::get('sidebarsettings', 'WebAdminController@getSidebarSetting');
Route::post('addsidebar', 'WebAdminController@tambahSidebarAjax');
Route::get('sidebar/json', 'WebAdminController@sidebarDataTB')->name('sidebar/json');
Route::get('sidebar/{id}/edit', 'WebAdminController@editSidebar');
Route::post('sidebar/delete', 'WebAdminController@deleteSidebar');
Route::get('submenu/json/{id}', 'WebAdminController@submenuDataTB')->name('submenu/json/{id}');
Route::put('sidebar/{id}', 'WebAdminController@updateSidebar');
Route::post('addsubmenu', 'WebAdminController@PostAddSubmenu');
Route::post('deletesubmenu', 'WebAdminController@deleteSubmenu');
Route::post('editsubmenu', 'WebAdminController@editsubmenu');
Route::get('logodanfavicon', 'WebAdminController@logoweb');
Route::get('juduldanslogan', 'WebAdminController@judul');
Route::put('juduldanslogan', 'WebAdminController@updateJudulDanSlogan');

//Berita Part
Route::get('berita', 'WebAdminController@getBerita');
Route::get('berita/json', 'WebAdminController@beritaDataTB')->name('berita/json');
Route::get('tambahBerita', 'WebAdminController@getTambahBerita');
Route::post('berita', 'WebAdminController@postBerita');
Route::post('delete/berita', 'WebAdminController@deleteBerita');
Route::get('berita/{id}/edit','WebAdminController@getBeritaUpdate');
Route::put('berita/{id}/edit', 'WebAdminController@updateBerita');

//User SPA get and ajax part :
Route::get('user/json', 'WebAdminController@userDataTB')->name('user/json');
Route::get('manageuser', 'WebAdminController@manageuser');
Route::post('auth/register','WebAdminController@register');
Route::post('auth/edituser','WebAdminController@edituser');
Route::post('auth/delete','WebAdminController@deleteuser');

//roles
Route::get('roles', 'WebAdminController@getRoles');
Route::get('roles/json', 'WebAdminController@rolesDataTB')->name('roles/json');
Route::post('roles/edit', 'WebAdminController@editRoles');
//
Route::put('logodanfavicon', 'WebAdminController@postImageLogo');



/*
|--------------------------------------------------------------------------
| Admin Roles
|--------------------------------------------------------------------------
|
| Ini route buat roles admin
|
*/
Route::get('disposition', 'AdminController@getDisposition');
Route::get('disposition/add', 'AdminController@addDisposition');
Route::post('disposition/add', 'AdminController@postDispositionAdd');
Route::get('disposition/json', 'AdminController@DispositionDataTB')->name('disposition/json');
Route::post('disposition/delete','AdminController@deleteDisposition');
Route::post('disposition/file/delete', 'AdminController@updateDeleteFile');
Route::post('disposition/edit','AdminController@update_disposition');
Route::get('disposition/{id}/edit', 'AdminController@edit_disposition');

Route::get('surat_rektor', 'AdminController@getSuratRektor');
Route::get('surat_rektor/add', 'AdminController@addSuratRektor');
Route::post('surat_rektor/add', 'AdminController@postSuratRektorAdd');
Route::get('surat_rektor/json', 'AdminController@SuratRektorDataTB')->name('surat_rektor/json');
Route::post('surat_rektor/delete', 'AdminController@DeleteSuratRektor');
Route::post('surat_rektor/file/delete', 'AdminController@updateFileSuratRektor');
Route::post('surat_rektor/edit', 'AdminController@edit_surat_rektor');
Route::get('surat_rektor/{id}/edit', 'AdminController@GETedit_surat_rektor');

Route::get('sk_rektor','AdminController@getSK_rektor');
Route::get('sk_rektor/add','AdminController@getAddSKRektor');
Route::post('sk_rektor/add', 'AdminController@PostAddSkRektor');
Route::get('sk_rektor/json', 'AdminController@sk_rektorDataTB')->name('sk_rektor/json');
Route::post('sk_rektor/file/delete', 'AdminController@DeleteFileSK_Rektor');
Route::post('sk_rektor/delete', 'AdminController@deleteSKRektor');
Route::post('sk_rektor/edit', 'AdminController@edit_sk_rektor');
Route::get('sk_rektor/{id}/edit', 'AdminController@GETedit_sk_rektor');


Route::get('purek_ii', 'AdminController@getSuratPurek');
Route::get('surat_purek/add', 'AdminController@getAddSuratPurek');
Route::post('surat_purek/add', 'AdminController@postAddSuratPurek');
