<?php 
namespace app\project\validate;
use think\Validate;

/**
 * 用户验证类
 * */
class User extends Validate 
{
	protected $rule = [
        'name'  => 'require|length:2,25',
        'username' => 'require|length:2,25',
    ];
}