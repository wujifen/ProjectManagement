<?php 
namespace app\project\controller;
use app\project\model\User;

/**
 * 首页类
 * */
class IndexController extends BaseController
{
    public function index()
    {
        // 获取当前登录者的id
        $id = session('userId');

        // 获取当前登录的user用户
        $user = User::get($id);

        $this->assign('user', $user);
        
        return $this->fetch();
    }
}