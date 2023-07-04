<?php 
namespace app\test\controller;
use think\Controller;

class LoginController extends Controller
{
    public function index()
    {
        
        return $this->fetch();
    }
}