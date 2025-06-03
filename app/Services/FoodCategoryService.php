<?php

namespace App\Services;

use App\Repositories\FoodCategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class FoodCategoryService
{
    protected $foodCategoryRepository;

    public function __construct(FoodCategoryRepository $foodCategoryRepository)
    {
        $this->foodCategoryRepository = $foodCategoryRepository;
    }

    public function getList()
    {
        $data = $this->foodCategoryRepository->query();

        $data->whereHas('shop');

        $data->orderBy('id', 'asc')->get();

        return DataTables::of($data)
        ->editColumn('shop', function ($item) {
            return $item->shop ? $item->shop->name : 'Chưa xác định';
        })
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
                'key' => Str::slug($data['name']),
                'price' => $data['price'] ?? 0,
                'note' => $data['note'] ?? null,
                'shop_id' => $data['shop_id'] ?? null,
                'status' => config('constants.ACTIVE'),
            ];

            // write code because render not support updateOrCreate $data['id'] = null
            if (!empty($data['id'])) {
                $this->foodCategoryRepository->updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    $dataCategory
                );
            } else {
                $this->foodCategoryRepository->create($dataCategory);
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
            $foodCategory = $this->foodCategoryRepository->find($id);
            if (!$foodCategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            $foodCategory->update([
                'status' => $foodCategory->status == config('constants.ACTIVE') ? config('constants.INACTIVE') : config('constants.ACTIVE'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Câp nhật trạng thái thành công',
                'data' => $foodCategory,
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
            $foodCategory = $this->foodCategoryRepository->find($id);
            if (!$foodCategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            // if ($foodCategory->admins->count() > 0 || $foodCategory->users->count() > 0) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Không thể xóa danh mục này vì nó đang có dữ liệu liên quan',
            //     ], 400);
            // }

            $foodCategory->delete();

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
        $foodCategory = $this->foodCategoryRepository->find($id);
        if (!$foodCategory) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy dữ liệu',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'view' => view('admin.foodCategory.detail', ['data' => $foodCategory])->render(),
        ], 200);
    }
}