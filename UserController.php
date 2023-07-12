<?php
namespace app\project\controller;

class  UserController extends BaseController{
	/**
     * 邀请用户参入私有项目
     * */
    public function invite()
    {
        $project = Project::get(Request::instance()->param('id'));
        $users = User::indexlist();
        $userId = session('userId');

        // 向V层传数据
        $this->assign('project', $project);
        $this->assign('userId', $userId);
        $this->assign('users', $users);
        return $this->fetch();
    }
}
    