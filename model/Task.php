<?php 
namespace app\project\model;
use think\Model;

/**
* 任务类
* */
class Task extends Model
{
    /**
     * 设置获取器获取任务的状态
     * */
    public function getStatusAttr($value)
    {
        $status = [0=>'未开始',1=>'进行中',2=>'已完成'];
        if (isset($status[$value])) {
            return $status[$value];
        } else {
            return $status[0];
        }
    }

    /**
     * 一对多关联查询  这里用于输出任务的负责人
     * */
    public function Task()
    {
        return $this->belongsTo('User');
    }

    static public function getSeachAttr($value)
    {
        $status = ['未开始'=>0,'进行中'=>1,'已完成'=>2];
        if (isset($status[$value])) {
            return $status[$value];
        } else {
            return false;
        }
    }

    public function details()
    {
        return $this->belongsTo('Detail');
    }
}