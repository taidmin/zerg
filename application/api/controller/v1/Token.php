<?php
/**
 * Created by PhpStorm.
 * User: Taidmin
 * Date: 2021/1/17
 * Time: 15:40
 */


namespace app\api\controller\v1;


use app\api\service\UserToken;
use app\api\validate\TokenGet;

class Token
{
    public function getToken($code = '')
    {
        (new TokenGet())->goCheck();

        $userToken = new UserToken($code);
        $token = $userToken->get();

        return [
            'token' => $token
        ];
    }
}