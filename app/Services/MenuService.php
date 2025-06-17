<?php

namespace App\Services;

use App\Repositories\MenuItemRepository;
use App\Repositories\MenuRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MenuService
{
    protected $menuRepository;
    protected $menuItemRepository;

    public function __construct(
        MenuRepository $menuRepository,
        MenuItemRepository $menuItemRepository
    )
    {
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
    }

    public function getList($data)
    {
        $menus = $this->menuRepository->query();

        if (!empty($data['search_shop'])) {
            $menus->where('shop_id', $data['search_shop']);
        }
        if (!empty($data['search_date'])) {
            $menus->whereDate('created_at', $data['search_date']);
        }

        $menus->orderBy('id', 'desc')->get();

        return DataTables::of($menus)
        ->addColumn('shop', function ($item) {
            return $item->shop->name ?? 'Chưa xác định';
        })
        ->addColumn('created_at', function ($item) {
            return $this->getDayOfWeek($item->created_at);
        })
        ->editColumn('status', function ($item) {
            return view('admin.common.action-status', ['item' => $item]);
        })
        ->addColumn('action', function ($item) {
            return '<a class="btn btn-danger btn-detail btn-sm mr-1" data-id="'.$item->id.'"><i class="fa fa-eye text-white"></i></a>
            <a href="'.route('admin.menus.menu-items.index', $item->id).'" class="btn btn-danger btn-edit btn-sm mr-1" data-id="'.$item->id.'" data-title="'.$item->name.'"><i class="fa fa-wrench text-white"></i></a>
            <a class="btn btn-danger btn-delete btn-sm" data-id="'.$item->id.'"><i class="fa fa-trash text-white"></i></a>';
        })
        ->rawColumns(['shop', 'action', 'status'])
        ->make(true);
    }

    public function save($data)
    {
        try {
            DB::beginTransaction();

            $dataCategory = [
                'title' => $data['title'],
                'shop_id' => $data['shop'],
            ];

            // write code because render not support updateOrCreate $data['id'] = null
            if (!empty($data['id'])) {
                $this->menuRepository->updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    $dataCategory
                );
            } else {
                $this->menuRepository->create($dataCategory);
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
            $menu = $this->menuRepository->find($id);
            if (!$menu) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            $menu->update([
                'status' => $menu->status == config('constants.ACTIVE') ? config('constants.INACTIVE') : config('constants.ACTIVE'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Câp nhật trạng thái thành công',
                'data' => $menu,
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
            $menu = $this->menuRepository->find($id);
            if (!$menu) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            // if ($menu->admins->count() > 0 || $menu->users->count() > 0) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Không thể xóa danh mục này vì nó đang có dữ liệu liên quan',
            //     ], 400);
            // }

            $menu->delete();

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

    private function getDayOfWeek($date)
    {
        $dayOfWeeks = [
            'Monday' => 'Thứ hai',
            'Tuesday' => 'Thứ ba',
            'Wednesday' => 'Thứ tư',
            'Thursday' => 'Thứ năm',
            'Friday' => 'Thứ sáu',
            'Saturday' => 'Thứ bảy',
            'Sunday' => 'Chủ nhật',
        ];
        
        $l = Carbon::parse($date)->format('l');
        $day = Carbon::parse($date)->format('d/m/Y');
        
        return $dayOfWeeks[$l] . ', ' . $day;
    }

    public function detail($id)
    {
        $menu = $this->menuRepository->find($id);
        if (!$menu) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy dữ liệu',
            ], 404);
        }

        $items = [];
        $categories = [];
        if (!empty($menu->items)) {
            foreach ($menu->items as $item) {
                $categories[] = $item->foodItem->food_category_id;
            }
            $items = $menu->items->pluck('food_item_id')->toArray();
        }

        return response()->json([
            'view' => view('admin.menu.preview-menu-item', [
                'menu' => $menu,
                'foodCategories' => $categories,
                'foodItems' => $items,
            ])->render(),
        ], 200);
    }

    public function storeMenuItem($data)
    {
        try {
            DB::beginTransaction();

            $menu = $this->menuRepository->find($data['menu_id']);
            if (!$menu) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            if ($menu->items->count() > 0) {
                $menu->items()->delete();
            }

            $dataItems = [];
            foreach ($data['items'] as $item) {
                $dataItems[] = [
                    'menu_id' => $data['menu_id'],
                    'food_item_id' => $item,
                ];
            }

            $this->menuItemRepository->createMultiple($dataItems);

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
}