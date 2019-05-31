<?php

namespace App\Http\Controllers\System;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OperationLog;

class RoleController
{
    /**
     * 创建角色
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->input();
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = DB::table('ibiart_slms_roles')->insertGetId($data);

        $result = $id ? Role::where('id', '!=', $id)->update(['is_default' => 0]) : false;

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '创建角色');
        }

        return $result ? response()->json(['result' => true], 200) : response()->json(['result' => false], 200);
    }

    /**
     * 获取权限列表
     *
     * @return JsonResponse
     */
    public function getRoles()
    {
        $roles = Role::all()->toArray();
        $is_loading = [
            'is_loading' => false
        ];

        array_walk($roles, function (&$value, $key, $is_loading) {
            $value = array_merge($value, $is_loading);
        }, $is_loading);

        return response()->json(['result' => $roles], 200);
    }

    public function setRoleMenus(Request $request)
    {
        $insertArr = [];
        $roleId = $request->input('roleid');

        $selected = $request->input('selected');
        foreach ($selected as $k => $row) {
            $insertArr[] = [
                'role_id' => $roleId,
                'menu_id' => $row['id'],
                'checked' => $row['checked']
            ];
        }

        DB::table('ibiart_slms_role_menus')->where('role_id', $roleId)->delete();
        $result = DB::table('ibiart_slms_role_menus')->insert($insertArr);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '设置菜单权限');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 设置默认角色
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setDefaultRole(Request $request)
    {
        $data = $request->input();

        $setDefaultRes = Role::where('id', $data['id'])->update(['is_default' => $data['is_default']]);

        if (!$setDefaultRes) {
            $result = false;
        } else {
            $result = Role::where('id', '!=', $data['id'])->update(['is_default' => 0]);
            $result = $result ? true : false;
        }

        return response()->json(['result' => $result], 200);
    }
}
