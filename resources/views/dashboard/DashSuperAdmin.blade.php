@extends('layouts.dlayout')
@section('title') {{DB::table('setting_situses')->where('id','=','1')->first()->namaSitus}} || {{DB::table('roles')->where('id','=','1')->first()->namaRule}} Dashboard @endsection

@section('content')

<div class="row text-center">
    <div class="col-sm-6 col-lg-6 col-xl-3">
        <div class="card-box widget-flat border-success bg-success text-white">
            <i class="fa fa-envelope"></i>
            <h3 class="m-b-10">1000</h3>
            <p class="text-uppercase m-b-5 font-13 font-600">Total Surat</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-6 col-xl-3">
        <div class="card-box bg-primary widget-flat border-primary text-white">
            <i class="fa fa-user"></i>
            <h3 class="m-b-10">{{DB::table('users')->where('roles_id','=','3')->count()}}</h3>
            <p class="text-uppercase m-b-5 font-13 font-600">Total {{DB::table('roles')->where('id','=','3')->first()->namaRule}}</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-6 col-xl-3">
        <div class="card-box widget-flat border-custom bg-custom text-white">
            <i class="fa fa-user-secret"></i>
            <h3 class="m-b-10">{{DB::table('users')->where('roles_id','=','2')->count()}}</h3>
            <p class="text-uppercase m-b-5 font-13 font-600">Total {{DB::table('roles')->where('id','=','2')->first()->namaRule}}</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-6 col-xl-3">
        <div class="card-box bg-danger widget-flat border-danger text-white">
            <i class="fa fa-institution"></i>
            <h3 class="m-b-10">312</h3>
            <p class="text-uppercase m-b-5 font-13 font-600">Total Institusi</p>
        </div>
    </div>
</div>
<!-- end row -->


<div class="row">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title mb-4">Surat Masuk Terbaru <br>
            </h4>

        </div>
    </div>
</div>
@endsection


@section('jstambahan')

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
