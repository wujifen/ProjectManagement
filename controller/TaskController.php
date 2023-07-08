<?php 
namespace app\project\controller;
use app\project\model\Task;
use think\Request;
/**
 * 任务类
 * */
class TaskController extends BaseController
{
    public function add()
    {
        return $this->fetch();
    }

    public function edit()
    {
        return $this->fetch();
    }

    public function event()
    {
        return $this->fetch();
    }

    public function detail()
    {
        return $this->fetch();
    }

    public function index()
    {   
        // 接收搜索框里输入的信息
        $name = Request::instance()->get('title'); 
        $status = Request::instance()->get('status');

        $task = new Task();

        // 定制查询信息
        if (!empty($name)) {
            $task->where('name','like','%' . $name . '%');
        }

        // 条件查询并调用分页
        $tasks = $task->paginate(5, false, ['query'=> ['name' =>$name]]);

        $this->assign('tasks', $tasks);

        return $this->fetch();
    }
}