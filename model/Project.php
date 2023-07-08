<?php 
namespace app\project\model;
use think\Model;
use think\Db;

/**
* 项目类
* */
class Project extends Model
{
    /**
     * 设置获取器获取项目的公开性
     * 0公开的 1私有的
     * */
    public function getTypeAttr($value)
    {
        $status = array('0'=>'公开的','1'=>'私有的');
        if (isset($status[$value])) {
            return $status[$value];
        } else {
            return $status[0];
        }
    }

    /**
    * 多对多关联
    * */
    public function Users()
    {
        return $this->belongsToMany('User');
    }

    /**
     * 一对多关联查询  这里用于输出项目的创建人
     * */
    public function User()
    {
        return $this->belongsTo('User');
    }

    /**
     * 判断登录人是否加入了该项目
     * */
    public function getIsJoin(User &$user)
    {
        $userId = $user->id;
        $projectId = $this->id;

        $map = array();
        $map['user_id'] = $userId;
        $map['project_id'] = $projectId;

        $projectUser = ProjectUser::get($map);
        if (!is_null($projectUser)) {
            return true;
        } 
        return false;
    }
}