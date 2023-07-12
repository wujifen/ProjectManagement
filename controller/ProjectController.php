<?php 
namespace app\project\controller;
use app\project\model\Project;
use app\project\model\ProjectUser;
use app\project\model\User;
use think\Request;
use app\project\service\UserService;

/**
 * 项目类
 * */
class ProjectController extends BaseController
{
    public function index()
    {  
        // 获取当前登录用户的Id
        $currentUserId = UserService::getCurrentUserId();
        $currenUser = User::get($currentUserId);
        $this->assign('currenUser', $currenUser);

        // 查询出当前登录用户可见的项目Id
        $projectIds = User::query($currentUserId);

        $array = array();
        foreach ($projectIds as $value) {
            $projectId = $value;
            $project = Project::get($projectId);
            $array[] = $project;
        }

        // 向V层传值
        $this->assign('projects', $array);
        
        // 向数据返回给用户
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function toJoin()
    {
        // 获取项目的id
        $projectId = Request::instance()->param('id/d');
        $project = Project::get($projectId);
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id', url('index'));
        }
        // 获取用户ID
        $currentUserId = UserService::getCurrentUserId();
        
        if (false === ProjectUser::saveToJoin($projectId, $currentUserId)) {
            return $this->error('加入失败', url('index'));
        }
        return $this->success('加入成功', url('index'));
    }

    /**
     * 保存新增加的项目
     * */
    public function save()
    { 
        // 接受add提交的数据
        $name = Input('post.name');
        $type = Input('post.type/d');   

        // 获取当前登录的对象的ID
        $id = session('userId');

        // 实例化对象project并赋值
        $project = new Project();
        $project->name = $name;
        $project->type = $type;
        $project->user_id = $id;

        // 保存数据至peoject表
        if (!$project->validate()->save()) {
            return $this->error('新建项目失败' . $this->getError(), url('index'));
        }

        return $this->success('新建项目成功', url('index'));
    }

    /**
     * 邀请用户参入私有项目
     * */
    public function invite()
    {
        $project = Project::get(Request::instance()->param('id/d'));
        $users = UserService::indexlist();

        // 向V层传数据
        $this->assign('project', $project);
        $this->assign('users', $users);
        return $this->fetch();
    }

    public function toSave()
    {
        $userId = Request::instance()->post('user_id/d');
        if (is_null($userId)) {
            return $this->error('未邀请用户', url('index'));
        }

        $projectId = Request::instance()->param('id/d');
        if (is_null($projectId)) {
            return $this->error('未获取到项目Id', url('index'));
        }

        $projectUser = new ProjectUser();
        if (!$projectUser->saveToJoin($projectId, $userId)) {
            return $this->error('邀请失败', url('invite'));
        }
        return $this->success('邀请成功', url('project/index'));
    }
}