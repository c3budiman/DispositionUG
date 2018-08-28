<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Redirect;
use Input;
use App\surat_masuk;
use App\surat_rektor;
use App\sk_rektor;
use Mail;
use Datatables;
use Carbon\Carbon;

class AdminController extends Controller
{

  public function getRoleAdmin() {
    $rolesyangberhak = DB::table('roles')->where('id','=','2')->first()->namaRule;
    return $rolesyangberhak;
  }

  public function __construct()
  {
      $this->middleware('auth');
      $this->middleware('rule:'.$this->getRoleAdmin().',nothingelse');
  }

  public function getDisposition() {
    return view('disposition.index');
  }

  public function addDisposition() {
    return view('disposition.add');
  }

  private $filenya;

  public function postDispositionAdd (Request $request) {
    if ($request->submit == 'saveandsend') {
      $send_email = true;
    } else {
      $send_email = false;
    }

    if ($_FILES['file']) {
      $lokasi_buat_db = '';
      $arr_lokasi_file = array();
      $file_count = count($_FILES['file']['name']);
      for ($i=0; $i<$file_count; $i++) {
        $tmpFilePath = $_FILES['file']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            //get the extension
            $ext = explode('.', basename($_FILES['file']['name'][$i]));
            $ext = end($ext);
            //nama file :
            $newNamaFile = md5($_FILES['file']['name'][$i].rand()).$i.'.'.$ext;
            //buat db :
            $lokasifileskr = '/storage/disposisi/'.$newNamaFile;

            // yg paling penting cek extension, no php allowed
            if ($ext == "png" || $ext == "PNG" || $ext == "jpg" || $ext == "pdf" || $ext == "PDF" || $ext == "JPG" || $ext == "jpeg" || $ext == "JPEG" ||
                $ext == "docx" || $ext == "DOCX" || $ext == "doc" || $ext == "DOC" || $ext == "xls" || $ext == "XLS" || $ext == "XLSX" || $ext == "xlsx") {
              //store
              $destinasi = public_path('storage/disposisi/');
              $proses = move_uploaded_file($tmpFilePath, $destinasi.$newNamaFile);
              array_push($arr_lokasi_file,$destinasi.$newNamaFile);
              $lokasi_buat_db .= $lokasifileskr."/new/";
            } else {
              return Redirect::back()->withErrors(['format file salah, tidak bisa diupload']);
            }
          }
      }

      //stor db and email here :
      if ($request->perihal == 0) {
        $perihal = 'Undangan';
      } else {
        $perihal = 'Pemberitahuan';
      }

      if ($request->email && $send_email) {
        //email :
        $sender = "UG Disposition | ".$perihal;
        $EmailSender = $request->email;
        $pesan = $request->desc;
        Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
        function ($message) use ($arr_lokasi_file)
        {
            if (Input::get('perihal') == 0) {
              $perihal = 'Undangan';
            } else {
              $perihal = 'Pemberitahuan';
            }
            $sender = "UG Disposition | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);
            $size = sizeOf($arr_lokasi_file);
            for ($i=0; $i < $size; $i++) {
                $message->attach($arr_lokasi_file[$i]);
            }
        });
      }

      //stor ke db :
      $tabel = new surat_masuk;
      $tabel->author = Auth::User()->id;
      $tabel->instansi_pengirim = $request->instansi;
      $tabel->lokasi = $lokasi_buat_db;
      $tabel->jumlah_file = $file_count;
      $tabel->perihal = $request->perihal;
      $tabel->email = $request->email;
      $tabel->tgl_disposisi = $request->date;
      $tabel->deskripsi = $request->desc;
      $tabel->save();

