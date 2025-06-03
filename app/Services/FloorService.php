<?php

namespace App\Services;

use App\Repositories\FloorRepository;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FloorService
{
    protected $floorRepository;

    public function __construct(FloorRepository $floorRepository)
    {
        $this->floorRepository = $floorRepository;
    }

    public function getList()
    {
        $data = $this->floorRepository->query();

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
                'status' => config('constants.ACTIVE'),
            ];

            // write code because render not support updateOrCreate $data['id'] = null
            if (!empty($data['id'])) {
                $this->floorRepository->updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    $dataCategory
                );
            } else {
                $this->floorRepository->create($dataCategory);
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
            $floor = $this->floorRepository->find($id);
            if (!$floor) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            $floor->update([
                'status' => $floor->status == config('constants.ACTIVE') ? config('constants.INACTIVE') : config('constants.ACTIVE'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Câp nhật trạng thái thành công',
                'data' => $floor,
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
            $floor = $this->floorRepository->find($id);
            if (!$floor) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            if ($floor->admins->count() > 0 || $floor->users->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể xóa danh mục này vì nó đang có dữ liệu liên quan',
                ], 400);
            }

            $floor->delete();

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
        $floor = $this->floorRepository->find($id);
        if (!$floor) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy dữ liệu',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'view' => view('admin.floor.detail', ['data' => $floor])->render(),
        ], 200);
    }
}