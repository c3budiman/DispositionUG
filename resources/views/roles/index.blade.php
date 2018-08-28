@extends('layouts.dlayout')

@section('title')
  Roles Management
@endsection

@section('css')
  <script src="{{ asset('js/app2.js') }}"></script>
@endsection

@section('content')
  <div class="content">
      <div class="container-fluid">

          <div class="row">
              <div class="col-12">
                  <div class="card-box table-responsive">
                      <h4 class="m-t-0 header-title">Mengatur Roles</h4>
                      <p class="text-muted font-14 m-b-30">
                          Untuk keperluan khusus anda dapat mengubah nama roles.
                      </p>

                      <table id="contoh" class="table table-bordered table-hover datatable">
                          <thead>
                              <tr>
                                  <th>id</th>
                                  <th>Nama Roles</th>
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
          <form class="form-horizontal" role="form">
            {{ csrf_field() }}
            <div class="form-group">
              <label class="control-label col-sm-2" for="id">ID:</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="fid" disabled>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-sm-2" for="name">NamaRule:</label>
              <div class="col-sm-10">
                <input type="name" class="form-control" id="n">
              </div>
            </div>

          </form>


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

    // DataTable
    $('.datatable').DataTable({
            "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Indonesian-Alternative.json"
        },
        processing: true,
        serverSide: true,
        ajax: '{{ route('roles/json') }}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'namaRule', name: 'namaRule'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });


    // ShowModals
    $(document).on('click', '.edit-modal', function() {
          $('#footer_action_button').text("Ubah");
          $('#footer_action_button').addClass('glyphicon-check');
          $('#footer_action_button').removeClass('glyphicon-trash');
          $('.actionBtn').addClass('btn-success');
          $('.actionBtn').removeClass('btn-danger');
          $('.actionBtn').addClass('edit');
          $('.modal-title').text('Ubah user');
          $('.deleteContent').hide();
          $('.form-horizontal').show();
          $('#fid').val($(this).data('id'));
          $('#n').val($(this).data('namarule'));
          $('#myModal').modal('show');
      });

      $('.modal-footer').on('click', '.edit', function() {
          $.ajax({
              type: "POST",
              url: "/roles/edit",
              dataType: "json",
              data: {
                '_token': $('input[name=_token]').val(),
                id: $("#fid").val(),
                namaRule: $("#n").val(),
              },
              success: function (data, status) {
                  $('.datatable').DataTable().ajax.reload(null, false);
              },
              error: function (request, status, error) {
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
