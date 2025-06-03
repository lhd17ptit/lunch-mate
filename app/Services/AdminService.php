<?php

namespace App\Services;

use App\Mail\InviteAdminMail;
use App\Models\Admin;
use App\Repositories\AdminFloorRepository;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class AdminService
{
    protected $adminRepository;
    protected $adminFloorRepository;

    public function __construct(
        AdminRepository $adminRepository,
        AdminFloorRepository $adminFloorRepository
    )
    {
        $this->adminRepository = $adminRepository;
        $this->adminFloorRepository = $adminFloorRepository;
    }

    public function getList($data)
    {
        $admins = $this->adminRepository->query()
        ->when(!empty($data['search']), function ($query) use ($data) {
            return $query->where('name', 'like', '%'.$data['search'].'%');
        })
        ->when(!empty($data['role']), function ($query) use ($data) {
            return $query->where('role', $data['role']);
        })
        ->when(!empty($data['floor_id']), function ($query) use ($data) {
            return $query->whereHas('floor',function ($query) use ($data) {
                return $query->where('floors.id', $data['floor_id']);
            });
        });
        $admins->orderBy('id', 'asc')->get();

        return DataTables::of($admins)
            ->editColumn('role_name', function ($item) {
                return $item->role == Admin::ADMIN ? 'Quản trị viên' : 'Đối tác';
            })
            ->editColumn('phone_number', function ($item) {
                return $item->phone_number ?? 'Chưa cập nhật';
            })
            ->editColumn('floor_name', function ($item) {
                if ($item->role == Admin::ADMIN) {
                    return 'Tất cả';
                }
                return $item->single_floor->name ?? 'Chưa cập nhật';
            })
            ->editColumn('status', function ($item) {
                return view('admin.common.action-status', ['item' => $item]);
            })
            ->addColumn('action', function ($item) {
                return '<a class="btn btn-danger btn-detail btn-sm mr-1" data-id="'.$item->id.'"><i class="fa fa-eye text-white"></i></a><a class="btn btn-danger btn-edit btn-sm mr-1" data-id="'.$item->id.'" data-name="'.$item->name.'" data-email="'.$item->email.'"  data-phone_number="'.$item->phone_number.'" data-role="'.$item->role.'" data-floor="'.($item->single_floor->id ?? '').'"><i class="fa fa-wrench text-white"></i></a><a class="btn btn-danger btn-delete btn-sm" data-id="'.$item->id.'"><i class="fa fa-trash text-white"></i></a>';
            })
            ->rawColumns(['role_name', 'floor_name', 'action', 'status'])
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
                'role' => $data['role'],
                'password' => Hash::make($password),
                'status' => config('constants.ACTIVE'),
            ];

            // write code because render not support updateOrCreate $data['id'] = null
            if (!empty($data['id'])) {
                $admin = $this->adminRepository->updateOrCreate(
                    [
                        'id' => $data['id'],
                    ],
                    $dataCategory
                );
            } else {
                $admin = $this->adminRepository->create($dataCategory);
            }

            if (!empty($data['floor'])) {
                $this->adminFloorRepository->UpdateOrCreate(
                    [
                        'admin_id' => $admin->id,
                    ],
                    [
                        'floor_id' => $data['floor'],
                    ]
                );
            } else {
                $this->adminFloorRepository->findByField('admin_id', $admin->id)->delete();
            }

            Mail::to($admin->email)->send(new InviteAdminMail($admin, $password));
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
            $admin = $this->adminRepository->find($id);
            if (!$admin) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            $admin->update([
                'status' => $admin->status == config('constants.ACTIVE') ? config('constants.INACTIVE') : config('constants.ACTIVE'),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Câp nhật trạng thái thành công',
                'data' => $admin,
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
            $admin = $this->adminRepository->find($id);
            if (!$admin) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy dữ liệu',
                ], 404);
            }

            $admin->floor()->detach();
            $admin->delete();

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
}