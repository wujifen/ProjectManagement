<?php 
namespace app\project\model;
use think\Model; 

class User extends Model
{
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

            if ($user->password === $password) {
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
        if ($this->getData('password') === $this::encryptPassword($password)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 对密码进行加密
     * @param password
     * @return string 加密后的密码
     * */
    static public function encryptPassword($password)
    {   
        if (!is_string($password)) {
            return $this->error("传入变量类型错误", url('login/index'));
        }
        return sha1(md5($password) . 'mengyunzhi');
    }
}