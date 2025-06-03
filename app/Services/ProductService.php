<?php

namespace App\Services;

use App\Libraries\Upload;
use App\Repositories\ProductColorRepository;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductSizeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductService
{
    const ROOT_PATH_STORAGE = '/products';

    protected $productRepository;
    protected $productImageRepository;
    protected $productSizeRepository;
    protected $productColorRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductImageRepository $productImageRepository,
        ProductSizeRepository $productSizeRepository,
        ProductColorRepository $productColorRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->productImageRepository = $productImageRepository;
        $this->productSizeRepository = $productSizeRepository;
        $this->productColorRepository = $productColorRepository;
    }

    public function getList($data)
    {
        $products = $this->productRepository->query();
        if (!empty($data['search'])) {
            $products->where('title', 'like', '%'.$data['search'].'%');
        }
        if (!empty($data['category'])) {
            $products->where('category_id', $data['category']);
        }
        if (isset($data['status'])) {
            $products->where('status', $data['status']);
        }
        $products->orderBy('id', 'desc')->get();

        return DataTables::of($products)
        ->editColumn('status', function ($item) {
            return view('admin.common.action-status', ['item' => $item]);
        })
        ->editColumn('category', function ($item) {
            return $item->category->title ?? 'N/A';
        })
        ->editColumn('price', function ($item) {
            return $item->price - ($item->price * $item->discount / 100);
        })
        ->addColumn('action', function ($item) {
            return '<a class="btn btn-danger btn-detail btn-sm mr-1" data-id="'.$item->id.'"><i class="fa fa-eye text-white"></i></a><a href="' . route('admin.products.detail', ['id' => $item->id]).'" class="btn btn-danger btn-edit btn-sm mr-1" data-id="'.$item->id.'"><i class="fa fa-wrench text-white"></i></a><a class="btn btn-danger btn-delete btn-sm" data-id="'.$item->id.'"><i class="fa fa-trash text-white"></i></a>';
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function getListMedias($data)
    {
        $products = $this->productImageRepository->findByField('product_id', $data['product_id'])->orderBy('position', 'asc')->get();

        return DataTables::of($products)
        ->addIndexcolumn()
        ->editColumn('media', function ($item) {
            $url = !empty($item->path) ? asset('storage'.$item->path) : asset('admin/images/no-image.png');
            if ($item->type == 1) {
                return '<img src="' . $url .'" style="width: 150px; height: 150px; object-fit: cover">';
            } else if ($item->type == 2) {
                return '<video src="' . $url .'" style="width: 150px; height: 150px; object-fit: cover" controls />';
            }
        })
        ->addColumn('position', function ($item) use ($products) {
            if ($item->position == $products->first()->position) {
                $result = '<span class="order-down" data-id="' . $item->id . '" data-position="' . $item->position . '"><i class="fa fa-chevron-down fa-1x text-dark"></i></span> ';
            } else if ($item->position == $products->last()->position) {
                $result = '<span class="order-up" data-id="' . $item->id . '" data-position="' . $item->position . '"><i class="fa fa-chevron-up fa-1x text-dark"></i></span>';
            } else {
                 $result = '<span class="order-up" data-id="' . $item->id . '" data-position="' . $item->position . '"><i class="fa fa-chevron-up fa-1x text-dark"></i></span>' . ' <br> ' .
                '<span class="order-down" data-id="' . $item->id . '" data-position="' . $item->position . '"><i class="fa fa-chevron-down fa-1x text-dark"></i></span> ';
            }
            return $result;
        })
        ->addColumn('action', function ($item) {
            return '<a class="btn btn-danger btn-delete-media btn-sm" data-id="'.$item->id.'"><i class="fa fa-trash text-white"></i></a>';
        })
        ->rawColumns(['media', 'position', 'action'])
        ->make(true);
    }

    public function save($data)
    {
        try {
            DB::beginTransaction();

            $productData = [
                'title' => $data['title'],
                'slug' => $data['slug'],
                'price' => $data['price'],
                'discount' => $data['discount'],
                'total' => $data['price'] - ($data['price'] * ($data['discount'] ?? 0) / 100),
                'description' => $data['description'],
                'category_id' => $data['category'],
                'status' => config('constants.ACTIVE'),
                'type' => 0,
                'source' => 0,
            ];
            
            if (!empty($data['id'])) {
                $product = $this->productRepository->updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    $productData
                );
            } else {
                $product = $this->productRepository->create($productData);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product saved successfully',
                'data' => $product,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeStatus($data)
    {
        try {
            DB::beginTransaction();
            $product = $this->productRepository->findOrFail($data['id']);
            if ($product) {
                $product->update(['status' => $product->status == config('constants.ACTIVE') ? config('constants.INACTIVE') : config('constants.ACTIVE')]);
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product status changed successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function delete($data)
    {
        try {
            DB::beginTransaction();
            $product = $this->productRepository->findOrFail($data['id']);
            if ($product) {
                $product->delete();
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveMedia($data)
    {
        try {
            DB::beginTransaction();
            if (!empty($data['media'])) {
                $lastMedia = $this->productImageRepository->findByField('product_id', $data['product_id'])->orderBy('position', 'desc')->first();

                $mimeType = $data['media']->getClientMimeType();
                $folder = self::ROOT_PATH_STORAGE . '/' . $data['product_id'];
                $fileName = Upload::storeFile($data['media'], $folder);

                $typeMap = ['image' => 1, 'video' => 2];
                $type = explode('/', $mimeType)[0];
                $typeValue = $typeMap[$type] ?? null;

                if (!empty($fileName)) {
                    $this->productImageRepository->create([
                        'product_id' => $data['product_id'],
                        'path' => $fileName,
                        'type' => $typeValue,
                        'position' => $lastMedia ? $lastMedia->position + 1 : 1,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Media saved successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function orderUpMedia($data)
    {
        try {
            $position = (int) $data['position'];
            $product = $this->productImageRepository->findByField('product_id', $data['product_id'])->where('position', '<', $position)->orderBy('position', 'desc')->first();
            if (!empty($product)) {
                $newPosition = (int) $product->position;
                DB::beginTransaction();
                $this->productImageRepository->find($product->id)->update(['position' => $position]);
                $this->productImageRepository->find($data['id'])->update(['position' => $newPosition]);
                DB::commit();
            }
            return response()->json([
                'message' => 'Order up media successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function orderDownMedia($data)
    {
        try {
            $position = (int) $data['position'];
            $product = $this->productImageRepository->findByField('product_id', $data['product_id'])->where('position', '>', $position)->orderBy('position', 'asc')->first();
            if (!empty($product)) {
                $newPosition = (int) $product->position;
                DB::beginTransaction();
                $this->productImageRepository->find($product->id)->update(['position' => $position]);
                $this->productImageRepository->find($data['id'])->update(['position' => $newPosition]);
                DB::commit();
            }
            return response()->json([
                'message' => 'Order down media successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteMedia($data)
    {
        try {
            DB::beginTransaction();
            $productImage = $this->productImageRepository->findOrFail($data['id']);
            if ($productImage) {
                Upload::deleteFile($productImage->path);
                $productImage->delete();
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Media deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveSize($data)
    {
        try {
            DB::beginTransaction();
            $productSize = $this->productSizeRepository->create([
                'product_id' => $data['product_id'],
                'size' => $data['name'],
            ]);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product size saved successfully',
                'data' => $productSize,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveColor($data)
    {
        try {
            DB::beginTransaction();
            $productColor = $this->productColorRepository->create([
                'product_id' => $data['product_id'],
                'color' => $data['name'],
            ]);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product color saved successfully',
                'data' => $productColor,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteSize($data)
    {
        try {
            DB::beginTransaction();
            $productSize = $this->productSizeRepository->findOrFail($data['id']);
            $productSize->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product size deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteColor($data)
    {
        try {
            DB::beginTransaction();
            $productColor = $this->productColorRepository->findOrFail($data['id']);
            $productColor->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Product color deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getListSize($data)
    {
        $products = $this->productSizeRepository->findByField('product_id', $data['product_id'])->orderBy('id', 'asc')->get();

        return DataTables::of($products)
        ->addIndexcolumn()
        ->addColumn('action', function ($item) {
            return '<a class="btn btn-danger btn-delete-size btn-sm" data-id="'.$item->id.'"><i class="fa fa-trash text-white"></i></a>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function getListColor($data)
    {
        $products = $this->productColorRepository->findByField('product_id', $data['product_id'])->orderBy('id', 'asc')->get();

        return DataTables::of($products)
        ->addIndexcolumn()
        ->addColumn('action', function ($item) {
            return '<a class="btn btn-danger btn-delete-color btn-sm" data-id="'.$item->id.'"><i class="fa fa-trash text-white"></i></a>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function getProductsByCategory($data)
    {
        $products = $this->productRepository->query()->where('status', config('constants.ACTIVE'));
        if (!empty($data['category_id'])) {
            $products->where('category_id', $data['category_id']);
        }
        if (!empty($data['search'])) {
            $products->where('title', 'like', '%'.$data['search'].'%');
        }
        if (!empty($data['price_min'])) {
            $products->where('total', '>=', $data['price_min']);
        }
        if (!empty($data['price_max'])) {
            $products->where('total', '<=', $data['price_max']);
        }
        if (!empty($data['color'])) {
            $products->whereHas('colors', function ($query) use ($data) {
                $query->where('color', $data['color']);
            });
        }

        if (!empty($data['order_by'])) {
            if ($data['order_by'] == 'price-asc') {
                $products->orderBy('total', 'asc');
            } else if ($data['order_by'] == 'price-desc') {
                $products->orderBy('total', 'desc');
            } else if ($data['order_by'] == 'latest') {
                $products->orderBy('id', 'desc');
            } else if ($data['order_by'] == 'oldest') {
                $products->orderBy('id', 'asc');
            }
        } else {
            $products->orderBy('id', 'desc');
        }

        return $products->paginate(20);
    }
}