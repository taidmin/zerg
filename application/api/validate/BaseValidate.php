<?php
/**
 * Created by PhpStorm.
 * User: Taidmin
 * Date: 2020/12/9
 * Time: 23:29
 */


namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\facade\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        $params = Request::param();

        $result = $this->batch()->check($params);

        if(!$result){
            throw new ParameterException([
                'msg' => $this->error,
            ]);
        }

        return true;
    }

    // 自定义验证规则,传入的参数是不是正整数
    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0){
            return true;
        }

        return false;
    }

    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|6|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if($result){
            return true;
        }

        return false;
    }

    protected function isNotEmpty($value)
    {
        if(empty($value)){
            return false;
        }

        return true;
    }

    public function getDataByRule($arrays)
    {
        if(array_key_exists('user_id', $arrays) || array_key_exists('uid', $arrays)){
            // 不允许包含user_id或uid，防止覆盖user_id外键
            throw new ParameterException([
                'msg' => '参数中包含非法的参数名user_id或者uid'
            ]);
        }

        $newArray = [];
        foreach($this->rule as $key => $value){
            $newArray[$key] = $arrays[$key];
        }

        return $newArray;
    }
}