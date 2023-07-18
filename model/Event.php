<?php 
namespace app\project\model;
use think\Model;

class Event extends Model
{
    public function user()
    {
        return $this->belongsTo('User');
    }
}