      return redirect('disposition')->with('status', 'Data Berhasil Di Simpan');
    } else {
        //kalo ga ada file yg di upload :
        //email :
        if ($request->perihal == 0) {
          $perihal = 'Undangan';
        } else {
          $perihal = 'Pemberitahuan';
        }

        //di kirim ke email ga?
        if ($request->email && $send_email) {
          //email :
          $sender = "UG Disposition | ".$perihal;
          $EmailSender = $request->email;
          $pesan = $request->desc;
          Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
          function ($message) use ($arr_lokasi_file)
          {
              if (Input::get('perihal') == 0) {
                $perihal = 'Undangan';
              } else {
                $perihal = 'Pemberitahuan';
              }
              $sender = "UG Disposition | ".$perihal;
              $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
              $message->to(Input::get('email'));
              $message->subject($sender);
          });
        }
        //stor ke db :
        $tabel = new surat_masuk;
        $tabel->author = Auth::User()->id;
        $tabel->instansi_pengirim = $request->instansi;
        $tabel->perihal = $request->perihal;
        $tabel->email = $request->email;
        $tabel->tgl_disposisi = $request->date;
        $tabel->deskripsi = $request->desc;
        $tabel->save();

        return redirect('disposition')->with('status', 'Data Berhasil Di Simpan');
    }
  }

  public function DispositionDataTB() {
    return Datatables::of(surat_masuk::query())
          ->addColumn('action', function ($datatb) {
              return
               '<a style="margin-left:5px" href="/disposition/'.$datatb->id.'/edit" class="btn btn-xs btn-info"><i class="fa fa-edit"></i> Ubah</a>'
               .'<div style="padding-top:10px"></div>'
              .'<button data-id="'.$datatb->id.'" data-nama="'.$datatb->deskripsi.'" class="delete-modal btn btn-xs btn-danger" type="submit"><i class="fa fa-trash"></i> Delete</button>';
          })
          ->editColumn('author', function ($datatb) {
                return $datatb->author ? DB::table('users')->where('id','=',$datatb->author)->first()->nama : '';
          })
          ->editColumn('perihal', function ($datatb) {
                return $datatb->perihal == '0' ? 'Undangan' : 'Pemberitahuan';
          })
          ->editColumn('tgl_disposisi', function ($datatb) {
              return $datatb->tgl_disposisi ? with(new Carbon($datatb->tgl_disposisi))->format('d/m/Y') : '';
          })
          ->editColumn('updated_at', function ($datatb) {
              return $datatb->updated_at ? $datatb->updated_at->diffForHumans() : '';
          })
          ->addIndexColumn()
          ->make(true);
  }

  public function deleteDisposition(Request $request) {
    $this->validate($request, [
      'id'      => 'required',
    ]);
    $data = surat_masuk::find($request->id);
    $lokasi_file = $data->lokasi;
    $arr_lokasi_file = explode('/new/', $lokasi_file);
    for ($i=0; $i < $data->jumlah_file; $i++) {
      //dd(public_path($arr_lokasi_file[$i]));
      unlink(public_path($arr_lokasi_file[$i]));
    };
    $data->delete();

    $response = array("success"=>"Surat Masuk Deleted");
    return response()->json($response,200);
  }

  public function edit_disposition($id) {
    $data = surat_masuk::find($id);
    return view('disposition.edit',['data'=>$data]);
  }

  public function updateDeleteFile(Request $request) {
    $this->validate($request, [
      'id'      => 'required',
      'path'    => 'required',
    ]);
    $data = surat_masuk::find($request->id);
    $path = $request->path."/new/";
    $new_path = str_replace($path,"",$data->lokasi);

    //delete the data.. and update db :
    unlink(public_path($request->path));
    $data->lokasi = $new_path;
    $data->jumlah_file = $data->jumlah_file-1;
    $data->save();

    $response = array("success"=>"File Deleted");
    return response()->json($response,200);
  }

  public function update_disposition(Request $request) {
    //get the current data
    $tabel = surat_masuk::find($request->id);

    //for send email
    if ($request->submit == 'saveandsend') {
      $send_email = true;
    } else {
      $send_email = false;
    }

    //is there a file?
    if (isset($_FILES['file'])) {
      $lokasi_buat_db = $tabel->lokasi;
      $arr_lokasi_file = explode('/new/', $lokasi_buat_db);
      array_pop($arr_lokasi_file);

      //$arr_lokasi_file = array();
      $file_count = count($_FILES['file']['name']);
      for ($i=0; $i<$file_count; $i++) {
        $tmpFilePath = $_FILES['file']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            //get the extension
            $ext = explode('.', basename($_FILES['file']['name'][$i]));
            $ext = end($ext);
            //nama file :
            $newNamaFile = md5($_FILES['file']['name'][$i].rand()).$i.$request->id.'.'.$ext;
            //buat db :
            $lokasifileskr = '/storage/disposisi/'.$newNamaFile;
            // yg paling penting cek extension, no php allowed
            if ($ext == "png" || $ext == "PNG" || $ext == "jpg" || $ext == "pdf" || $ext == "PDF" || $ext == "JPG" || $ext == "jpeg" || $ext == "JPEG" ||
                $ext == "docx" || $ext == "DOCX" || $ext == "doc" || $ext == "DOC" || $ext == "xls" || $ext == "XLS" || $ext == "XLSX" || $ext == "xlsx") {
              //store
              $destinasi = public_path('storage/disposisi/');
              $proses = move_uploaded_file($tmpFilePath, $destinasi.$newNamaFile);
              array_push($arr_lokasi_file,$destinasi.$newNamaFile);
              $lokasi_buat_db .= $lokasifileskr."/new/";

            } else {
              return Redirect::back()->withErrors(['format file salah, tidak bisa diupload']);
            }
          }
      }
      $file_count2 = $file_count;
      $file_count = sizeOf($arr_lokasi_file);


      //stor db and email here :
      if ($request->perihal == 0) {
        $perihal = 'Undangan';
      } else {
        $perihal = 'Pemberitahuan';
      }

      if ($request->email && $send_email) {
        //email :
        $sender = "UG Disposition Update | ".$perihal;
        $EmailSender = $request->email;
        $pesan = $request->desc;
        Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
        function ($message) use ($arr_lokasi_file,$file_count2)
        {
            if (Input::get('perihal') == 0) {
              $perihal = 'Undangan';
            } else {
              $perihal = 'Pemberitahuan';
            }
            $sender = "UG Disposition Update | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);

            $size = sizeOf($arr_lokasi_file);
            for ($i=0; $i < $size; $i++) {
                if ($i < $size-$file_count2) {
                  $message->attach(public_path($arr_lokasi_file[$i]));
                } else {
                  $message->attach($arr_lokasi_file[$i]);
                }
            }
        });
      }

      //stor ke db :
      $tabel->author = Auth::User()->id;
      $tabel->instansi_pengirim = $request->instansi;
      $tabel->lokasi = $lokasi_buat_db;
      $tabel->jumlah_file = $file_count;
      $tabel->perihal = $request->perihal;
      $tabel->email = $request->email;
      $tabel->tgl_disposisi = $request->date;
      $tabel->deskripsi = $request->desc;
      $tabel->save();

      return redirect('disposition')->with('status', 'Data Berhasil Di Simpan');
    } else {
        $tabel = surat_masuk::find($request->id);
        //kalo ga ada file yg di upload :
        //email :
        if ($request->perihal == 0) {
          $perihal = 'Undangan';
        } else {
          $perihal = 'Pemberitahuan';
        }


        //di kirim ke email ga?
        if ($request->email && $send_email && $tabel->lokasi) {
          $arr_lokasi_file = $tabel->lokasi;
          $arr_lokasi_file = explode('/new/', $tabel->lokasi);
          array_pop($arr_lokasi_file);
          //email :
          $sender = "UG Disposition | ".$perihal;
          $EmailSender = $request->email;
          $pesan = $request->desc;
          Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
          function ($message) use ($arr_lokasi_file)
          {
              if (Input::get('perihal') == 0) {
                $perihal = 'Undangan';
              } else {
                $perihal = 'Pemberitahuan';
              }
              $sender = "UG Disposition | ".$perihal;
              $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
              $message->to(Input::get('email'));
              $message->subject($sender);
              $size = sizeOf($arr_lokasi_file);
              for ($i=0; $i < $size; $i++) {
                  $message->attach(public_path($arr_lokasi_file[$i]));
              }
          });
        } elseif ($request->email && $send_email) {
          $sender = "UG Disposition Update | ".$perihal;
          $EmailSender = $request->email;
          $pesan = $request->desc;
          Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
          function ($message)
          {
              if (Input::get('perihal') == 0) {
                $perihal = 'Undangan';
              } else {
                $perihal = 'Pemberitahuan';
              }
              $sender = "UG Disposition Update | ".$perihal;
              $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
              $message->to(Input::get('email'));
              $message->subject($sender);
          });
        }
        //stor ke db :
        $tabel->author = Auth::User()->id;
        $tabel->instansi_pengirim = $request->instansi;
        $tabel->perihal = $request->perihal;
        $tabel->email = $request->email;
        $tabel->tgl_disposisi = $request->date;
        $tabel->deskripsi = $request->desc;
        $tabel->save();

        return redirect('disposition')->with('status', 'Data Berhasil Di Simpan');
    }
  }

  public function getSuratRektor() {
    return view('surat_rektor.index');
  }

  public function addSuratRektor() {
    return view('surat_rektor.add');
  }

  public function postSuratRektorAdd(Request $request) {
    if ($request->submit == 'saveandsend') {
      $send_email = true;
    } else {
      $send_email = false;
    }

    if (isset($_FILES['file'])) {
      $lokasi_buat_db = '';
      $arr_lokasi_file = array();
      $file_count = count($_FILES['file']['name']);
      for ($i=0; $i<$file_count; $i++) {
        $tmpFilePath = $_FILES['file']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            //get the extension
            $ext = explode('.', basename($_FILES['file']['name'][$i]));
            $ext = end($ext);
            //nama file :
            $newNamaFile = md5($_FILES['file']['name'][$i].rand()).$i.'.'.$ext;
            //buat db :
            $lokasifileskr = '/storage/surat_rektor/'.$newNamaFile;

            // yg paling penting cek extension, no php allowed
            if ($ext == "png" || $ext == "PNG" || $ext == "jpg" || $ext == "pdf" || $ext == "PDF" || $ext == "JPG" || $ext == "jpeg" || $ext == "JPEG" ||
                $ext == "docx" || $ext == "DOCX" || $ext == "doc" || $ext == "DOC" || $ext == "xls" || $ext == "XLS" || $ext == "XLSX" || $ext == "xlsx") {
              //store
              $destinasi = public_path('storage/surat_rektor/');
              $proses = move_uploaded_file($tmpFilePath, $destinasi.$newNamaFile);
              array_push($arr_lokasi_file,$destinasi.$newNamaFile);
              $lokasi_buat_db .= $lokasifileskr."/new/";
            } else {
              return Redirect::back()->withErrors(['format file salah, tidak bisa diupload']);
            }
          }
      }

      $perihal = Input::get('perihal');
      if ($request->email && $send_email) {
        //email :
        $sender = "UG Surat Rektor | ".$perihal;
        $EmailSender = $request->email;
        $pesan = $request->desc;
        Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
        function ($message) use ($arr_lokasi_file)
        {
            $perihal = Input::get('perihal');
            $sender = "UG Disposition Surat Rektor | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);
            $size = sizeOf($arr_lokasi_file);
            for ($i=0; $i < $size; $i++) {
                $message->attach($arr_lokasi_file[$i]);
            }
        });
      }

      //stor ke db :
      $tabel = new surat_rektor;
      $tabel->author = Auth::User()->id;
      $tabel->nomor = $request->nomor;
      $tabel->tujuan = $request->tujuan;
      $tabel->lokasi = $lokasi_buat_db;
      $tabel->jumlah_file = $file_count;
      $tabel->perihal = $request->perihal;
      $tabel->email = $request->email;
      $tabel->deskripsi = $request->desc;
      $tabel->save();

      return redirect('surat_rektor')->with('status', 'Data Berhasil Di Simpan');
    } else {
        //kalo ga ada file yg di upload :
        //email :
        $perihal = Input::get('perihal');
        if ($request->email && $send_email) {
          //email :
          $sender = "UG Surat Rektor | ".$perihal;
          $EmailSender = $request->email;
          $pesan = $request->desc;
          Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
          function ($message)
          {
              $perihal = Input::get('perihal');
              $sender = "UG Disposition Surat Rektor | ".$perihal;
              $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
              $message->to(Input::get('email'));
              $message->subject($sender);
          });
        }
        //stor ke db :
        $tabel = new surat_rektor;
        $tabel->author = Auth::User()->id;
        $tabel->nomor = $request->nomor;
        $tabel->tujuan = $request->tujuan;
        $tabel->perihal = $request->perihal;
        $tabel->email = $request->email;
        $tabel->deskripsi = $request->desc;
        $tabel->save();

        return redirect('surat_rektor')->with('status', 'Data Berhasil Di Simpan');
    }
  }

  public function SuratRektorDataTB() {
    return Datatables::of(surat_rektor::query())
          ->addColumn('action', function ($datatb) {
              return
               '<a style="margin-left:5px" href="/surat_rektor/'.$datatb->id.'/edit" class="btn btn-xs btn-info"><i class="fa fa-edit"></i> Ubah</a>'
               .'<div style="padding-top:10px"></div>'
              .'<button data-id="'.$datatb->id.'" data-nama="'.$datatb->perihal.'" class="delete-modal btn btn-xs btn-danger" type="submit"><i class="fa fa-trash"></i> Delete</button>';
          })
          ->editColumn('author', function ($datatb) {
                return $datatb->author ? DB::table('users')->where('id','=',$datatb->author)->first()->nama : '';
          })
          ->editColumn('updated_at', function ($datatb) {
              return $datatb->updated_at ? $datatb->updated_at->diffForHumans() : '';
          })
          ->addIndexColumn()
          ->make(true);
  }

  public function DeleteSuratRektor(Request $request) {
    $this->validate($request, [
      'id'      => 'required',
    ]);
    $data = surat_rektor::find($request->id);
    $lokasi_file = $data->lokasi;
    $arr_lokasi_file = explode('/new/', $lokasi_file);
    for ($i=0; $i < $data->jumlah_file; $i++) {
        unlink(public_path($arr_lokasi_file[$i]));
    };
    $data->delete();

    $response = array("success"=>"Surat Keluar Rektor Deleted");
    return response()->json($response,200);
  }

  public function updateFileSuratRektor(Request $request) {
    $this->validate($request, [
      'id'      => 'required',
      'path'    => 'required',
    ]);
    $data = surat_rektor::find($request->id);
    $path = $request->path."/new/";
    $new_path = str_replace($path,"",$data->lokasi);

    //delete the data.. and update db :
    unlink(public_path($request->path));
    $data->lokasi = $new_path;
    $data->jumlah_file = $data->jumlah_file-1;
    $data->save();

    $response = array("success"=>"File Deleted");
    return response()->json($response,200);
  }

  public function GETedit_surat_rektor($id) {
    $data = surat_rektor::find($id);
    return view('surat_rektor.edit',['data'=>$data]);
  }

  public function edit_surat_rektor(Request $request) {
    //get the current data
    $tabel = surat_rektor::find($request->id);

    //for send email
    if ($request->submit == 'saveandsend') {
      $send_email = true;
    } else {
      $send_email = false;
    }

    //is there a file?
    if (isset($_FILES['file'])) {
      $lokasi_buat_db = $tabel->lokasi;
      $arr_lokasi_file = explode('/new/', $lokasi_buat_db);
      array_pop($arr_lokasi_file);

      //$arr_lokasi_file = array();
      $file_count = count($_FILES['file']['name']);
      for ($i=0; $i<$file_count; $i++) {
        $tmpFilePath = $_FILES['file']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            //get the extension
            $ext = explode('.', basename($_FILES['file']['name'][$i]));
            $ext = end($ext);
            //nama file :
            $newNamaFile = md5($_FILES['file']['name'][$i].rand()).$i.$request->id.'.'.$ext;
            //buat db :
            $lokasifileskr = '/storage/surat_rektor/'.$newNamaFile;
            // yg paling penting cek extension, no php allowed
            if ($ext == "png" || $ext == "PNG" || $ext == "jpg" || $ext == "pdf" || $ext == "PDF" || $ext == "JPG" || $ext == "jpeg" || $ext == "JPEG" ||
                $ext == "docx" || $ext == "DOCX" || $ext == "doc" || $ext == "DOC" || $ext == "xls" || $ext == "XLS" || $ext == "XLSX" || $ext == "xlsx") {
              //store
              $destinasi = public_path('storage/surat_rektor/');
              $proses = move_uploaded_file($tmpFilePath, $destinasi.$newNamaFile);
              array_push($arr_lokasi_file,$destinasi.$newNamaFile);
              $lokasi_buat_db .= $lokasifileskr."/new/";

            } else {
              return Redirect::back()->withErrors(['format file salah, tidak bisa diupload']);
            }
          }
      }
      $file_count2 = $file_count;
      $file_count = sizeOf($arr_lokasi_file);


      //stor db and email here :
      $perihal = Input::get('perihal');
      if ($request->email && $send_email) {
        //email :
        $sender = "UG Surat Rektor | ".$perihal;
        $EmailSender = $request->email;
        $pesan = $request->desc;
        Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
        function ($message) use ($arr_lokasi_file,$file_count2)
        {
            $perihal = Input::get('perihal');
            $sender = "UG Disposition Surat Rektor | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);
            $size = sizeOf($arr_lokasi_file);
            for ($i=0; $i < $size; $i++) {
                if ($i < $size-$file_count2) {
                  $message->attach(public_path($arr_lokasi_file[$i]));
                } else {
                  $message->attach($arr_lokasi_file[$i]);
                }
            }
        });
      }

      //stor ke db :
      $tabel->author = Auth::User()->id;
      $tabel->nomor = $request->nomor;
      $tabel->tujuan = $request->tujuan;
      $tabel->lokasi = $lokasi_buat_db;
      $tabel->jumlah_file = $file_count;
      $tabel->perihal = $request->perihal;
      $tabel->email = $request->email;
      $tabel->deskripsi = $request->desc;
      $tabel->save();

      return redirect('surat_rektor')->with('status', 'Data Berhasil Di Simpan');
    } else {
        $tabel = surat_rektor::find($request->id);
        //kalo ga ada file yg di upload :
        //email :
        $perihal = Input::get('perihal');

        //di kirim ke email ga?
        if ($request->email && $send_email && $tabel->lokasi) {
          $arr_lokasi_file = $tabel->lokasi;
          $arr_lokasi_file = explode('/new/', $tabel->lokasi);
          array_pop($arr_lokasi_file);
          //email :
          $sender = "UG Surat Keluar Rektor Update | ".$perihal;
          $EmailSender = $request->email;
          $pesan = $request->desc;
          Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
          function ($message) use ($arr_lokasi_file)
          {
            $perihal = Input::get('perihal');
            $sender = "UG Surat Keluar Rektor Update | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);
              $size = sizeOf($arr_lokasi_file);
              for ($i=0; $i < $size; $i++) {
                  $message->attach(public_path($arr_lokasi_file[$i]));
              }
          });
        } elseif ($request->email && $send_email) {
          $sender = "UG Surat Keluar Rektor Update | ".$perihal;
          $EmailSender = $request->email;
          $pesan = $request->desc;
          Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
          function ($message)
          {
            $perihal = Input::get('perihal');
            $sender = "UG Disposition Surat Rektor | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);
          });
        }
        //stor ke db :
        $tabel->author = Auth::User()->id;
        $tabel->nomor = $request->nomor;
        $tabel->tujuan = $request->tujuan;
        $tabel->perihal = $request->perihal;
        $tabel->email = $request->email;
        $tabel->deskripsi = $request->desc;
        $tabel->save();

        return redirect('surat_rektor')->with('status', 'Data Berhasil Di Simpan');
    }
  }

  public function getSK_rektor() {
    return view('sk_rektor.index');
  }

  public function PostAddSkRektor(Request $request) {
    //button save atau save and send
    if ($request->submit == 'saveandsend') {
      $send_email = true;
    } else {
      $send_email = false;
    }

    //is there a afile?
    if (isset($_FILES['file'])) {
      $lokasi_buat_db = '';
      $arr_lokasi_file = array();
      $file_count = count($_FILES['file']['name']);
      for ($i=0; $i<$file_count; $i++) {
        $tmpFilePath = $_FILES['file']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            //get the extension
            $ext = explode('.', basename($_FILES['file']['name'][$i]));
            $ext = end($ext);
            //nama file :
            $newNamaFile = md5($_FILES['file']['name'][$i].rand()).$i.'.'.$ext;
            //buat db :
            $lokasifileskr = '/storage/sk_rektor/'.$newNamaFile;

            // yg paling penting cek extension, no php allowed
            if ($ext == "png" || $ext == "PNG" || $ext == "jpg" || $ext == "pdf" || $ext == "PDF" || $ext == "JPG" || $ext == "jpeg" || $ext == "JPEG" ||
                $ext == "docx" || $ext == "DOCX" || $ext == "doc" || $ext == "DOC" || $ext == "xls" || $ext == "XLS" || $ext == "XLSX" || $ext == "xlsx") {
              //store
              $destinasi = public_path('storage/sk_rektor/');
              $proses = move_uploaded_file($tmpFilePath, $destinasi.$newNamaFile);
              array_push($arr_lokasi_file,$destinasi.$newNamaFile);
              $lokasi_buat_db .= $lokasifileskr."/new/";
            } else {
              return Redirect::back()->withErrors(['format file salah, tidak bisa diupload']);
            }
          }
      }

      $perihal = Input::get('perihal');
      if ($request->email && $send_email) {
        //email :
        $sender = "UG Disposition : SK Rektor | ".$perihal;
        $EmailSender = $request->email;
        $pesan = $request->desc;
        Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
        function ($message) use ($arr_lokasi_file)
        {
            $perihal = Input::get('perihal');
            $sender = "UG Disposition : SK Rektor | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);
            $size = sizeOf($arr_lokasi_file);
            for ($i=0; $i < $size; $i++) {
                $message->attach($arr_lokasi_file[$i]);
            }
        });
      }

      //stor ke db :
      $tabel = new sk_rektor;
      $tabel->author = Auth::User()->id;
      $tabel->nomor_sk = $request->nomor;
      $tabel->tujuan = $request->tujuan;
      $tabel->lokasi = $lokasi_buat_db;
      $tabel->jumlah_file = $file_count;
      $tabel->perihal = $request->perihal;
      $tabel->email = $request->email;
      $tabel->deskripsi = $request->desc;
      $tabel->save();

      return redirect('sk_rektor')->with('status', 'Data Berhasil Di Simpan');
    } else {
        //kalo ga ada file yg di upload :
        //email :
        $perihal = Input::get('perihal');
        if ($request->email && $send_email) {
          //email :
          $sender = "UG Surat Rektor | ".$perihal;
          $EmailSender = $request->email;
          $pesan = $request->desc;
          Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
          function ($message)
          {
              $perihal = Input::get('perihal');
              $sender = "UG Disposition Surat Rektor | ".$perihal;
              $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
              $message->to(Input::get('email'));
              $message->subject($sender);
          });
        }
        //stor ke db :
        $tabel = new sk_rektor;
        $tabel->author = Auth::User()->id;
        $tabel->nomor_sk = $request->nomor;
        $tabel->tujuan = $request->tujuan;
        $tabel->perihal = $request->perihal;
        $tabel->email = $request->email;
        $tabel->deskripsi = $request->desc;
        $tabel->save();

        return redirect('sk_rektor')->with('status', 'Data Berhasil Di Simpan');
    }
  }

  public function getAddSKRektor() {
    return view('sk_rektor.add');
  }

  public function sk_rektorDataTB() {
    return Datatables::of(sk_rektor::query())
          ->addColumn('action', function ($datatb) {
              return
               '<a style="margin-left:5px" href="/sk_rektor/'.$datatb->id.'/edit" class="btn btn-xs btn-info"><i class="fa fa-edit"></i> Ubah</a>'
               .'<div style="padding-top:10px"></div>'
              .'<button data-id="'.$datatb->id.'" data-nama="'.$datatb->perihal.'" class="delete-modal btn btn-xs btn-danger" type="submit"><i class="fa fa-trash"></i> Delete</button>';
          })
          ->editColumn('author', function ($datatb) {
                return $datatb->author ? DB::table('users')->where('id','=',$datatb->author)->first()->nama : '';
          })
          ->addIndexColumn()
          ->make(true);
  }

  public function GETedit_sk_rektor($id) {
    $data = sk_rektor::find($id);
    return view('sk_rektor.edit',['data'=>$data]);
  }

  public function DeleteFileSK_Rektor(Request $request) {
    $this->validate($request, [
      'id'      => 'required',
      'path'    => 'required',
    ]);
    $data = sk_rektor::find($request->id);
    $path = $request->path."/new/";
    $new_path = str_replace($path,"",$data->lokasi);

    //delete the data.. and update db :
    unlink(public_path($request->path));
    $data->lokasi = $new_path;
    $data->jumlah_file = $data->jumlah_file-1;
    $data->save();

    $response = array("success"=>"File Deleted");
    return response()->json($response,200);
  }

  public function deleteSKRektor(Request $request) {
    $this->validate($request, [
      'id'      => 'required',
    ]);
    $data = sk_rektor::find($request->id);
    $lokasi_file = $data->lokasi;
    $arr_lokasi_file = explode('/new/', $lokasi_file);
    for ($i=0; $i < $data->jumlah_file; $i++) {
        unlink(public_path($arr_lokasi_file[$i]));
    };
    $data->delete();

    $response = array("success"=>"SK Rektor Deleted");
    return response()->json($response,200);
  }

  public function edit_sk_rektor(Request $request) {
    //get the current data
    $tabel = sk_rektor::find($request->id);

    //for send email
    if ($request->submit == 'saveandsend') {
      $send_email = true;
    } else {
      $send_email = false;
    }

    //is there a file?
    if (isset($_FILES['file'])) {
      $lokasi_buat_db = $tabel->lokasi;
      $arr_lokasi_file = explode('/new/', $lokasi_buat_db);
      array_pop($arr_lokasi_file);

      //$arr_lokasi_file = array();
      $file_count = count($_FILES['file']['name']);
      for ($i=0; $i<$file_count; $i++) {
        $tmpFilePath = $_FILES['file']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            //get the extension
            $ext = explode('.', basename($_FILES['file']['name'][$i]));
            $ext = end($ext);
            //nama file :
            $newNamaFile = md5($_FILES['file']['name'][$i].rand()).$i.$request->id.'.'.$ext;
            //buat db :
            $lokasifileskr = '/storage/sk_rektor/'.$newNamaFile;
            // yg paling penting cek extension, no php allowed
            if ($ext == "png" || $ext == "PNG" || $ext == "jpg" || $ext == "pdf" || $ext == "PDF" || $ext == "JPG" || $ext == "jpeg" || $ext == "JPEG" ||
                $ext == "docx" || $ext == "DOCX" || $ext == "doc" || $ext == "DOC" || $ext == "xls" || $ext == "XLS" || $ext == "XLSX" || $ext == "xlsx") {
              //store
              $destinasi = public_path('storage/sk_rektor/');
              $proses = move_uploaded_file($tmpFilePath, $destinasi.$newNamaFile);
              array_push($arr_lokasi_file,$destinasi.$newNamaFile);
              $lokasi_buat_db .= $lokasifileskr."/new/";

            } else {
              return Redirect::back()->withErrors(['format file salah, tidak bisa diupload']);
            }
          }
      }
      $file_count2 = $file_count;
      $file_count = sizeOf($arr_lokasi_file);


      //stor db and email here :
      $perihal = Input::get('perihal');
      if ($request->email && $send_email) {
        //email :
        $sender = "Update SK Rektor | ".$perihal;
        $EmailSender = $request->email;
        $pesan = $request->desc;
        Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
        function ($message) use ($arr_lokasi_file,$file_count2)
        {
            $perihal = Input::get('perihal');
            $sender = "Update SK Rektor | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);
            $size = sizeOf($arr_lokasi_file);
            for ($i=0; $i < $size; $i++) {
                if ($i < $size-$file_count2) {
                  $message->attach(public_path($arr_lokasi_file[$i]));
                } else {
                  $message->attach($arr_lokasi_file[$i]);
                }
            }
        });
      }

      //stor ke db :
      $tabel->author = Auth::User()->id;
      $tabel->nomor_sk = $request->nomor;
      $tabel->tujuan = $request->tujuan;
      $tabel->lokasi = $lokasi_buat_db;
      $tabel->jumlah_file = $file_count;
      $tabel->perihal = $request->perihal;
      $tabel->email = $request->email;
      $tabel->deskripsi = $request->desc;
      $tabel->save();

      return redirect('sk_rektor')->with('status', 'Data Berhasil Di Simpan');
    } else {
        $tabel = sk_rektor::find($request->id);
        //kalo ga ada file yg di upload :
        //email :
        $perihal = Input::get('perihal');

        //di kirim ke email ga?
        if ($request->email && $send_email && $tabel->lokasi) {
          $arr_lokasi_file = $tabel->lokasi;
          $arr_lokasi_file = explode('/new/', $tabel->lokasi);
          array_pop($arr_lokasi_file);
          //email :
          $sender = "Update SK Rektor | ".$perihal;
          $EmailSender = $request->email;
          $pesan = $request->desc;
          Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
          function ($message) use ($arr_lokasi_file)
          {
            $perihal = Input::get('perihal');
            $sender = "Update SK Rektor | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);
              $size = sizeOf($arr_lokasi_file);
              for ($i=0; $i < $size; $i++) {
                  $message->attach(public_path($arr_lokasi_file[$i]));
              }
          });
        } elseif ($request->email && $send_email) {
          $sender = "Update SK Rektor | ".$perihal;
          $EmailSender = $request->email;
          $pesan = $request->desc;
          Mail::send('emails.send', ['sender' => $sender, 'EmailSender' => $EmailSender, 'pesan' => $pesan],
          function ($message)
          {
            $perihal = Input::get('perihal');
            $sender = "UG Disposition Surat Rektor | ".$perihal;
            $message->from('ugdisposition@gmail.com', 'UG Disposition No Reply');
            $message->to(Input::get('email'));
            $message->subject($sender);
          });
        }
        //stor ke db :
        $tabel->author = Auth::User()->id;
        $tabel->nomor_sk = $request->nomor;
        $tabel->tujuan = $request->tujuan;
        $tabel->perihal = $request->perihal;
        $tabel->email = $request->email;
        $tabel->deskripsi = $request->desc;
        $tabel->save();

        return redirect('sk_rektor')->with('status', 'Data Berhasil Di Simpan');
    }
  }

  public function getSuratPurek() {
    return view('surat_purek.index');
  }

  public function getAddSuratPurek() {
    return view('surat_purek.add');
  }

  public function postAddSuratPurek(Request $request) {

  }


}
