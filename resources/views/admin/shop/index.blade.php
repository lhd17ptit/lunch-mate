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
                    Danh sách cửa hàng
                </nav>
            </div>
        </div>

    </div>
</div>
<div class="mt-1 px-3 pt-2 pb-5" style="background: white">
    <div class="d-flex mt-4 justify-content-end mb-4 col-12">
        <div class="btn btn-primary btn-add" style="margin-top: -7px;">Thêm mới</div>
    </div>
    <table id="example" class="table table-striped table-bordered data-table w-100" style="color:black">
        <thead>
            <tr class="text-center">
                <th>Tên cửa hàng</th>
                <th>Trạng thái</th>
                <th>Hoạt động</th>
            </tr>
        </thead>
        <tbody class="text-center">
        </tbody>
    </table>
</div>

<div class="modal fade" id="modal_item_shop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal_item_shop_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_item_shop_label">THÊM MỚI</h5>
                <button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="item_shop_id" value="">
                <div class="form-group position-relative">
                    <label for="title">TÊN <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control input-food" id="title" placeholder="Nhập tên shop" maxlength="250">
                    <span class="error font-size-normal text-danger" id="err_title"></span>
                </div>
                <div class="form-group position-relative">
                    <label for="description">Mô tả</label>
                    <input type="text" name="description" class="form-control input-food" id="description" placeholder="Nhập mô tả" maxlength="250">
                    <span class="error font-size-normal text-danger" id="err_description"></span>
                </div>
                <div class="form-group position-relative">
                    <label for="phone_number">Số điện thoại</label>
                    <input type="text" name="phone_number" class="form-control input-food" id="phone_number" placeholder="Nhập số điện thoại" maxlength="250">
                    <span class="error font-size-normal text-danger" id="err_phone_number"></span>
                </div>
                <div class="form-group position-relative">
                    <label for="note">Ghi chú</label>
                    <textarea name="note" class="form-control input-food" id="note" placeholder="Nhập ghi chú" maxlength="500"></textarea>
                    <span class="error font-size-normal text-danger" id="err_note"></span>
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
            <div class="modal-body" id="detail_admin">
                
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
                    url: '{{ route('admin.shops.list') }}',
                    data : function(d) {
                        //
                    }
                },
                columns: [
                    { data: 'name', name: 'name',  class: 'align-middle', orderable: false },
                    { data: 'status', name: 'status',  class: 'align-middle', orderable: false },
                    {data: 'action', name: 'action', class: 'align-middle', orderable: false, searchable: false},
                ]
            });

            $('input[type=text]').on('keyup', function() {
                var inputValue = $(this).val();
                inputValue = inputValue.replace(/\s+/g, ' ');
                $(this).val(inputValue);
            });
        });

        $(document).on('click', '.btn-close-modal', function () {
            $('#modal_item_shop').modal('hide');
            $('#modal_admin_detail').modal('hide');
        });

        $(document).on('change', '.switch-status', function() {
            var status = $(this).prop('checked') == true ? 0 : 1;
            var id = $(this).data('id');
            var datatable = $('#example').DataTable();
            $('.js-loading-mask').removeClass('is-remove');
            $.ajax({
                url: "shops/change-status?id="+id+'&status='+status,
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

        $(document).on('click', '.btn-add', function () {
            $('#modal_item_shop_label').html('THÊM MỚI');
            $('#item_shop_id').val('');
            $('#title').val('');
            $('#description').val('');
            $('#phone_number').val('');
            $('#note').val('');
            $('#err_description').html('');
            $('#err_phone_number').html('');
            $('#err_note').html('');
            $('#err_title').html('');
            $('#modal_item_shop').modal('show');
        });

        $(document).on('click', '#save_data', function () {
            var id = $('#item_shop_id').val();
            var flag = true;
            var title = $('#title').val();
            var description = $('#description').val();
            var phone_number = $('#phone_number').val();
            var note = $('#note').val();

            if (title == '') {
                flag = false;
                $('#err_title').html('Tên cửa hàng không được để trống');
            } else {
                $('#err_title').html('');
            }

            if (flag == true) {
                $('.js-loading-mask').removeClass('is-remove');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.shops.save') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id,
                        name: title,
                        description: description,
                        phone_number: phone_number,
                        note: note
                    },
                    success: function(data) {
                        $('.js-loading-mask').addClass('is-remove');
                        $('#modal_item_shop').modal('hide');
                        $('#example').DataTable().draw();
                        toastr.success("Thêm mới thành công");
                    },
                    error: function(error) {
                        $('.js-loading-mask').addClass('is-remove');
                        toastr.error("Thêm mới thất bại");
                        $('#err_title').html(error && error.responseJSON.errors && error.responseJSON.errors && error.responseJSON.errors.name  ? error.responseJSON.errors.name : '');
                    }
                });
            }
        });

        $(document).on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            var title = $(this).data('title');

            $('#modal_item_shop_label').html('CHỈNH SỬA');
            $('#item_shop_id').val(id);
            $('#title').val(title);
            $('#err_title').html('');
            $('#modal_item_shop').modal('show');
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
                        url: '{{ route('admin.shops.delete') }}',
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

        // $(document).on('click', '.btn-detail', function () {
        //     var id = $(this).data('id');
        //     $('.js-loading-mask').removeClass('is-remove');
        //     $.ajax({
        //         type: 'GET',
        //         url: '{{ route('admin.shops.detail') }}',
        //         data: {
        //             id: id,
        //             "_token": "{{ csrf_token() }}"
        //         },
        //         success: function(data) {
        //             $('.js-loading-mask').addClass('is-remove');
        //             $('#detail_admin').html(data.view);
        //             $('#modal_admin_detail').modal('show');
        //         },
        //         error: function(error) {
        //             $('.js-loading-mask').addClass('is-remove');
        //             toastr.error("Lây thông tin thất bại");
        //         }
        //     });
        // });
</script>
@endpush