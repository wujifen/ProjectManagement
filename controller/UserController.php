<?php 
namespace app\project\controller;
use app\project\model\User;
use think\Request;
use think\Validate;

/**
 *用户类 
 * */
class UserController extends BaseController
{   
    
    /**
     * 登录界面
     * */
    public function add()
    {
        $user = new User();
        $user->id = 0;
        $user->name = '';
        $user->username = '';
        $user->password = '';
        $user->sex = 0;

        $this->assign('user', $user);
        return $this->fetch('edit');
    }

    /**
     * 保存add提交的数据
     * */
    public function save()
    {
        // 实例化User对象并赋值
        $user = new User();
        $user->name = input('post.name');
        $user->username = input('post.username');
        $user->password = input('post.password');
        $user->sex = input('post.sex');

        // 新增对象至数据表
        if (!$user->validate()->save()) {
            return $this->error('添加失败' . $user->getError(), url('index'));
        }

        // 反馈结果
        return $this->success('添加成功', url('index'));
    }

    /**
     * 编辑界面
     * */
    public function edit()
    {
        // 从Pathinfo获取需要修改的用户的Id
        $id = input('param.id/d');

        if (is_null($id)) {
            return $this->error('未获取到Id', url('index'));
        }

        // 获取要修改的User对象-
        $user = User::get($id);

        if (is_null($user)) {
            return $this->error('未获取到用户', url('index'));
        }

        // 将对象传给V层
        $this->assign('user', $user);

        // 跳转至edit页面
        return $this->fetch();
    }

    /**
     * 保存edit提交的数据
     * */
    public function update() 
    {
        // 获取要更新的关键字信息
        $id = input('post.id/d');

        if (is_null($id)) {
            return $this->error('更新失败，未找到I为：' . $id . '的用户');
        }

        // 获取要更新的对象
        $user = User::get($id);
        if (is_null($user)) {
            return $this->error('更新失败，用户不存在');
        }

        // 写入新的数据
        $user->name = input('post.name');
        $user->sex = input('post.sex/d');

        // 更新反馈结果
        if (!$user->validate()->save()) {
            return $this->error('更新失败' . $user->getError(), url('index'));
        }
        return $this->success('更新成功', url('index'));
    }

    /**
     * 删除用户
     * */
     public function delete()
    {
        // 从pathinfo 获取id 
        $id = input('param.id/d');

        // 判断Id是否存在
        if (is_null($id)) {
            return $this->error('删除失败，未获取到Id信息', url('index'));
        }

        // 获取要删除的对象
        $user = User::get($id);

        // 判断用户是否存在
        if (is_null($user)) {
            return $this->error('删除失败，用户不存在', url('index'));
        }

        // 删除对象并跳转
        if (!$user->validate()->delete()) {
            return $this->error('删除失败' . $user->getError(), url('index'));
        }
        return $this->success('删除成功', url('index'));
    }

    /**
     * 用户列表
     * */
    public function index()
    {
        $users = User::indexlist();

        $userId = session('userId');

        // 向V层传数据
        $this->assign('userId', $userId);
        $this->assign('users', $users);

        // 将数据返还给用户
        return $this->fetch();
    }

    /**
     * 重置密码
     * */
    public function reset()
    {
        // 接收pathinfo里的Id
        $id = input('param.id/d');

        $this->assign('id', $id);
        
        return $this->fetch();
    }

    /**
     * 保存密码
     * */
    public function savePassword()
    {   
        // 获取Id
       $id = input('post.id/d');
       if (is_null($id)) {
        return $this->error('失败，为获取到Id', url('index'));
       }

       // 获取新密码
       $confirmPassword = input('post.confirmPassword');
        if (is_null($confirmPassword)) {
        return $this->error('失败，为获取到新密码', url('index'));
       }

       // 获取重置密码的用户
       $user = User::get($id);
       if (is_null($user)) {
            return $this->error('失败，为获取到用户', url('index'));
       }

       // 将新密码存入数据库
       $user->password = $confirmPassword;
        if (!$user->validate()->save()) {
            return $this->error('失败' . $this->getError(), url('index'));
        }
       return $this->success('重置密码成功', url('index'));
    }
}