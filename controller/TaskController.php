<?php 
namespace app\project\controller;
use app\project\model\Task;
use think\Request;
use app\project\model\Project;
use app\project\model\User;
use app\project\model\Detail;
/**
 * 任务类
 * */
class TaskController extends BaseController
{
    public function add()
    {
        $task = new Task();
        $task->id = 0;
        $task->name = '';
        $task->start_date = '';
        $task->end_date = '';
        $task->status = '';
        $task->user_id = 0;
        $task->project_id = 0;

        // 获取项目id信息
        $projectid = Request::instance()->param('id');
        $project = Project::get($projectid);
        $this->assign('task', $task);
        $this->assign('project', $project);
        return $this->fetch('edit');
    }

    public function edit()
    {
        // 获取项目id信息
        $projectid = Request::instance()->param('id/d');
        $project = Project::get($projectid);

        $taskId = Request::instance()->param('taskId/d');

        $task = Task::get($taskId);
        $this->assign('task', $task);
        $this->assign('project', $project);

        return $this->fetch();
    }

    public function event()
    {
        return $this->fetch();
    }

    public function detail()
    {
        $id = Request::instance()->param('id/d');
        $task = Task::get($id);
        $details = $task->details();
        return $this->fetch('details', $details);
    }

    public function saveEvent()
    {
        $event = input('event');

        if (empty($event)) {
            return $this->success('添加事件失败', url('index'));
        }
         return $this->error('添加事件成功', url('index'));
    }

    public function index()
    {   // 获取项目id
        $id =Request::instance()->param('id');
        $project = Project::get($id);

        // 获取搜索框信息
        $name = Request::instance()->get('title'); 
        $value = Request::instance()->get('status');

        // 定制查询条件
        if (false !== $status = Task::getSeachAttr($value)) {
            $tasks = $project->tasks()->where('name','like','%' . $name . '%')->where('status',$status )->select();
        } else {
            $tasks = $project->tasks()->where('name','like','%' . $name . '%')->select();
        }
        
        $this->assign('id', $id);
        $this->assign('tasks', $tasks);

        return $this->fetch();
    }

    public function save() 
    {   
        $projectid = Request::instance()->param('projectid/d');
        
        $task = new Task();
        $task->name = input('post.name');
        $task->start_date = input('start_date');
        $task->end_date = input('status');
        $task->user_id = input('user_id');
        $task->project_id = $projectid;

        if (!$task->validate()->save()) {
            return $this->error('添加失败' . $this->getError(), url('index'));
        }
        return $this->success('添加成功', url('index'));
    }

    public function update()
    {
        $taskid = Request::instance()->param('userid');
        $taskOld = Task::get($taskid);

        $projectid = Request::instance()->param('id');

        $task = new Task();
        $task->name = input('name');
        $task->status = input('status');
        $task->start_date = input('startDate');
        $task->end_date = input('endDate');
        $task->user_id = input('userId');
        $task->project_id = $projectid;

        if ($taskOld->getData('status') !== $task->getSeachAttr($task->status)) {
            // 记录xx人更改了状态
            $detail = new Detail();
            $detail->user_id = session('userId');
            $detail->status_old = $taskOld->getData('status');
            $detail->status_new = $task->getSeachAttr($task->status);
            if (!$detail->validate()->save()) {
                return $this->error('状态更改失败' . $this->getError(), url('index'));
            }
        }
        if(!$task->validate()->save()) {
            return $this->error('更改失败' . $this->getError());
        }
        return $this->success('更改成功', url('index'));
    }
}