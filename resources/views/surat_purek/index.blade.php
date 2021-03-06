@extends('layouts.dlayout')

@section('title')
  {{DB::table('setting_situses')->where('id','=','1')->first()->namaSitus}} | Surat Purek II
@endsection

@section('content')
  <!-- Start Page content -->
  <div class="content">
      <div class="container-fluid">
          <div class="row">
              <div class="col-12">
                  <div class="card-box table-responsive">
                      <h4 class="m-t-0 header-title">Disposisi : Surat Purek II Keluar</h4>
                      <p class="text-muted font-14 m-b-30">
                          Anda bisa menambah, mengedit dan menghapus Surat Purek II. fungsi pencarian berlaku untuk seluruh kolom.
                      </p>
                      <div class="pull-right" style="margin-top:-50px">
                          <a href="/surat_purek/add" class="btn btn-xs btn-success"> <i class="fa fa-plus"></i> Tambah</a>
                          {{-- <button type="button"  href="#" class="btn btn-xs btn-success" id="tambah"> <i class="fa fa-plus"></i> Tambah</button> --}}
                      </div>

                      <br>

                      <table id="contoh" class="table table-bordered table-hover datatable">
                          <thead>
                              <tr>
                                  <th>No</th>
                                  <th>Terakhir Di Ubah</th>
                                  <th>Nomor Surat</th>
                                  <th>Tanggal Surat</th>
                                  <th>Perihal</th>
                                  <th>Jumlah Files</th>
                                  <th>Pencatat</th>
                                  <th colspan="10%">Action</th>
                              </tr>
                          </thead>
                      </table>
                  </div>
              </div>
          </div>
      </div>
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
            Delete surat dengan perihal :
            "<span class="dname"></span>" ?
              {{ csrf_field() }}
              <input type="hidden" id="iddelete">
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

@endsection
@section('js')
  <script type="text/javascript">
  $(document).ready(function() {
      // $('.datatable').DataTable({
      //         "language": {
      //         "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
      //     },
      //     processing: true,
      //     serverSide: true,
      //     ajax: '{{ route('sk_rektor/json') }}',
      //     columns: [
      //         {data: 'DT_Row_Index', orderable: false, searchable: false},
      //         {data: 'updated_at', name: 'updated_at'},
      //         {data: 'nomor_sk', name: 'nomor_sk'},
      //         {data: 'tujuan', name: 'tujuan'},
      //         {data: 'perihal', name: 'perihal'},
      //         {data: 'jumlah_file', name: 'jumlah_file'},
      //         {data: 'author', name: 'author'},
      //         {data: 'action', name: 'action', orderable: false, searchable: false},
      //     ]
      // });
      $(document).on('click', '#tambah', function() {
          $('#tambah-sidebar').modal('show');
      });

      $(document).on('click', '.delete-modal', function() {
            $('#footer_action_button').text(" Delete");
            $('#footer_action_button').removeClass('glyphicon-check');
            $('#footer_action_button').addClass('glyphicon-trash');
            $('.actionBtn').removeClass('btn-success');
            $('.actionBtn').addClass('btn-danger');
            $('.actionBtn').addClass('delete');
            $('.modal-title').text('Delete');
            $('.did').text($(this).data('perihal'));
            $('.deleteContent').show();
            $('.form-horizontal').hide();
            $('#iddelete').val($(this).data('id'));
            $('.dname').html($(this).data('nama'));
            $('#myModal').modal('show');
      });

      $('.modal-footer').on('click', '.delete', function() {
          $.ajax({
              type: "POST",
              url: "/surat_purek/delete",
              dataType: "json",
              data: {
                '_token': $('input[name=_token]').val(),
                id: $("#iddelete").val(),
              },
              success: function (data, status) {
                  $('.datatable').DataTable().ajax.reload(null, false);
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
@endsection
