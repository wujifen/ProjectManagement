<?php 
namespace app\project\controller;
use think\Controller;
use app\project\model\User;

class IndexController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!User::isLogin()) {
			return $this->error('未登录', url('Login/index'));
		}
	}
}