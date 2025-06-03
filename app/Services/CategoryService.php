<?php

namespace App\Services;

use App\Libraries\Upload;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryService
{
    const ROOT_PATH_STORAGE = '/categories';
    
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getList()
    {
        $data = $this->categoryRepository->query();

        $data->orderBy('id', 'asc')->get();

        return DataTables::of($data)
        ->editColumn('image', function ($item) {
            $url = !empty($item->path) ? asset('storage'.$item->path) : asset('admin/images/no-image.png');
            return '<img src="' . $url .'" style="width: 60px; height: 60px; object-fit: cover">';
        })
        ->editColumn('status', function ($item) {
            return view('admin.common.action-status', ['item' => $item]);
        })
        ->addColumn('action', function ($item) {
            return '<a class="btn btn-danger btn-detail btn-sm mr-1" data-id="'.$item->id.'"><i class="fa fa-eye text-white"></i></a><a class="btn btn-danger btn-edit btn-sm mr-1" data-id="'.$item->id.'"><i class="fa fa-wrench text-white"></i></a><a class="btn btn-danger btn-delete btn-sm" data-id="'.$item->id.'"><i class="fa fa-trash text-white"></i></a>';
        })
        ->rawColumns(['image', 'action', 'status'])
        ->make(true);
    }

    public function save($data)
    {
        try {
            DB::beginTransaction();

            $dataCategory = [
                'title' => $data['title'],
                'slug' => $data['slug'],
                'sub_title' => $data['sub_title'],
                'description' => $data['description'],
                'status' => config('constants.ACTIVE'),
            ];

            if (!empty($data['id'])) {
                $category = $this->categoryRepository->updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    $dataCategory
                );
            } else {
                $category = $this->categoryRepository->create($dataCategory);
            }

            if (!empty($data['image'])) {
                $id = !empty($category) ? $category->id : '';
                $folder = self::ROOT_PATH_STORAGE . '/' . $id;
                $fileName = Upload::storeFile($data['image'], $folder);
                if ($fileName) {
                    $category->update([
                        'path' => $fileName,
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Category saved successfully',
                'data' => $category,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function detail($id)
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'view' => view('admin.category.detail', ['data' => $category])->render(),
        ], 200);
    }

    public function edit($id)
    {
        try {
            $category = $this->categoryRepository->find($id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            $viewData = [
                'id' => $category->id,
                'image_url' => asset('storage'.$category->path),
                'title' => $category->title,
                'slug' => $category->slug,
                'sub_title' => $category->sub_title,
                'description' => $category->description,
                'status' => $category->status,
            ];

            return response()->json([
                'message' => 'Success',
                'category' => $viewData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeStatus($id)
    {
        try {
            $category = $this->categoryRepository->find($id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            $category->update([
                'status' => $category->status == config('constants.ACTIVE') ? config('constants.INACTIVE') : config('constants.ACTIVE'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Category status updated successfully',
                'data' => $category,
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
            $category = $this->categoryRepository->find($id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            if (!empty($category->products) && $category->products->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category has products, cannot delete',
                ], 400);
            }

            Upload::deleteFile($category->path);
            $category->delete();

            return response()->json([
                'status' => true,
                'message' => 'Category deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}