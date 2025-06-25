@extends('admin.dashboard-layout')

@section('title', 'Menu ngày - ' .  date('d/m/Y', strtotime($menu->created_at)))

@section('content')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css" rel="stylesheet">

<div class="page-breadcrumb" style="margin-top: -30px;">
    <div class="row">
        <div class="col-5 align-self-center">
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb" style="font-size: 20px; font-weight: 600;">
                    Chi tiết menu ngày: {{ date('d/m/Y', strtotime($menu->created_at)) }}
                </nav>
            </div>
        </div>

    </div>
</div>
<input type="hidden" id="menu_id" value="{{ $menu->id }}">
<div class="mt-1 px-3 pt-2 pb-5" style="background: white">
    <div class="row mt-5">
        <div class="col-6 block-choose-food">
            <div class="text-center" style="font-size: 20px; font-weight: 600; text-decoration: underline;">Hôm nay có gì?</div>
            <div class="choose-food mt-3">
                @if (!empty($menu->foodCategories))
                    @foreach ($menu->foodCategories as $foodCategory)
                        <div class="form-check">
                            <input class="form-check-input food-category" type="checkbox" value="{{ $foodCategory->id }}" id="food-category-{{ $foodCategory->id }}" data-id="{{ $foodCategory->id }}" {{ in_array($foodCategory->id, $foodCategories) ? 'checked' : '' }}>
                            <label class="form-check-label" for="food-category-{{ $foodCategory->id }}">
                                {{ $foodCategory->name }}
                            </label>
                        </div>
                        <div class="row foood-item food-category-{{ $foodCategory->id }}" {{ in_array($foodCategory->id, $foodCategories) ? '' : 'd-none' }}>
                            @foreach ($foodCategory->foods as $food)
                                @if ($food->type == 1 && empty($typeOne))
                                    <div class="text-center col-10 mt-3">Món chính</div>
                                    @php
                                        $typeOne = true;
                                    @endphp
                                @elseif ($food->type == 2 && empty($typeTwo))
                                    <div class="text-center col-10 mt-3">Món phụ</div>
                                    @php
                                        $typeTwo = true;
                                    @endphp
                                @elseif ($food->type == 3 && empty($typeThree))
                                    <div class="text-center col-10 mt-3">Món rau</div>
                                    @php
                                        $typeThree = true;
                                    @endphp
                                @endif
                                <div class="form-check-item col-5">
                                    <input class="form-check-input food-item item-food-category-{{ $foodCategory->id }}" type="checkbox" value="{{ $food->id }}" id="food-item-{{ $food->id }}" data-id="{{ $food->id }}" {{ in_array($food->id, $foodItems) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="food-item-{{ $food->id }}">
                                        {{ $food->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif

                <div class="d-flex justify-content-center mt-4">
                    <div class="btn btn-primary btn-save-data">Lưu menu</div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="text-center" style="font-size: 20px; font-weight: 600; text-decoration: underline;">Chi tiết</div>
            <div class="preview-menu-today mt-3" id="preview_menu_today"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            getDataMenu();

            $('.food-category').click(function() {
                var status = $(this).prop('checked');
                var id = $(this).val();
                if (status) {
                    $('.food-category-' + id).removeClass('d-none');
                    $('.item-food-category-' + id).prop('checked', true);
                } else {
                    $('.food-category-' + id).addClass('d-none');
                    $('.item-food-category-' + id).prop('checked', false);
                }

                getDataMenu();
            });

            $('.food-item').click(function() {
                getDataMenu();
            });
        });

        $('.btn-save-data').click(function() {
            var menu_id = $('#menu_id').val();
            var categories = [];
            var items = [];

            $('.food-category').each(function() {
                if ($(this).prop('checked')) {
                    var id = $(this).val();
                    categories.push(id);
                }
            });
            
            $('.food-item').each(function() {
                if ($(this).prop('checked')) {
                    var id = $(this).val();
                    items.push(id);
                }
            });            

            if (categories.length > 0 && items.length > 0) {
                $('.js-loading-mask').removeClass('is-remove');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.menus.menu-items.store') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        categories: categories,
                        items: items,
                        menu_id: menu_id,
                    },
                    success: function(data) {
                        $('.js-loading-mask').addClass('is-remove');
                        toastr.success("Lưu thành công");

                        setTimeout(() => {
                            window.location.href = '{{ route('admin.menus.index') }}';
                        }, 2000);
                    },
                    error: function(error) {
                        $('.js-loading-mask').addClass('is-remove');
                        toastr.eror("Lưu thất bại");
                    }
                });
            }
            
        });

        function getDataMenu() {
            var menu_id = $('#menu_id').val();
            var categories = [];
            var items = [];
            $('.food-category').each(function() {
                if ($(this).prop('checked')) {
                    var id = $(this).val();
                    categories.push(id);
                }
            });

            $('.food-item').each(function() {
                if ($(this).prop('checked')) {
                    var id = $(this).val();
                    items.push(id);
                }
            });

            if (categories.length > 0 && items.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.menus.menu-items.preview') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        categories: categories,
                        items: items,
                        menu_id: menu_id,
                    },
                    success: function(data) {
                       $('#preview_menu_today').html(data.view)
                    },
                    error: function(error) {
                        //
                    }
                });
            } else {
                $('#preview_menu_today').html('');
            }
        }
    </script>
@endpush