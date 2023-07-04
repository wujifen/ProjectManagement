<?php 
namespace app\test\controller;
use think\Controller;

class ProjectController extends Controller
{
    public function index()
    {
        
        return $this->fetch();
    }
}