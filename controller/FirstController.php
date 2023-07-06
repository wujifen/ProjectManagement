<?php 
namespace app\project\controller;
use app\project\model\User;

class FirstController extends IndexController
{
    public function index()
    {
        $id = session('userId');
        $user = User::get($id);
        $this->assign('user', $user);
        return $this->fetch();
    }
}