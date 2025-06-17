@extends('admin.dashboard-layout')

@section('title', 'Quản lý của hàng')

@section('content')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css" rel="stylesheet">

<div class="page-breadcrumb" style="margin-top: -30px;">
    <div class="row">
        <div class="col-5 align-self-center">
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb" style="font-size: 20px; font-weight: 600;">
                    Danh sách menu
                </nav>
            </div>
        </div>

    </div>
</div>
<div class="mt-1 px-3 pt-2 pb-5" style="background: white">
    <div class="row mt-5">
        <div class="d-flex col-9">
            <div class="form-group position-relative">
                <select class="form-control input-food" id="search_shop" style="width: 250px;">
                    <option value="">Tìm kiếm theo cửa hàng</option>
                     @foreach($shops as $shop)
                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group position-relative">
                <input type="date" name="search_date" class="form-control input-food ml-3" id="search_date" placeholder="Nhập ngày ..." maxlength="250" style="width: 200px">
            </div>
        </div>
        
        <div class="d-flex justify-content-end mb-4 col-3">
            <div class="btn btn-primary btn-add" style="margin-top: -7px;">Thêm mới</div>
        </div>
    </div>
    <table id="example" class="table table-striped table-bordered data-table w-100" style="color:black">
        <thead>
            <tr class="text-center">
                <th>Tên menu</th>
                <th>Cửa hàng</th>
                <th>Ngày tạo</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody class="text-center">
        </tbody>
    </table>
</div>

<div class="modal fade" id="modal_item_menu" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal_item_menu_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_item_menu_label">THÊM MỚI</h5>
                <button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="item_menu_id" value="">
                <div class="form-group position-relative">
                    <label for="title">TÊN <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control input-food" id="title" placeholder="Nhập tiêu đề" maxlength="250">
                    <span class="error font-size-normal text-danger" id="err_title"></span>
                </div>
                <div class="form-group position-relative">
                    <label for="shop_id">Cửa hàng <span class="text-danger">*</span></label>
                    <select class="form-control" id="shop_id" name="shop_id">
                        <option value="">Chọn của hàng</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                    <span class="error font-size-normal text-danger" id="err_shop"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close-modal" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="save_data">Lưu</button>
            </div>
        </div>
    </div>
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
                ajax: {
                    method: "GET",
                    url: '{{ route('admin.menus.list') }}',
                    data : function(d) {
                        d.search_shop = $('#search_shop').val();
                        d.search_date = $('#search_date').val();
                    }
                },
                columns: [
                    { data: 'title', name: 'title',  class: 'align-middle', orderable: false },
                    { data: 'shop', name: 'shop',  class: 'align-middle', orderable: false },
                    { data: 'created_at', name: 'created_at',  class: 'align-middle', orderable: false },
                    { data: 'status', name: 'status',  class: 'align-middle', orderable: false },
                    {data: 'action', name: 'action', class: 'align-middle', orderable: false, searchable: false},
                ]
            });

            $('input[type=text]').on('keyup', function() {
                var inputValue = $(this).val();
                inputValue = inputValue.replace(/\s+/g, ' ');
                $(this).val(inputValue);
            });

            $('#search_shop').on('change', function() {
                $('#example').DataTable().draw();
            });

            $('#search_date').on('change', function() {
                $('#example').DataTable().draw();
            });
        });

        $(document).on('click', '.btn-close-modal', function () {
            $('#modal_item_menu').modal('hide');
            $('#modal_admin_detail').modal('hide');
        });

        $(document).on('click', '.btn-add', function () {
            $('#modal_item_menu_label').html('THÊM MỚI');
            $('#item_menu_id').val('');
            $('#title').val('');
            $('#shop_id').val('');
            $('#err_title').html('');
            $('#err_shop').html('');
            $('#modal_item_menu').modal('show');
        });

        $(document).on('click', '#save_data', function () {
            var id = $('#item_menu_id').val();
            var flag = true;
            var title = $('#title').val();
            var shop_id = $('#shop_id').val();

            if (title == '') {
                flag = false;
                $('#err_title').html('Tên cửa hàng không được để trống');
            } else {
                $('#err_title').html('');
            }

            if (shop_id == '') {
                flag = false;
                $('#err_shop').html('Cửa hàng không được để trống');
            } else {
                $('#err_shop').html('');
            }

            if (flag == true) {
                $('.js-loading-mask').removeClass('is-remove');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.menus.save') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id,
                        title: title,
                        shop: shop_id,
                    },
                    success: function(data) {
                        $('.js-loading-mask').addClass('is-remove');
                        $('#modal_item_menu').modal('hide');
                        $('#example').DataTable().draw();
                        toastr.success("Thêm mới thành công");
                    },
                    error: function(error) {
                        $('.js-loading-mask').addClass('is-remove');
                        toastr.error("Thêm mới thất bại");
                        $('#err_title').html(error && error.responseJSON.errors && error.responseJSON.errors && error.responseJSON.errors.name  ? error.responseJSON.errors.name : '');
                        $('#err_shop').html(error && error.responseJSON.errors && error.responseJSON.errors && error.responseJSON.errors.shop  ? error.responseJSON.errors.shop : '');
                    }
                });
            }
        });


        $(document).on('click', '.btn-delete', function () {
            var id = $(this).data('id');
             
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa shop này?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.js-loading-mask').removeClass('is-remove');
                    $.ajax({
                        type: 'DELETE',
                        url: '{{ route('admin.menus.delete') }}',
                        data: {
                            id: id,
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            $('.js-loading-mask').addClass('is-remove');
                            $('#example').DataTable().draw();
                            toastr.success("Xóa thành công");

                        },
                        error: function(error) {
                            $('.js-loading-mask').addClass('is-remove');
                            toastr.error("Xóa thất bại");
                        }
                    });
                }
            })
        });

        $(document).on('click', '.btn-detail', function () {
            var id = $(this).data('id');
            $('.js-loading-mask').removeClass('is-remove');
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.menus.detail') }}',
                data: {
                    id: id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('.js-loading-mask').addClass('is-remove');
                    $('#detail_admin').html(data.view);
                    $('#modal_admin_detail').modal('show');
                },
                error: function(error) {
                    $('.js-loading-mask').addClass('is-remove');
                    toastr.error("Lây thông tin thất bại");
                }
            });
        });

        $(document).on('change', '.switch-status', function() {
            var status = $(this).prop('checked') == true ? 0 : 1;
            var id = $(this).data('id');
            var datatable = $('#example').DataTable();
            $('.js-loading-mask').removeClass('is-remove');
            $.ajax({
                url: "menus/change-status?id="+id+'&status='+status,
                type: 'GET',
                success: function(data) {
                    $('.js-loading-mask').addClass('is-remove');
                    datatable.draw();
                    toastr.success("Thay đổi trạng thái thành công.");
                },
                error: function(error) {
                    $('.js-loading-mask').addClass('is-remove');
                    toastr.error("Thay đổi trạng thái thất bại");
                    datatable.draw();
                }
            });
        });
</script>
@endpush