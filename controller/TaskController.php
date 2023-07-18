<?php 
namespace app\project\controller;
use app\project\model\Task;
use think\Request;
use app\project\model\Project;
use app\project\model\User;
use app\project\model\Detail;
use app\project\model\Event;
use app\project\service\UserService;
use think\Cookie;
use app\project\model\ProjectUser;
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
            return $this->error('未获取到项目Id');
        }
        $project = Project::get($projectId);

        $userIds = $project->projectUsers()->column('user_id');
        foreach ($userIds as $key => $value) {
            $users[] = User::get($value);
        }

        $this->assign('users',$users);
        $this->assign('project', $project);

        return $this->fetch();
    }

    public function edit()
    {
        // 获取项目id信息
        $projectId = Request::instance()->param('projectId/d');
        $project = Project::get($projectId);

        if (is_null($projectId)) {
            return $this->error('未获取到项目Id');
        }
       
        $userIds = $project->projectUsers()->column('user_id');
        foreach ($userIds as $key => $value) {
            $users[] = User::get($value);
        }

        // 获取任务的Id
        $taskId = Request::instance()->param('taskId/d');
        if (is_null($taskId)) {
            return $this->error('未获取到任务Id');
        }
        $task = Task::get($taskId);

        $this->assign('task', $task);
        $this->assign('users',$users);
        $this->assign('project', $project);

        return $this->fetch();
    }

    /**
     * 添加事件
     * */
    public function event()
    {
        // 获取任务,c项目的Id
        $projectId = Request::instance()->param('projectId/d');
        $taskId = Request::instance()->param('taskId/d');

        if (is_null($projectId)) {
                    return $this->error('未获取到项目Id');
        }
        if (is_null($taskId)) {
            return $this->error('未获取到任务Id');
        }

        $this->assign('taskId', $taskId);
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
        $details =Detail::where('task_id', $taskId)->order('id desc')->select();
        $events = Event::where('task_id', $taskId)->order('id desc')->select();
        $this->assign('details', $details);
        $this->assign('events', $events);
        return $this->fetch();
    }

    public function saveEvent()
    {
        $projectId = Request::instance()->param('projectId/d');
        $taskId = Request::instance()->param('taskId/d');
        if (is_null($taskId)) {
            return $this->error('未获取到任务Id');
        }
        // 获取事件并存入数据库
        $event = new Event();
        $event->event = input('post.event');
        $event->user_id = UserService::getCurrentUserId();
        $event->task_id = $taskId;

        if(!$event->validate()->save()) {
            return $this->error('添加事件失败' . $event->getError());
        }
        
        $currentPage = Cookie::get('currentPage','page_');

         return $this->success('添加事件成功', url('index?id=' . $projectId));
    }

    public function index()
    {   
        // 获取项目projectId
        $projectId =Request::instance()->param('id');
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id', url('project/index'));
        }
        $project = Project::get($projectId);

        // 判断权限
        $currentUserId = UserService::getCurrentUserId();
        $projectUser = new ProjectUser();
        $is = $projectUser->where('project_id', $projectId)->where('user_id', $currentUserId)->select();
        if (empty($is)) {
            return $this->error('您未加入该项目');
        }

        // 获取搜索框信息
        $name = Request::instance()->get('title'); 
        $status = Request::instance()->get('status');

        if(isset($_GET['page'])){
            $page = $_GET['page'];
            Cookie::set('currentPage',$page,['prefix'=>'page_','expire'=>3600]);
        }
        elseif (Cookie::get('currentPage','page_')!=null){
            $page=Cookie::get('currentPage','page_');
        }
        else{
            $page = 1;
        }

        // 定制查询条件
        if($status === null && $name === null || $status==='' && $name===''){
            $tasks = $project->tasks()->paginate(5, false, ['page'=>$page]);
        } else {
            if ($name !== '' && $status === '') {
                $tasks = $project->tasks()->where('name','like','%' . $name . '%')->order('id desc')->paginate(5, false, ['page'=>$page], ['query'=> ['name' =>$name]]);
            } else { 
                if($name === '' && $status !== '') 
                    $tasks = $project->tasks()->where('status',$status )->order('id desc')->paginate(5, false, ['page'=>$page], ['query'=> ['status' => $status]]);
                else{
                    $tasks = $project->tasks()->where('name','like','%' . $name . '%')->where('status',$status )->order('id desc')->paginate(5, false, ['page'=>$page], ['query'=> ['name' =>$name, 'status' => $status]]);
                }
            }
            
        }
        
        $this->assign('projectId', $projectId);
        $this->assign('tasks', $tasks);
        $this->assign('status', $status);

        return $this->fetch();
    }

    public function save() 
    {   
        // 获取项目的Id
        $projectId = Request::instance()->param('projectId/d');
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id');
        }

        // 实例化任务并赋值
        $task = new Task();
        $task->name = input('post.name');
        $startDate = input('startDate');
        $endDate = input('endDate');
        $task->status = input('status');
        $task->user_id = input('userId/d');

        if ($this->getDateLegai($startDate, $endDate)) {
            $task->start_date = $startDate;
            $task->end_date = $endDate;
        } else {
            return $this->error('结束时间不能小于开始时间');
        }
        if (is_null($task)) {
            return $this->error('未获取到需要保存的任务');
        }
        if ($task->user_id === 0) {
            return $this->error('未选择任务负责人');
        }

        $task->project_id = $projectId;

        if (!$task->validate()->save()) {
            return $this->error('添加失败' . $this->getError());
        }
        return $this->success('添加成功', url('index?id=' . $projectId));
    }

    public function update()
    {
        // 获取项目的id
        $projectId = Request::instance()->param('projectId/d');
        if (is_null($projectId)) {
            return $this->error('未获取到项目的Id');
        }

        // 获取任务的Id
        $taskId = input('taskId');
        $task = Task::get($taskId);
        if (is_null($task)) {
            return $this->error('未能传入任务');
        }

        $task->user_id = input('userId/d');
        if ($task->user_id === 0) {
            return $this->error('未选择任务负责人');
        }

        $startDate = input('startDate');
        $endDate = input('endDate');

        // 判断时间的合法性
        if ($this->getDateLegai($startDate, $endDate)) {
            $task->start_date = $startDate;
            $task->end_date = $endDate;
        } else {
            return $this->error('结束时间不能小于开始时间');
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
                return $this->error('状态更改失败');
            }
        }

        $task->name = input('name');
        $task->status = $status;
        $task->project_id = $projectId;

        if(!$task->validate()->save()) {
            return $this->error('更改失败' . $task->getError());
        }

        return $this->success('更改成功', url('index?id=' . $projectId));
    }

    /**
     * 判断时间的合法性
     * @return 开始时间<结束时间 true; 否则false
     * */
    static public function getDateLegai($startDate, $endDate)
    {
        if (strtotime($startDate) < strtotime($endDate)) {
            return true;
        } else {
            return false;
        }
    }

}