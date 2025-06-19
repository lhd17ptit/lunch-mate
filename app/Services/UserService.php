<?php

namespace App\Services;

use App\Mail\InviteUserMail;
use App\Models\Admin;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getList($data)
    {
        $admin = auth()->guard('admin')->user();
        $floorAdmin = $admin->role == Admin::PARTNER ? ($admin->single_floor ?? null) : null;

        $users = $this->userRepository->query()
        ->when(!empty($data['search']), function ($query) use ($data) {
            return $query->where('name', 'like', '%'.$data['search'].'%');
        })
        ->when(!empty($data['floor_id']), function ($query) use ($data) {
            return $query->where('floor_id', $data['floor_id']);
        })
        ->when(!empty($floorAdmin), function ($query) use ($floorAdmin) {
            return $query->where('floor_id', $floorAdmin->id);
        });
        $users->orderBy('id', 'asc')->get();

        return DataTables::of($users)
        ->editColumn('floor_name', function ($item) {
            return $item->floor ? $item->floor->name : 'Chưa cập nhật';
        })
        ->editColumn('email', function ($item) {
            return $item->email ?? 'Chưa cập nhật';
        })
        ->editColumn('phone_number', function ($item) {
            return $item->phone_number ?? 'Chưa cập nhật';
        })
        ->editColumn('status', function ($item) {
            return view('admin.common.action-status', ['item' => $item]);
        })
        ->addColumn('action', function ($item) {
            return '<a class="btn btn-danger btn-detail btn-sm mr-1" data-id="'.$item->id.'"><i class="fa fa-eye text-white"></i></a><a class="btn btn-danger btn-edit btn-sm mr-1" data-id="'.$item->id.'" data-name="'.$item->name.'" data-email="'.$item->email.'"  data-phone_number="'.$item->phone_number.'" data-floor="'.($item->floor_id ?? '').'"><i class="fa fa-wrench text-white"></i></a><a class="btn btn-danger btn-delete btn-sm" data-id="'.$item->id.'"><i class="fa fa-trash text-white"></i></a>';
        })
        ->rawColumns(['floor_name', 'action', 'status'])
        ->make(true);
    }

    public function save($data)
    {
        try {
            DB::beginTransaction();

            $password = rand(10000000, 99999999);

            $dataCategory = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'floor_id' => $data['floor'] ?? null,
                'status' => config('constants.ACTIVE'),
                'password' => Hash::make($password),
            ];

            // write code because render not support updateOrCreate $data['id'] = null
            if (!empty($data['id'])) {
                $user = $this->userRepository->updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    $dataCategory
                );
            } else {
                $user = $this->userRepository->create($dataCategory);
            }

            // Mail::to($user->email)->send(new InviteUserMail($user, $password));
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
            $user = $this->userRepository->find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            $user->update([
                'status' => $user->status == config('constants.ACTIVE') ? config('constants.INACTIVE') : config('constants.ACTIVE'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Câp nhật trạng thái thành công',
                'data' => $user,
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
            $user = $this->userRepository->find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            $user->delete();

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
        $user = $this->userRepository->find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy dữ liệu',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'view' => view('admin.floor.detail', ['data' => $user])->render(),
        ], 200);
    }
}