<?php 
namespace app\project\model;
use think\Model; 
use think\Db;
use think\Request;
use app\project\service\UserService;

/**
 * 用户类
 * */
class User extends Model
{
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

    static public function isLogin()
    {
        $currentUserId = UserService::getCurrentUserId();
        if (isset($currentUserId)) {
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
        $query ="SELECT sub.id
                FROM (
                  SELECT p.id
                  FROM yunzhi_project_user AS pu
                  JOIN yunzhi_user AS u ON pu.user_id = u.id
                  JOIN yunzhi_project AS p ON p.id = pu.project_id
                  WHERE u.id = $id AND p.type = 1

                  UNION

                  SELECT p.id
                  FROM yunzhi_project AS p
                  WHERE $id = p.user_id OR p.type = 0
                ) AS sub
                ORDER BY sub.id desc";

        $result = Db::query($query);
        return $result;
    }
    
    public function projectusers() 
    {
        return $this->hasMany('ProjectUser');
    }

    public function projects()
    {
        return $this->hasMany('Project');
    }
}