<?php 
namespace app\project\controller;
use think\Controller;
use app\project\model\User;

/**
 * 基类
 * */
class BaseController extends Controller
{
    public function __construct()
    {
        // 调用父类的构造器
        parent::__construct();

        // 判断是否已登录
        if (!User::isLogin()) {
            return $this->error('未登录', url('Login/index'));
        }
    }
}