<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Sidebar;
use App\submenu;
use Excel;
use Datatables;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\SettingSitus;
use Storage;
use App\berita;
use Auth;
use App\Events\beritaEvent;

class WebAdminController extends Controller
{
    public function getRoleAdmin() {
      $rolesyangberhak = DB::table('roles')->where('id','=','1')->first()->namaRule;
      return $rolesyangberhak;
    }

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('rule:'.$this->getRoleAdmin().',nothingelse');
    }


    // Memulai Seksi Sidebar
    // .
    // .
    public function getSidebarSetting() {
      return view('sidebar.index');
    }

    public function sidebarDataTB() {
      return Datatables::of(Sidebar::query())
            ->addColumn('kepunyaan_roles', function($datatb) {
              return DB::table('roles')->where('id','=',$datatb->kepunyaan)->first()->namaRule;
            })
            ->addColumn('action', function ($datatb) {
                return
                 '<a style="margin-left:5px" href="/sidebar/'.$datatb->id.'/edit" class="btn btn-xs btn-info"><i class="fa fa-edit"></i> Ubah</a>'
                 .'<div style="padding-top:10px"></div>'
                .'<button data-id="'.$datatb->id.'" data-nama="'.$datatb->nama.'" class="delete-modal btn btn-xs btn-danger" type="submit"><i class="fa fa-trash"></i> Delete</button>';
            })
            ->make(true);
    }

    public function addsidebar() {
      return view('sidebar.tambahsidebar');
    }

    public function editSidebar($id) {
      $sidebar = Sidebar::find($id);
      if (!$sidebar) {
        abort(404);
      }
      return view('sidebar.editsidebar', ['sidebar'=>$sidebar, 'id'=>$id]);
    }

    public function tambahSidebarAjax(Request $request, Sidebar $sidebar) {
        $this->validate($request, [
          'nama'      => 'required',
          'class_css' => 'required',
          'link'      => 'required',
        ]);
        $sidebar = new Sidebar();
        $sidebar->nama = strip_tags($request->nama);
        $sidebar->kepunyaan = strip_tags($request->roles_id);
        $sidebar->class_css = strip_tags($request->class_css);
        $sidebar->link = strip_tags($request->link);
        $sidebar->save();

        $response = array("success"=>"Sidebar Added");
        return response()->json($response,201);
    }

    public function deleteSidebar(Request $request) {
      $this->validate($request, [
        'id'      => 'required',
      ]);
      $sidebar = Sidebar::find($request->id);
      $sidebar->delete();

      $response = array("success"=>"Sidebar Deleted");
      return response()->json($response,200);
    }

    public function postDataSidebar($sidebar) {
      $sidebar->nama = strip_tags(Input::get('nama'));
      $sidebar->kepunyaan = strip_tags(Input::get('roles_id'));
      $sidebar->class_css = strip_tags(Input::get('class_css'));
      $sidebar->link = strip_tags(Input::get('link'));
      $sidebar->save();
    }

    public function updateSidebar($id) {
      $sidebar = Sidebar::find($id);
      $this->postDataSidebar($sidebar);
      return redirect('/sidebarsettings')->with('status', 'Sidebar Berhasil Di Update!');
    }

    public function getAddSubMenu($id) {
      $sidebar = Sidebar::find($id);
      return view('sidebar.submenuadd', ['sidebar'=>$sidebar, 'id'=>$id]);
    }

    public function PostAddSubmenu(Request $request) {
      $this->validate($request, [
        'nama'      => 'required',
        'link'      => 'required',
      ]);
      $submenu = new submenu();
      $submenu->kepunyaan = $request->id;
      $submenu->nama = strip_tags($request->nama);
      $submenu->link = strip_tags($request->link);
      $submenu->save();
      $response = array("success"=>"Submenu Added");
      return response()->json($response,201);
    }

    public function editsubmenu(Request $request) {
      $this->validate($request, [
        'id'      => 'required',
        'nama'      => 'required',
        'link'      => 'required',
      ]);
      $submenu = submenu::find($request->id);
      $submenu->nama = strip_tags($request->nama);
      $submenu->link = strip_tags($request->link);
      $submenu->save();
      $response = array("success"=>"Submenu Edited");
      return response()->json($response,200);
    }

    public function deleteSubmenu(Request $request) {
      $this->validate($request, [
        'id'      => 'required',
      ]);

      $submenu = submenu::find($request->id);
      $submenu->delete();

      $response = array("success"=>"Submenu Deleted");
      return response()->json($response,200);
    }

    public function submenuDataTB($id) {
      $submenu = DB::table('submenu')->where('kepunyaan', $id);
        return Datatables::of($submenu)
            ->addColumn('action', function ($datatb) {
                return
                '<button data-id="'.$datatb->id.'" data-nama="'.$datatb->nama.'" data-kepunyaan="'.$datatb->kepunyaan.'" data-link="'.$datatb->link.'"  class="edit-modal btn btn-xs btn-info" type="submit"><i class="fa fa-edit"></i> Edit</button>'
                .'<div style="margin-top:10px"></div>'
                .'<button data-id="'.$datatb->id.'" data-nama="'.$datatb->nama.'" class="delete-modal btn btn-xs btn-danger" type="submit"><i class="fa fa-trash"></i> Delete</button>';
            })
            ->make(true);
    }
    // Akhir Seksi Sidebar


    //Memulai Seksi pengguna
    // .
    // .
    public function userDataTB() {
      return Datatables::of(User::query())
            ->addColumn('action', function ($datatb) {
                return
                 '<button data-id="'.$datatb->id.'" data-nama="'.$datatb->nama.'" data-roles_id="'.$datatb->roles_id.'" data-email="'.$datatb->email.'" data-avatar="'.$datatb->avatar.'"  class="edit-modal btn btn-xs btn-info" type="submit"><i class="fa fa-edit"></i> Edit</button>'
                 .'<div style="padding-top:10px"></div>'
                .'<button data-id="'.$datatb->id.'" data-nama="'.$datatb->nama.'" data-roles_id="'.$datatb->roles_id.'" data-email="'.$datatb->email.'" data-avatar="'.$datatb->avatar.'"  class="delete-modal btn btn-xs btn-danger" type="submit"><i class="fa fa-trash"></i> Delete</button>';
            })
            ->make(true);
    }

    public function manageuser() {
      return view('user.userIndex');
    }

    public function register(Request $request, User $user){
      //validasi request
      $this->validate($request, [
        'nama'      => 'required',
        'email'     => 'required|email|unique:users',
        'password'  => 'required|min:6',
      ]);

      //mass asignment ke database
      $createuser = $user->create([
        'nama'      => $request->nama,
        'email'     => $request->email,
        'avatar'     => $request->avatar,
        'roles_id'     => $request->roles_id,
        'password'  => bcrypt($request->password),
      ]);

      //endpoint api berdasarkan hasil dari response, jika berjalan lancar :
      // 201, artinya konten berhasil dibuat, 200 success, 404 not found, 500 server error etc etc...
      return response()->json(['sukses' => 'User Berhasil Ditambahkan !'], 201);
    }

    public function edituser(Request $request, User $user){
      //validasi request
      $this->validate($request, [
        'nama'      => 'required',
        'email'     => 'required|email',
      ]);

      $user = User::find($request->id);
      $user->email = strip_tags($request->email);
      $user->nama = strip_tags($request->nama);
      $user->avatar = strip_tags($request->avatar);
      $user->roles_id = $request->roles_id;
      $user->save();


      //membuat response array, untuk di tampilkan menjadi json nantinya
      $response = array("success"=>"User Modified");
      return response()->json($response,201);
    }

    public function deleteuser(Request $request, User $user){
      $user = User::find($request->id);
      $user->delete();
      //membuat response array, untuk di tampilkan menjadi json nantinya
      $response = array("success"=>"User Deleted");

      return response()->json($response,200);
    }
    //Akhir Seksi Pengguna



    //Mulai Seksi Roles
    // .
    // .
    public function getRoles() {
      return view('roles.index');
    }

    public function rolesDataTB() {
      return Datatables::of(Role::query())
            ->addColumn('action', function ($datatb) {
                return
                '<button data-id="'.$datatb->id.'" data-namaRule="'.$datatb->namaRule.'"  class="edit-modal btn btn-xs btn-info" type="submit"><i class="fa fa-edit"></i> Ubah</button>';
            })
            ->make(true);
    }

    public function UploadImage() {

    }

    public function editRoles(Request $request, Role $role) {
      //validasi request
      $this->validate($request, [
        'namaRule'      => 'required',
      ]);

      $roles = Role::find($request->id);
      $roles->namaRule = strip_tags($request->namaRule);
      $roles->save();

      //membuat response array, untuk di tampilkan menjadi json nantinya
      $response = array("success"=>"User Modified");
      return response()->json($response,200);
    }
    //Akhir Seksi Roles

    //Seksi Logo
    public function logoweb(){
      return view('situs.logo');
    }


    public function postImageLogo(Request $request) {
      if ($request->hasFile('tes')) {
        $namafile = $request->file('tes')->getClientOriginalName();
        $ext = $request->file('tes')->getClientOriginalExtension();
        $lokasifileskr = '/images/'.$namafile;

        // yg paling penting cek extension, no php allowed
        if ($ext == "png" || $ext == "jpg") {
          //store
          $destinasi = public_path('images/');
          // dd($destinasi);
          $proses = $request->file('tes')->move($destinasi,$namafile);
          //delete foto sebelumnya jika ada....
          $logo = DB::table('setting_situses')->where('id','=','1')->first()->logo;
          // if ($logo != null || $logo != "") {
          //   $file_lama = public_path($logo);
          //   unlink($file_lama);
          // }

          //update db
          $logo = SettingSitus::find('1');
          $logo->logo = $lokasifileskr;

          $logo->save();
          return redirect('logodanfavicon')->with('status', 'Logo Berhasil Di Update!');
        } else {
          return Redirect::back()->withErrors(['format file salah, tidak bisa diupload']);
        }
      } elseif ($request->hasFile('tes2')) {
        $namafile = $request->file('tes2')->getClientOriginalName();
        $ext = $request->file('tes2')->getClientOriginalExtension();
        $lokasifileskr = '/images/'.$namafile;
        // dd($namafile);
        // yg paling penting cek extension, no php allowed
        if ($ext == "png" || $ext == "jpg") {
          //store
          $destinasi = public_path('images/');
           // dd($destinasi);
          $proses = $request->file('tes2')->move($destinasi,$namafile);
          //delete foto sebelumnya jika ada....
          $favicon = DB::table('setting_situses')->where('id','=','1')->first()->favicon;
          if ($favicon != null || $favicon != "") {
            $file_lama = public_path($favicon);
            unlink($file_lama);
          }
          //update db
          $favicon = SettingSitus::find('1');
          $favicon->favicon = $lokasifileskr;
          $favicon->save();
          return redirect('logodanfavicon')->with('status', 'Favicon Berhasil Di Update!');
        } else {
          return Redirect::back()->withErrors(['format file salah, tidak bisa diupload']);
        }
      }
    }

    public function judul(){
      return view('situs.judul');
    }

    public function updateJudulDanSlogan(Request $request) {
      $settingsitus = SettingSitus::find('1');
      $settingsitus->namaSitus = strip_tags($request->judul);
      $settingsitus->slogan = strip_tags($request->slogan);
      $settingsitus->save();
      return redirect('juduldanslogan')->with('status', 'Judul dan/atau Slogan Berhasil Di Update!');
    }

    public function getBerita() {
      return view('berita.aturBerita');
    }

    public function beritaDataTB() {
      $query = berita::with('authornya');
      return Datatables::of($query)
            ->addColumn('action', function ($datatb) {
                return
                 '<a target="_blank" style="margin-left:5px" href="/berita/'.$datatb->id_berita.'" class="btn btn-xs btn-warning"><i class="fa fa-eye"></i> Lihat</a>'
                 .'<div style="padding-top:10px"></div>'
                 .'<a style="margin-left:5px" href="/berita/'.$datatb->id_berita.'/edit" class="btn btn-xs btn-info"><i class="fa fa-edit"></i> Ubah</a>'
                 .'<div style="padding-top:10px"></div>'
                 .'<button style="margin-left:5px" data-id="'.$datatb->id_berita.'" data-nama="'.$datatb->judul.'"  class="delete-modal btn btn-xs btn-danger" type="submit"><i class="fa fa-trash"></i> Delete</button>';
            })
            ->make(true);
    }

    public function getTambahBerita() {
      return view('berita.tambahBerita');
    }

    public function getBeritaUpdate($id) {
      $berita = berita::with('authornya')->where('id_berita','=',$id)->first();
      return view('berita.editBerita',['berita'=>$berita]);
    }

    public function updateBerita(Request $request, $id) {
      $berita = berita::find($id);
      $berita->author = Auth::User()->id;
      $berita->uri = '/berita/'.str_slug(strip_tags($request->judul));
      $berita->judul = strip_tags($request->judul);
      $berita->content = $request->content;
      $berita->save();

      $berita2 = berita::with('authornya.role')->latest()->get();
      $berita2 = response()->json($berita2);
      event(new beritaEvent($berita2));
      return redirect('berita')->with('status', 'Berita berhasil ditambahkan!');
    }

    public function postBerita(Request $request) {
      $berita = new berita();
      $berita->author = Auth::User()->id;
      $berita->uri = '/berita/'.str_slug(strip_tags($request->judul));
      $berita->judul = strip_tags($request->judul);
      $berita->content = $request->content;
      $berita->save();

      $berita2 = berita::with('authornya.role')->latest()->get();
      $berita2 = response()->json($berita2);
      event(new beritaEvent($berita2));
      return redirect('berita')->with('status', 'Berita berhasil ditambahkan!');
    }

    public function deleteBerita(Request $request) {
      $berita = berita::find($request->id_berita);
      $berita->delete();

      $berita2 = berita::with('authornya.role')->latest()->get();
      $berita2 = response()->json($berita2);
      event(new beritaEvent($berita2));

      $response = array("success"=>"Berita Deleted");
      return response()->json($response,200);
    }

}
