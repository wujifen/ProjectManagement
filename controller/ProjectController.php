<?php 
namespace app\project\controller;

/**
 * 项目类
 * */
class ProjectController extends IndexController
{
    public function index()
    {
        
        return $this->fetch();
    }
    public function add()
    {
        
        return $this->fetch();
    }
    public function tojoin()
    {
        
        return $this->success('你已成功加入', url('task/index'));
    }
    public function demo()
    { 
        return $this->fetch();
    }
}