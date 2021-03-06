<?php
/**
 * Created by PhpStorm.
 * User: Taidmin
 * Date: 2021/2/27
 * Time: 15:31
 */


namespace app\api\service;


use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use think\Loader;

Loader::import();

class Pay
{
    private $orderID;
    private $orderNO;

    public function __construct($orderID)
    {
        if(!$orderID){
            throw new Exception('订单号不允许为NULL');
        }

        $this->orderID = $orderID;
    }

    public function pay()
    {
        // 订单号可能根本就不存在
        // 订单号确实是存在的，但是，订单号和当前用户是不匹配的
        // 订单有可能已经被支付过
        // 进行库存量检测
        $this->checkOrderValid();

        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderID);
        if(!$status){
            return $status;
        }
    }

    private function makeWxPreOrder()
    {
        // openid
        $openId = Token::getCurrentTokenVar('openid');
        if(!$openId){
            throw new TokenException();
        }
    }

    private function checkOrderValid()
    {
        $order = OrderModel::where('order', $this->orderID)->find();
        if(!$order){
            throw new OrderException();
        }

        if(!Token::isValidOperate($order->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
            ]);
        }

        if($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'msg' => '订单已支付过啦',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }

        $this->orderNO = $order->order_no;

        return true;
    }
}