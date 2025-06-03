@extends('admin.dashboard-layout')

@section('title', 'Người quản lý tầng')

@section('content')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css" rel="stylesheet">

<div class="page-breadcrumb" style="margin-top: -30px;">
    <div class="row">
        <div class="col-5 align-self-center">
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb" style="font-size: 20px; font-weight: 600;">
                    Danh sách người quản lý
                </nav>
            </div>
        </div>

    </div>
</div>
<div class="mt-1 px-3 pt-2 pb-5" style="background: white">
    <div class="row mt-5">
        <div class="d-flex col-9">
            <div class="form-group position-relative">
                <input type="text" name="search" class="form-control input-food" id="search" placeholder="Nhập từ khóa ..." maxlength="250" style="width: 250px">
            </div>

            <div class="form-group position-relative">
                <select class="form-control input-food ml-3" id="search_floor" style="width: 250px;">
                    <option value="">Tìm kiếm theo tầng</option>
                     @foreach($floors as $floor)
                        <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group position-relative">
                <select class="form-control input-food ml-3" id="search_role" style="width: 250px;">
                    <option value="">Tìm kiếm theo vai trò</option>
                    <option value="1">Admin</option>
                    <option value="2">Partner</option>
                </select>
            </div>
        </div>
        
        <div class="d-flex justify-content-end mb-4 col-3">
            <div class="btn btn-primary btn-add" style="margin-top: -7px;">Thêm mới</div>
        </div>
    </div>
    <table id="example" class="table table-striped table-bordered data-table w-100" style="color:black">
        <thead>
            <tr class="text-center">
                <th>Tên</th>
                <th>Số điện thoại</th>
                <th>QL tầng</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Hoạt động</th>
            </tr>
        </thead>
        <tbody class="text-center">
        </tbody>
    </table>
</div>

