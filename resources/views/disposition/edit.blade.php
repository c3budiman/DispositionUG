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
    <form enctype="multipart/form-data" action="/disposition/edit" method="post" class="form-horizontal ">
    <div class="pull-right">
        <a  href="/disposition" class="btn btn-danger"><i class="fa fa-reply"></i> Cancel</a>
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
                  <input name="instansi" type="text" required class="form-control" value="{{$data->instansi_pengirim}}">
              </div>
              <input type="hidden" name="id" value="{{$data->id}}">
              <label class="col-2 col-form-label">Disposisi (Email) : </label>
              <div class="col-4">
                <select id="selectnya" class="js-example-basic-single" name="email">
                  <?php
                  $email = DB::table('known_email')->get();
                   ?>
                   @if ($data->email)
                     <option value="{{$data->email}}">{{$data->email}}</option>
                   @else
                     <option value="">No Email</option>
                   @endif
                   <option value="other">Other Email</option>
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
                    @if ($data->perihal == '0')
                      <option value="0">Undangan</option>
                      <option value="1">Pemberitahuan</option>
                    @else
                      <option value="1">Pemberitahuan</option>
                      <option value="0">Undangan</option>
                    @endif

                  </select>
              </div>
              <label class="col-2 col-form-label">File : </label>
              <div class="col-4">
                <?php $lokasi_file = $data->lokasi;
                $arr_lokasi_file = explode('/new/', $lokasi_file);
                ?>

                @for ($i=0; $i < $data->jumlah_file; $i++)
                 <a class="btn btn-xs btn-info" href="{{$arr_lokasi_file[$i]}}"><i class="fa fa-file"></i> File {{$i+1}} </a>

                 <button data-id="{{$data->id}}" data-id-file="{{$i+1}}" data-lokasi="{{$arr_lokasi_file[$i]}}" class="delete-modal btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>
                 <div style="margin-top:10px;">

                 </div>
                @endfor
                <br>
                <button class="add_more">Tambah Files</button>
              </div>
          </div>
          <div class="form-group row">
              <label class="col-2 col-form-label">Tanggal Surat : </label>
              <div class="col-9">
                <?php use Carbon\Carbon; ?>
                <input class="form-control" type="date" name="date" value="{{with(new Carbon($data->tgl_disposisi))->format('Y-m-d')}}">
              </div>
          </div>
          <div class="form-group row">
              <label class="col-2 col-form-label">Deskripsi : </label>
              <div class="col-9">
                <textarea class="form-control" name="desc" rows="4" cols="80">{{$data->deskripsi}}</textarea>
              </div>
          </div>
      </form>
  </div>
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">

          <div class="deleteContent">
            Delete file ke :
            <span class="did"></span> ?
              {{ csrf_field() }}
              <input type="hidden" id="iddelete">
              <input type="hidden" id="path">
          </div>



          <div class="modal-footer">
            <button type="button" class="btn actionBtn" data-dismiss="modal">
              <span id="footer_action_button" class='glyphicon'> </span>
            </button>
            <button type="button" class="btn btn-warning" data-dismiss="modal">
              <span class='glyphicon glyphicon-remove'></span> Batal
            </button>
          </div>

        </div>
      </div>
    </div>
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

       $(document).on('click', '.delete-modal', function(e) {
             e.preventDefault();
             $('#footer_action_button').text(" Delete");
             $('#footer_action_button').removeClass('glyphicon-check');
             $('#footer_action_button').addClass('glyphicon-trash');
             $('.actionBtn').removeClass('btn-success');
             $('.actionBtn').addClass('btn-danger');
             $('.actionBtn').addClass('delete');
             $('.modal-title').text('Delete');
             $('.deleteContent').show();
             $('.did').text($(this).data('id-file'));
             $('#iddelete').val($(this).data('id'));
             $('#path').val($(this).data('lokasi'));
             $('.dname').html($(this).data('nama'));
             $('#myModal').modal('show');
       });

       $('.modal-footer').on('click', '.delete', function() {
           $.ajax({
               type: "POST",
               url: "/disposition/file/delete",
               dataType: "json",
               data: {
                 '_token': $('input[name=_token]').val(),
                 id: $("#iddelete").val(),
                 path: $("#path").val(),
               },
               success: function (data, status) {
                   location.reload();
               },
               error: function (request, status, error) {
                   console.log($("#iddelete").val());
                   console.log(request.responseJSON);
                   $.each(request.responseJSON.errors, function( index, value ) {
                     alert( value );
                   });
               }
           });
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
