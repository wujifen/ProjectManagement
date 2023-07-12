<?php 
namespace app\project\model;
use think\Model;

class Detail extends Model
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
    public function user()
    {
        return $this->belongsTo('User');
    }
}