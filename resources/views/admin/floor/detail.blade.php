<div class="form-group position-relative">
    <label for="title" class="mb-0">Tên:</label> {{ $data->name ?? 'N/A'}}
</div>

<table class="table-list-admin mt-3">
    <thead>
        <tr>
            <td colspan="2" class="text-center"><strong>Người quản lý</strong></td>
        </tr>
    </thead>
    <thead>
        <tr>
            <th>Name</th>
            <th>Số điện thoại</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data->admins as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->phone_number }}</td>
            </tr>
        @endforeach
    </tbody>
</table>