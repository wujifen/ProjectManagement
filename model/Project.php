<?php 
namespace app\project\model;
use think\Model;

/**
 * 项目类
 * */
 class Project extends Model
 {
    /**
     * 多对多关联
     * */
    public function Users()
    {
        return $this->belongsToMany('User');
    }
 }