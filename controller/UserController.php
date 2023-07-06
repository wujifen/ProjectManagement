<?php 
namespace app\project\controller;
use app\project\model\User;
use think\Request;
use think\Validate;

class UserController extends IndexController
{   
    // index->add->save->index
    public function add()
    {
        return $this->fetch();
    }
    public function save()
    {
        // 实例化User对象并赋值
        $user = new User();

        // 为对象赋值
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


    // index->edit->updateo->index
    public function edit()
    {
        // 从Pathinfo获取需要修改的用户的Id
        $id = input('param.id/d');

        // 获取要修改的User对象-
        $user = User::get($id);

        // 将对象传给V层
        $this->assign('user', $user);

        // 跳转至edit页面
        return $this->fetch();
    }

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
        $user->username = input('post.username');
        $user->password = input('post.password');
        $user->sex = input('post.sex/d');

        // 更新反馈结果
        if (!$user->validate()->save()) {
            return $this->error('更新失败' . $user->getError(), url('index'));
        }
        return $this->success('更新成功', url('index'));
    }


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

    // index->index
    public function index()
    {
        // 获取查询信息
        $name = Request::instance()->get('name');
        $username = Request::instance()->get('username');

        // 设置每页大小
        $pageSize = 5;

        // 实例化User
        $user = new User();

        // 定制查询信息
        if (!empty($name)) {
            $user->where('name','like','%' . $name . '%');
        }

        if (!empty($username)) {
            $user->where('username', 'like', '%' . $username . '%');
        }

        // 条件查询并调用分页
        $users = $user->paginate($pageSize, false, ['query'=> ['name' =>$name,'username' => $username]]);

        // 向V层传数据
        $this->assign('users', $users);

        // 取回打包后的数据
        $htmls = $this->fetch();

        // 将数据返还给用户
        return $this->fetch();
    }

    public function invite()
    {
        return $this->fetch();
    }

    
}