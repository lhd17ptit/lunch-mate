<?php

namespace App\Services;

use App\Libraries\Upload;
use App\Repositories\BannerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BannerService
{
    const ROOT_PATH_STORAGE = '/banners';
    
    protected $bannerRepository;

    public function __construct(BannerRepository $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
    }

    public function getList()
    {
        $data = $this->bannerRepository->query();

        $data->orderBy('id', 'asc')->get();

        return DataTables::of($data)
        ->addIndexColumn()
        ->editColumn('image', function ($item) {
            $url = !empty($item->path) ? asset('storage'.$item->path) : asset('admin/images/no-image.png');
            return '<img src="' . $url .'" style="width: 100px; height: 100px; object-fit: cover">';
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

            $bannerData = [
                'title' => $data['title'],
                'sub_title' => $data['sub_title'],
                'status' => config('constants.ACTIVE'),
            ];
           
            if (!empty($data['id'])) {
                $banner = $this->bannerRepository->updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    $bannerData
                );
            } else {
                $banner = $this->bannerRepository->create($bannerData);
            }

            if (!empty($data['image'])) {
                $id = !empty($banner) ? $banner->id : '';
                $folder = self::ROOT_PATH_STORAGE . '/' . $id;
                $fileName = Upload::storeFile($data['image'], $folder);
                if ($fileName) {
                    $banner->update([
                        'path' => $fileName,
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Banner saved successfully',
                'data' => $banner,
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
        $banner = $this->bannerRepository->find($id);
        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'view' => view('admin.banner.detail', ['data' => $banner])->render(),
        ], 200);
    }

    public function edit($id)
    {
        try {
            $banner = $this->bannerRepository->find($id);
            if (!$banner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Banner not found',
                ], 404);
            }

            $viewData = [
                'id' => $banner->id,
                'image_url' => asset('storage'.$banner->path),
                'title' => $banner->title,
                'sub_title' => $banner->sub_title,
                'status' => $banner->status,
            ];

            return response()->json([
                'message' => 'Success',
                'banner' => $viewData,
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
            $banner = $this->bannerRepository->find($id);
            if (!$banner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Banner not found',
                ], 404);
            }

            $banner->update([
                'status' => $banner->status == config('constants.ACTIVE') ? config('constants.INACTIVE') : config('constants.ACTIVE'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Banner status updated successfully',
                'data' => $banner,
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
            $banner = $this->bannerRepository->find($id);
            if (!$banner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Banner not found',
                ], 404);
            }
            Upload::deleteFile($banner->path);
            $banner->delete();

            return response()->json([
                'status' => true,
                'message' => 'Banner deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}