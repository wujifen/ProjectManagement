<?php 
namespace app\test\controller;
use think\Controller;

class TaskController extends Controller
{
    public function index()
    {
        
        return $this->fetch();
    }
}