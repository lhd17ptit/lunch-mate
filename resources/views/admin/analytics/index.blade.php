@extends('admin.dashboard-layout')

@section('title', 'Thống kê')

@section('content')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css" rel="stylesheet">

<div class="page-breadcrumb" style="margin-top: -30px;">
    <div class="row">
        <div class="col-5 align-self-center">
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb" style="font-size: 20px; font-weight: 600;">
                    Thống kê
                </nav>
            </div>
        </div>

    </div>
</div>
<div class="mt-1 px-3 pt-2 pb-5" style="background: white">
    <div class="row mt-5">
        <div class="d-flex col-9">
            <div class="form-group position-relative">
                <select class="form-control input-food" id="search_floor" style="width: 250px;">
                    <option value="">Tìm kiếm theo tầng</option>
                     @foreach($floors as $floor)
                        <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group position-relative">
                <input type="date" name="search_date" class="form-control input-food ml-3" id="search_date" placeholder="Nhập ngày ..." maxlength="250" style="width: 200px">
            </div>
        </div>
        
        <div class="d-flex justify-content-end mb-4 col-3">
            <div class="btn btn-primary btn-export" style="margin-top: -7px;">Xuất file</div>
        </div>
    </div>
    <table id="example" class="table table-striped table-bordered data-table w-100" style="color:black">
        <thead>
            <tr class="text-center">
                <th>Tên khách hàng</th>
                <th>Tầng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody class="text-center">
        </tbody>
    </table>
</div>

<div class="modal fade" id="modal_admin_detail" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal_admin_detail_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_item_admin_label">Chi tiết</h5>
                <button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body preview-menu-today" id="detail_admin">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close-modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var datatable = $('#example').DataTable({
                responsive : true,
                processing : true,
                serverSide : true,
                stateSave: false,
                searching: false,
                pageLength: 30,
                ajax: {
                    method: "GET",
                    url: '{{ route('admin.analytics.list') }}',
                    data : function(d) {
                        d.search_floor = $('#search_floor').val();
                        d.search_date = $('#search_date').val();
                    }
                },
                columns: [
                    { data: 'user', name: 'user',  class: 'align-middle', orderable: false },
                    { data: 'floor', name: 'floor',  class: 'align-middle', orderable: false },
                    { data: 'created_at', name: 'created_at',  class: 'align-middle', orderable: false },
                    { data: 'amount', name: 'amount',  class: 'align-middle', orderable: false },
                    {data: 'action', name: 'action', class: 'align-middle', orderable: false, searchable: false},
                ]
            });

            $('input[type=text]').on('keyup', function() {
                var inputValue = $(this).val();
                inputValue = inputValue.replace(/\s+/g, ' ');
                $(this).val(inputValue);
            });

            $('#search_floor').on('change', function() {
                $('#example').DataTable().draw();
            });

            $('#search_date').on('change', function() {
                $('#example').DataTable().draw();
            });
        });

        $(document).on('click', '.btn-close-modal', function () {
            $('#modal_admin_detail').modal('hide');
        });

        $(document).on('click', '.btn-detail', function () {
            var id = $(this).data('id');
            $('.js-loading-mask').removeClass('is-remove');
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.analytics.detail') }}',
                data: {
                    id: id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('.js-loading-mask').addClass('is-remove');
                    $('#detail_admin').html(data.data.view);
                    $('#modal_admin_detail').modal('show');
                },
                error: function(error) {
                    $('.js-loading-mask').addClass('is-remove');
                    toastr.error("Lây thông tin thất bại");
                }
            });
        });

        $(document).on('click', '.btn-export', function () {
            var search_floor = $('#search_floor').val();
            var search_date = $('#search_date').val();
            window.location.href = '{{ route('admin.analytics.export') }}?search_floor=' + search_floor + '&search_date=' + search_date;
        });
</script>
@endpush