<div class="modal fade" id="modal_item_admin" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal_item_admin_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_item_admin_label">THÊM MỚI</h5>
                <button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="item_admin_id" value="">
                <div class="form-group position-relative">
                    <label for="title">Tên <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control input-food" id="name" placeholder="Nhập tên" maxlength="250">
                    <span class="error font-size-normal text-danger" id="err_name"></span>
                </div>

                <div class="form-group position-relative">
                    <label for="title">Email <span class="text-danger">*</span></label>
                    <input type="text" name="email" class="form-control input-food" id="email" placeholder="Nhập email" maxlength="250">
                    <span class="error font-size-normal text-danger" id="err_email"></span>
                </div>

                <div class="form-group position-relative">
                    <label for="title">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" name="phone_number" class="form-control input-food" id="phone_number" placeholder="Nhập số điện thoại" maxlength="20">
                    <span class="error font-size-normal text-danger" id="err_phone_number"></span>
                </div>

                <div class="form-group position-relative">
                    <label for="title">Vai trò <span class="text-danger">*</span></label>
                    <select class="form-control" id="role" name="role">
                        <option value="1">Admin</option>
                        <option value="2">Partner</option>
                    </select>
                </div>

                <div class="form-group position-relative">
                    <label for="title">Quản lý tầng</label>
                    <select class="form-control" id="floor_id" name="floor_id">
                        <option value="">Chọn tầng</option>
                        @foreach($floors as $floor)
                            <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                        @endforeach
                    </select>
                    <span class="error font-size-normal text-danger" id="err_floor"></span>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close-modal" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="save_data">Lưu</button>
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
                    url: '{{ route('admin.user-admins.list') }}',
                    data : function(d) {
                        d.search = $('#search').val();
                        d.floor_id = $('#search_floor').val();
                        d.role = $('#search_role').val();
                    }
                },
                columns: [
                    { data: 'name', name: 'name',  class: 'align-middle', orderable: false },
                    { data: 'phone_number', name: 'phone_number',  class: 'align-middle', orderable: false },
                    { data: 'floor_name', name: 'floor_name',  class: 'align-middle', orderable: false },
                    { data: 'role_name', name: 'role_name',  class: 'align-middle', orderable: false },
                    { data: 'status', name: 'status',  class: 'align-middle', orderable: false },
                    {data: 'action', name: 'action', class: 'align-middle', orderable: false, searchable: false},
                ]
            });

            $('input[type=text]').on('keyup', function() {
                var inputValue = $(this).val();
                inputValue = inputValue.replace(/\s+/g, ' ');
                $(this).val(inputValue);
            });

            $('#search').on('keyup', function() {
                datatable.draw();
            });

            $('#search_floor').on('change', function() {
                datatable.draw();
            });

            $('#search_role').on('change', function() {
                datatable.draw();
            });
        });

        $(document).on('click', '.btn-close-modal', function () {
            $('#modal_item_admin').modal('hide');
        });

        $(document).on('change', '.switch-status', function() {
            var status = $(this).prop('checked') == true ? 0 : 1;
            var id = $(this).data('id');
            var datatable = $('#example').DataTable();
            $('.js-loading-mask').removeClass('is-remove');
            $.ajax({
                url: "user-admins/change-status?id="+id+'&status='+status,
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
            $('#modal_item_admin_label').html('THÊM MỚI');
            $('#item_admin_id').val('');
            $('#name').val('');
            $('#email').val('');
            $('#phone_number').val('');
            $('#role').val('1');
            $('floor_id').val('');
            $('#err_name').html('');
            $('#err_email').html('');
            $('#err_phone_number').html('');
            $('#modal_item_admin').modal('show');
        });

        $(document).on('click', '#save_data', function () {
            var id = $('#item_admin_id').val();
            var flag = true;
            var name = $('#name').val();
            var email = $('#email').val();
            var phone_number = $('#phone_number').val();
            var role = $('#role').val();
            var floor_id = $('#floor_id').val();

            if (name == '') {
                flag = false;
                $('#err_name').html('Tên không được để trống');
            } else {
                $('#err_name').html('');
            }

            if (email == '') {
                flag = false;
                $('#err_email').html('Email không được để trống');
            } else {
                $('#err_email').html('');
            }

            if (phone_number == '') {
                flag = false;
                $('#err_phone_number').html('Số điện thoại không được để trống');
            } else {
                $('#err_phone_number').html('');
            }

            if (role == 1 && floor_id != '') { //role admin
                flag = false;
                $('#floor_id').val('')
                $('#err_floor').html('Admin không cần chọn tầng');
            } else if (role == 2 && floor_id == '') { //role partner
                flag = false;
                $('#err_floor').html('Vui lòng chọn tầng');
            } else {
                $('#err_floor').html('');
            }

            if (flag == true) {
                $('.js-loading-mask').removeClass('is-remove');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.user-admins.save') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id,
                        name: name,
                        email: email,
                        phone_number: phone_number,
                        role: role,
                        floor: floor_id,
                    },
                    success: function(data) {
                        $('.js-loading-mask').addClass('is-remove');
                        $('#modal_item_admin').modal('hide');
                        $('#example').DataTable().draw();
                        toastr.success("Thêm mới thành công");
                    },
                    error: function(error) {
                        $('.js-loading-mask').addClass('is-remove');
                        toastr.error("Thêm mới thất bại");
                        $('#err_name').html(error && error.responseJSON.errors && error.responseJSON.errors && error.responseJSON.errors.name  ? error.responseJSON.errors.name : '');
                        $('#err_email').html(error && error.responseJSON.errors && error.responseJSON.errors && error.responseJSON.errors.email  ? error.responseJSON.errors.email : '');
                        $('#err_phone_number').html(error && error.responseJSON.errors && error.responseJSON.errors && error.responseJSON.errors.phone_number  ? error.responseJSON.errors.phone_number : '');
                        $('#err_role').html(error && error.responseJSON.errors && error.responseJSON.errors && error.responseJSON.errors.role  ? error.responseJSON.errors.role : '');
                    }
                });
            }
        });

        $(document).on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var phone_number = $(this).data('phone_number');
            var role = $(this).data('role');
            var floor_id = $(this).data('floor');

            $('#modal_item_admin_label').html('CHỈNH SỬA');
            $('#item_admin_id').val(id);
            $('#name').val(name);
            $('#email').val(email);
            $('#phone_number').val(phone_number);
            $('#floor_id').val(floor_id);
            $('#err_name').html('');
            $('#err_email').html('');
            $('#err_phone_number').html('');
            $('#role').val(role);
            $('#modal_item_admin').modal('show');
        });

        $(document).on('click', '.btn-delete', function () {
            var id = $(this).data('id');
             
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa tài khoản này?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.js-loading-mask').removeClass('is-remove');
                    $.ajax({
                        type: 'DELETE',
                        url: '{{ route('admin.user-admins.delete') }}',
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
</script>
@endpush