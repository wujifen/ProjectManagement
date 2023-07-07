<?php 
namespace app\project\controller;
use think\Controller;
use app\project\model\User;

/**
 * 登录类
 * */
class LoginController extends Controller
{   
    /**
     * 登录界面
     * */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 注销
     * */
    public function logOut() 
    {
        if (User::logOut()) {
            return $this->success('注销成功', url('index'));
        } else {
            return $this->error('注销失败', url('index'));
        }
    }

    /**
     * 登录
     * */
    public function login()
    {
        // 接收提交的数据
        $username = input('post.username');
        $password = input('post.password');

        // 判断密码是否正确
        if (!User::login($username, $password)) {
            return $this->error('密码或用户名不正确', url('index'));
        } 
        return $this->success('登录成功', url('first/index'));
    }
}