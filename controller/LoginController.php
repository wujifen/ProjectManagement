<?php 
namespace app\project\controller;
use think\Controller;
use app\project\model\User;

class LoginController extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    // 注销
    public function logOut() 
    {
        if(User::logOut()) {
            return $this->success('logOut success', url('index'));
        } else {
            return $this->error('logOut error', url('index'));
        }
    }

    public function login()
    {
        // 接收提交的数据
        $username = input('post.username');
        $password = input('post.password');

        // 判断密码是否正确
        if (!User::login($username, $password)) {
            return $this->error('username or password incorrect', url('index'));
        } 
        return $this->success('login success', url('first/index'));
    }
}