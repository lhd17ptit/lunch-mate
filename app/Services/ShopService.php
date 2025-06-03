<?php

namespace App\Services;

use App\Repositories\ShopRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ShopService
{
    protected $shopRepository;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function getList()
    {
        $data = $this->shopRepository->query();

        $data->orderBy('id', 'asc')->get();

        return DataTables::of($data)
        ->editColumn('status', function ($item) {
            return view('admin.common.action-status', ['item' => $item]);
        })
        ->addColumn('action', function ($item) {
            return '<a class="btn btn-danger btn-detail btn-sm mr-1" data-id="'.$item->id.'"><i class="fa fa-eye text-white"></i></a><a class="btn btn-danger btn-edit btn-sm mr-1" data-id="'.$item->id.'" data-title="'.$item->name.'"><i class="fa fa-wrench text-white"></i></a><a class="btn btn-danger btn-delete btn-sm" data-id="'.$item->id.'"><i class="fa fa-trash text-white"></i></a>';
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function save($data)
    {
        try {
            DB::beginTransaction();

            $dataCategory = [
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'status' => config('constants.ACTIVE'),
                'phone_number' => $data['phone_number'] ?? null,
                'note' => $data['note'] ?? null,
            ];

            // write code because render not support updateOrCreate $data['id'] = null
            if (!empty($data['id'])) {
                $this->shopRepository->updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    $dataCategory
                );
            } else {
                $this->shopRepository->create($dataCategory);
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Lưu thành công',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeStatus($id)
    {
        try {
            $shop = $this->shopRepository->find($id);
            if (!$shop) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            $shop->update([
                'status' => $shop->status == config('constants.ACTIVE') ? config('constants.INACTIVE') : config('constants.ACTIVE'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Câp nhật trạng thái thành công',
                'data' => $shop,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $shop = $this->shopRepository->find($id);
            if (!$shop) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            // if ($shop->admins->count() > 0 || $shop->users->count() > 0) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Không thể xóa danh mục này vì nó đang có dữ liệu liên quan',
            //     ], 400);
            // }

            $shop->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function detail($id)
    {
        $shop = $this->shopRepository->find($id);
        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy dữ liệu',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'view' => view('admin.shop.detail', ['data' => $shop])->render(),
        ], 200);
    }
}