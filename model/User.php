<?php 
namespace app\project\model;
use think\Model; 
use think\Db;
use think\Request;

/**
 * 用户类
 * */
class User extends Model
{
    public function creators()
    {
        return $this->hasMany('Project')->field('creator_id');
    }

    /**
     * 设置获取器获取用户性别
     * 0男 1女
     * */
    public function getSexAttr($value)
    {
        $status = array('0'=>'男','1'=>'女');
        $sex = $status[$value];
        if (isset($sex)) {
            return $sex;
        } else {
            return $status[0];
        }
    }

    /**
     * 注销
     * @return true成功，false失败
     * */
    static public function logOut()
    {
        // 销毁session中的数据
        session('userId', null);

        return true;
    }

    /**
     * 判断登录页密码和用户名是否正确
     * @param username 用户名
     * @param password 密码
     * @return bool
     * */
    static public function login($username, $password)
    {
        $map = array('username' => $username);
        $user = self::get($map);

        if (!is_null($user)) {
            session('userId', $user->id);
            if ($user->checkPassword($password)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 列表的查询
     * */
    static public function indexlist()
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

        // 反馈结果
        return $users; 
    }

    static public function isLogin()
    {
        $id = session('userId');
        if (isset($id)) {
            return true;
        }
        return false;
    }

    /**
     *  验证密码是否正确
     *  @param string $password
     *  @return bool
     * **/
    public function checkPassword($password) 
    {
        if ($this->getData('password') === $password) {
            return true;
        }
        return false;
    }

    /**
     * 获取用户是否存在关联表中
     * */
    public function getIsJoin(Project &$project)
    {
        // 取用户的Id
        $userId = $this->id;
        $projectId = $project->id;

        // 定制查询条件
        $map = array();
        $map['user_id'] = $userId;
        $map['project_id'] = $projectId;

        // 从关联表中取信息
        $userProject = ProjectUser::get($map);
        if (is_null($userProject)) {
            return false;
        }
        return true;
    }

    /**
     * 拼接查询条件
     * */
    static public function query($id)
    {
        $query ="select p.id from yunzhi_project_user pu join yunzhi_user u on pu.user_id = u.id JOIN yunzhi_project p on p.id = pu.project_id where u.id =$id and p.type = 1 
            UNION
            select p.id from yunzhi_project p WHERE $id = p.user_id or p.type = 0";

        $result = Db::query($query);
        return $result;
    }

}