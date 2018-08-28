@extends('layouts.dlayout')

@section('title')
  {{DB::table('setting_situses')->where('id','=','1')->first()->namaSitus}} | Surat Masuk
@endsection

@section('content')
  <!-- Start Page content -->
  <div class="content">
      <div class="container-fluid">
          <div class="row">
              <div class="col-12">
                  <div class="card-box table-responsive">
                      <h4 class="m-t-0 header-title">Disposition List</h4>
                      <p class="text-muted font-14 m-b-30">
                          Anda bisa menambah, mengedit dan menghapus disposisi surat. fungsi pencarian berlaku untuk seluruh kolom.
                      </p>
                      <div class="pull-right" style="margin-top:-50px">
                          <a href="/disposition/add" class="btn btn-xs btn-success"> <i class="fa fa-plus"></i> Tambah</a>
                          {{-- <button type="button"  href="#" class="btn btn-xs btn-success" id="tambah"> <i class="fa fa-plus"></i> Tambah</button> --}}
                      </div>

                      <br>

                      <table id="contoh" class="table table-bordered table-hover datatable">
                          <thead>
                              <tr>
                                  <th>No</th>
                                  <th>Author</th>
                                  <th>Instansi Pengirim</th>
                                  <th>Perihal</th>
                                  <th>Deskripsi</th>
                                  <th>Jumlah Files</th>
                                  <th>Tgl Surat</th>
                                  <th>Terakhir di update</th>
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
            Delete surat dengan deskripsi :
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
      $('.datatable').DataTable({
              "language": {
              "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
          },
          processing: true,
          serverSide: true,
          ajax: '{{ route('disposition/json') }}',
          columns: [
              {data: 'DT_Row_Index', orderable: false, searchable: false},
              {data: 'author', name: 'author'},
              {data: 'instansi_pengirim', name: 'instansi_pengirim'},
              {data: 'perihal', name: 'perihal'},
              {data: 'deskripsi', name: 'deskripsi'},
              {data: 'jumlah_file', name: 'jumlah_file'},
              {data: 'tgl_disposisi', name: 'tgl_disposisi'},
              {data: 'updated_at', name: 'updated_at'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ]
      });
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
            $('.did').text($(this).data('deskripsi'));
            $('.deleteContent').show();
            $('.form-horizontal').hide();
            $('#iddelete').val($(this).data('id'));
            $('.dname').html($(this).data('nama'));
            $('#myModal').modal('show');
      });

      $('.modal-footer').on('click', '.delete', function() {
          $.ajax({
              type: "POST",
              url: "/disposition/delete",
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
