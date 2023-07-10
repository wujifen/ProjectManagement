<?php 
namespace app\project\model;
use think\Model;

/**
 * 项目和用户的中间表
 * */
 class ProjectUser extends Model
 {
    /**
     * 申请加入项目
     * @param int projectId 项目的Id 
     * @param int userId 用户的Id
     * @return  保存成功true，失败false 
     * */
    static public function tojoin($projectId, $userId) 
    {
        // 实例化Peoject
        $projectUser = new ProjectUser();

        // 赋值
        $projectUser->project_id = $projectId;
        $projectUser->user_id = $userId;

        if (is_null($projectUser)) {
            return false;
        }
        if (!$projectUser->save()) {
            return false;
        }
        return true;        
    }
 }