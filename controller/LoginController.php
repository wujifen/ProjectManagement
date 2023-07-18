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
     * 新用户注册
     * */
    public function add()
    {
        // 实例化User对象并赋值
        $user = new User();
        $user->id = 0;
        $user->name = '';
        $user->username = '';
        $user->password = '';
        $user->sex = 0;

        $this->assign('user', $user);
        return $this->fetch('user/edit');
    }

    /**
     * 保存add提交的数据
     * */
    public function save()
    {
        // 实例化User对象并赋值
        $user = new User();
        $user->name = input('post.name');
        $username = input('post.username');
        $count = $user->where('username', $username)->select();
        if (!empty($count)) {
            return $this->error('添加失败,用户名已被注册');
        }
        $user->username = input('post.username');
        $user->password = input('post.password');
        $user->sex = input('post.sex/d');

        // 新增对象至数据表
        if (!$user->validate()->save()) {
            return $this->error('添加失败' . $user->getError());
        }

        // 反馈结果
        return $this->success('添加成功', url('login/index'));
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
        // 接收登录提交的数据
        $username = input('post.username');
        $password = input('post.password');

        // 判断密码是否正确
        if (!User::login($username, $password)) {
            return $this->error('密码或用户名不正确', url('index'));
        } 
        return $this->success('登录成功', url('index/index'));
    }
}