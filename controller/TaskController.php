<?php 
namespace app\project\controller;
use app\project\model\Task;
use think\Request;
use app\project\model\Project;
use app\project\model\User;
use app\project\model\Detail;
use app\project\service\UserService;
/**
 * 任务类
 * */
class TaskController extends BaseController
{
    public function add()
    {
        // 获取项目id信息
        $projectId = Request::instance()->param('projectId');
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id', url('task/index'));
        }
        $project = Project::get($projectId);

        $this->assign('project', $project);

        return $this->fetch();
    }

    public function edit()
    {
        // 获取项目id信息
        $projectId = Request::instance()->param('projectId/d');
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id', url('task/index'));
        }
        $project = Project::get($projectId);

        // 获取任务的Id
        $taskId = Request::instance()->param('taskId/d');
        if (is_null($taskId)) {
            return $this->error('未获取到任务Id', url('task/index'));
        }
        $task = Task::get($taskId);

        $this->assign('task', $task);
        $this->assign('project', $project);

        return $this->fetch();
    }

    /**
     * 添加事件
     * */
    public function event()
    {
        // 获取项目的Id
        $projectId = Request::instance()->param('projectId');
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id', url('task/index'));
        }

        $this->assign('projectId', $projectId);

        return $this->fetch();
    }

    /**
     * 查看详情，状态的更改情况
     * */
    public function detail()
    {
        $taskId = Request::instance()->param('taskId/d');
        $task = Task::get($taskId);
        $details =Detail::where('task_id', $taskId)->select();

        $this->assign('details', $details);
        return $this->fetch();
    }

    public function saveEvent()
    {
        $projectId = Request::instance()->param('projectId');
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id', url('task/index'));
        }
        $event = input('event');

        if (empty($event)) {
            return $this->success('添加事件失败', url('index?id=' . $projectId));
        }
         return $this->error('添加事件成功', url('index?id=' . $projectId));
    }

    public function index()
    {   
        // 获取项目projectId
        $projectId =Request::instance()->param('id');
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id', url('project/index'));
        }
        $project = Project::get($projectId);

        // 获取搜索框信息
        $name = Request::instance()->get('title'); 
        $value = Request::instance()->get('status');

        // 定制查询条件
        if (false !== $status = Task::getSeachAttr($value)) {
            $tasks = $project->tasks()->where('name','like','%' . $name . '%')->where('status',$status );
            $tasks = $project->tasks()->paginate(10, false, ['query'=> ['name' =>$name,'status' => $status]]);
        } else {
            $tasks = $project->tasks()->where('name','like','%' . $name . '%');
            $tasks = $project->tasks()->paginate(10, false, ['query'=> ['name' =>$name]]);
        }
        
        $this->assign('projectId', $projectId);
        $this->assign('tasks', $tasks);

        return $this->fetch();
    }

    public function save() 
    {   
        // 获取项目的Id
        $projectId = Request::instance()->param('projectId/d');
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id', url('project/index'));
        }

        // 实例化任务并赋值
        $task = new Task();
        $task->name = input('post.name');
        $task->start_date = input('startDate');
        $task->end_date = input('endDate');
        $task->status = input('status');
        $task->user_id = input('userId/d');
        if (is_null($task)) {
            return $this->error('未获取到需要保存的任务', url('task/index'));
        }
        if ($task->user_id === 0) {
            return $this->error('未选择任务负责人', url('add?projectId=' . $projectId));
        }

        $task->project_id = $projectId;

        if (!$task->validate()->save()) {
            return $this->error('添加失败' . $this->getError(), url('index?id=' . $projectId));
        }
        return $this->success('添加成功', url('index?id=' . $projectId));
    }

    public function update()
    {
        // 获取项目的id
        $projectId = Request::instance()->param('projectId/d');
        if (is_null($projectId)) {
            return $this->error('未获取到项目的Id', url('task/index'));
        }

        // 获取任务的Id
        $taskId = input('taskId');
        $task = Task::get($taskId);
        if (is_null($task)) {
            return $this->error('未能传入任务', url('task/index'));
        }

        $task->user_id = input('userId/d');
        if ($task->user_id === 0) {
            return $this->error('未选择任务负责人', url('edit?projectId=' . $projectId . '?taskId=' . $taskId));
        }

        $status = input('status/d');

        // 记录是否有人更该了状态
        if ($task->getData('status') !== $status) {
            $detail = new Detail();
            $detail->task_id =  $taskId;
            $detail->user_id = UserService::getCurrentUserId();
            $detail->status_old = $task->getData('status');
            $detail->status_new = $status;
            if (!$detail->validate()->save()) {
                return $this->error('状态更改失败' . $detail->getError(), url('index?id=' . $projectId));
            }
        }

        $task->name = input('name');
        $task->start_date = input('startDate');
        $task->end_date = input('endDate');
        $task->status = $status;
        $task->project_id = $projectId;

        if(!$task->validate()->save()) {
            return $this->error('更改失败' . $task->getError());
        }

        return $this->success('更改成功', url('index?id=' . $projectId));
    }
}