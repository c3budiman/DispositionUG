<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Redirect;
use File;
use Storage;
use User;
use App\Sidebar;
use Excel;
use Datatables;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;


class authController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }
  public function logout() {
    Auth::logout();
    return redirect('/login')->with('status', 'Anda Telah berhasil logout!');
  }
  public function getRoot() {
    if (Auth::User()->roles_id == 1) {
      return view('dashboard.DashSuperAdmin');
    } elseif (Auth::User()->roles_id == 2) {
      return view('dashboard.DashAdmin');
    } else {
      return view('dashboard.DashUser');
    }
  }
  public function getMyProfile() {
    return view('myProfile');
  }
  public function getEditProfile() {
    return view('editprofile');
  }
  public function getSupport() {
    return view('support');
  }
  public function UpdateProfile(Request $request) {
    if ($request->hasFile('tes')) {
      //change the password here...
      if ($request->password2) {
        $password = $request->password;
        $passwordbaru1 = $request->password1;
        $passwordbaru2 = $request->password2;
        if ($passwordbaru1 == $passwordbaru2) {
                //ganti password here
                $request->user()->fill(['password'=>Hash::make($passwordbaru1)])->save();
          }
          else {
            return Redirect::back()->withErrors(['Password baru tidak sama', 'Kredensial anda salah']);
          }
      }

      $namafile = $request->file('tes')->getClientOriginalName();
      $ext = $request->file('tes')->getClientOriginalExtension();
      $newNamaFile = Auth::User()->email .'-id_'. Auth::User()->id . '.' .$ext;
      $lokasifileskr = '/storage/avatar/'.$newNamaFile;
      // yg paling penting cek extension, no php allowed
      if ($ext == "png" || $ext == "jpg") {
        //store
        $destinasi = public_path('storage/avatar/');
        $proses = $request->file('tes')->move($destinasi,$newNamaFile);
        //update db
        $users = Auth::user();
        $users->email = strip_tags(Input::get('email'));
        $users->nama = strip_tags(Input::get('nama'));
        $users->avatar = $lokasifileskr;
        $users->save();
        return redirect('editprofile')->with('status', 'Profil Berhasil Di Update!');
      } else {
        return Redirect::back()->withErrors(['format file salah, tidak bisa diupload']);
      }
    } else {
      //change the password here...
      if ($request->password2) {
        $password = $request->password;
        $passwordbaru1 = $request->password1;
        $passwordbaru2 = $request->password2;
        if ($passwordbaru1 == $passwordbaru2) {
                //ganti password here
                $request->user()->fill(['password'=>Hash::make($passwordbaru1)])->save();
          }
          else {
            return Redirect::back()->withErrors(['Password baru tidak sama', 'Kredensial anda salah']);
          }
      }
      //update db without poto profile
      //update db
      $users = Auth::user();
      $users->email = strip_tags(Input::get('email'));
      $users->nama = strip_tags(Input::get('nama'));
      $users->save();
      return redirect('editprofile')->with('status', 'Profil Berhasil Di Update!');
    }
  }
}
