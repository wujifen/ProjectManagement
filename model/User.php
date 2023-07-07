<?php 
namespace app\project\model;
use think\Model; 

/**
 * 用户类
 * */
class User extends Model
{
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
}