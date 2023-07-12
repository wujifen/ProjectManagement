<?php 
namespace app\project\service;
use app\project\model\User;
use think\Request;
use think\Db;

/**
 *用户处理业务逻辑的service类
 * */
class UserService {
    /**
     * 获取当前登录用户
     * @return 登录了返回$currentUserId ，否则返回空
     * */
    static public function getCurrentUserId()
    {
        $currentUserId = session('userId');
        return $currentUserId !== '' ? $currentUserId : '';
    }

    /**
     * 默认列表的查询
     * */
    static public function indexlist()
    {
        // 获取查询信息
        $name = Request::instance()->get('name');
        $username = Request::instance()->get('username');

        // 设置每页大小
        $pageSize = 5;

        // 实例化User
        $user = new User();

        // 定制查询信息
        if (!empty($name)) {
            $user->where('name','like','%' . $name . '%');
        }

        if (!empty($username)) {
            $user->where('username', 'like', '%' . $username . '%');
        }

        // 条件查询并调用分页
        $users = $user->paginate($pageSize, false, ['query'=> ['name' =>$name,'username' => $username]]);

        // 反馈结果
        return $users; 
    }
}