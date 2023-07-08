<?php 
namespace app\project\controller;
use app\project\model\Project;
use app\project\model\ProjectUser;
use app\project\model\User;

/**
 * 项目类
 * */
class ProjectController extends BaseController
{
    public function index()
    {  
        // 获取当前登录用户的Id
        $id = session('userId');
        $this->assign('user', User::get($id));

        // 查询出当前登录用户可见的项目Id
        $projectIds = User::query($id);

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

    public function tojoin()
    {
        return $this->success('你已成功加入', url('task/index'));
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
        $project->creator_id = $id;

        // 保存数据至peoject表
        if (!$project->validate()->save()) {
            return $this->error('新建项目失败' . $this->getError(), url('index'));
        }

        // 实例化对象project_user并赋值
        $projectUsers = new ProjectUser();
        $projectUsers->user_id = $id;
        $projectUsers->project_id = $project->id; 

        // 将project_user传入V层
        $this->assign('projectUsers', $projectUsers);

        // 反馈结果
        if (!$projectUsers->validate()->save()) {
            return $this->error('新建项目失败' . $this->getError(), url('index'));
        }
        return $this->success('新建项目成功', url('index'));
    }

}