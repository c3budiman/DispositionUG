<?php
  $logo = DB::table('setting_situses')->where('id','=','1')->first()->logo;
?>
@extends('layouts.dlayout')

@section('title')
  {{DB::table('setting_situses')->where('id','=','1')->first()->namaSitus}} | Tambah Surat Masuk
@endsection

@section('content')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <div class="card-box">
    <form enctype="multipart/form-data" action="{{url(action("AdminController@postDispositionAdd"))}}" method="post" class="form-horizontal ">
    <div class="pull-right">
        <button  name="submit" type="submit" value="save" class="btn btn-success"><i class="fa fa-upload"></i> Save</button>
        <button  name="submit" type="submit" value="saveandsend" class="btn btn-info"><i class="fa fa-send"></i> Save And Send</button>
    </div>
      <h4 class="header-title m-t-0">Menambah Surat Masuk</h4>
      <p class="text-muted font-14 m-b-10">
         Disini anda bisa menambah Disposition
      </p>
          {{ csrf_field() }}
          <div class="form-group row">
              <label class="col-2 col-form-label">Instansi Pengirim : </label>
              <div class="col-4">
                  <input name="instansi" type="text" required class="form-control" value="">
              </div>
              <label class="col-2 col-form-label">Disposisi (Email) : </label>
              <div class="col-4">
                <select id="selectnya" class="js-example-basic-single" name="email">
                  <?php
                  $email = DB::table('known_email')->get();
                   ?>
                   <option value="">Select Email</option>
                   <option value="other">Other Email</option>
                   <option value="no">No Email</option>
                   @foreach ($email as $em)
                     <option value="{{$em->email}}">{{$em->nama}} || {{$em->email}}</option>
                   @endforeach
                </select>
                <br>
                <input id="emailother" class="form-control" type="email" name="email" value="" placeholder="masukkan email disini">
              </div>
          </div>
          <div class="form-group row">
              <label class="col-2 col-form-label">Perihal : </label>
              <div class="col-4">
                  <select class="form-control" name="perihal">
                    <option value="0">Undangan</option>
                    <option value="1">Pemberitahuan</option>
                  </select>
              </div>
              <label class="col-2 col-form-label">File : </label>
              <div class="col-4">
                <input name="file[]" type="file" />
                <button class="add_more">Tambah Files</button>
              </div>
          </div>
          <div class="form-group row">
              <label class="col-2 col-form-label">Tanggal Surat : </label>
              <div class="col-9">
                <input class="form-control" type="date" name="date">
              </div>
          </div>
          <div class="form-group row">
              <label class="col-2 col-form-label">Deskripsi : </label>
              <div class="col-9">
                <textarea class="form-control" name="desc" rows="4" cols="80"></textarea>
              </div>
          </div>
      </form>
  </div>
  <!--  end row -->

  <script type="text/javascript">
    $(document).ready(function() {
      $('#emailother').hide();
      $('.js-example-basic-single').select2();
      $('select').on('change', function (e) {
          var optionSelected = $("option:selected", this);
          var valueSelected = this.value;
          if (valueSelected === 'other') {
            $('#emailother').show();
          } else {
            $('#emailother').hide();
          }
          console.log(valueSelected);
      });

       $('.add_more').click(function(e){
         e.preventDefault();
         $(this).before("<input name='file[]' type='file'/>");
       });
    });
  </script>
  <!-- Sweet Alert Js  -->
  <script src="plugins/sweet-alert/sweetalert2.min.js"></script>

  @if (session('status'))
    <script type="text/javascript">
    !function ($) {
      "use strict";
      var SweetAlert = function () {
      };
      SweetAlert.prototype.init = function () {
          $(document).ready(function () {
              swal(
                  {
                      title: 'Sukses!',
                      text: '{{ session('status') }}',
                      type: 'success',
                      confirmButtonClass: 'btn btn-confirm mt-2'
                  }
              )
          });
        },
     $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert
          }(window.jQuery),
            function ($) {
                "use strict";
                $.SweetAlert.init()
            } (window.jQuery);
    </script>
  @endif

  @if($errors->any())
  <script type="text/javascript">
  !function ($) {
    "use strict";
    var SweetAlert = function () {
    };
    SweetAlert.prototype.init = function () {
        $(document).ready(function () {
            swal(
                {
                    title: 'Error!',
                    text: '{{$errors->first()}}',
                    type: 'error',
                    confirmButtonClass: 'btn btn-confirm mt-2'
                }
            )
        });
      },
   $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert
        }(window.jQuery),
          function ($) {
              "use strict";
              $.SweetAlert.init()
          } (window.jQuery);
  </script>
  @endif
@endsection
