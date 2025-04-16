<?php
namespace PHPEMS;
/**
 * Created by PhpStorm.
 * User: 火眼
 * Date: 2019/1/14
 * Time: 21:16
 */

require_once PEPATH."/lib/include/wechat/WxPay.Config.Interface.php";
require_once PEPATH."/lib/include/wechat/WxPay.Config.php";
require_once PEPATH."/lib/include/wechat/WxPay.Exception.php";
require_once PEPATH."/lib/include/wechat/WxPay.Data.php";
require_once PEPATH."/lib/include/wechat/WxPay.Api.php";
require_once PEPATH."/lib/include/wechat/WxPay.Notify.php";
require_once PEPATH."/lib/include/wechat/WxPay.JsApiPay.php";


class wxpay extends \WxPayNotify
{
    public $wxdata;
	public $agent = null;

    public function __construct()
    {
        
		$this->orders = \PHPEMS\ginkgo::make('orders','bank');
		$this->tools = new \JsApiPay();
    }

    public function _init($parm = null)
    {
        if($parm)$this->agent = $parm;
        else $this->agent = \PHPEMS\ginkgo::make('ev')->isWeixin();
    }
	
	public function pehandle()
	{
		$this->handle(new \WxPayConfig($this->agent),false);
	}

    public function Queryorder($transaction_id)
    {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $config = new \WxPayConfig($this->agent);
        $result = \WxPayApi::orderQuery($config, $input);
        if(array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($objData,$config,&$msg)
    {
        $data = $objData->GetValues();
        //TODO 1、进行参数校验
        if(!array_key_exists("return_code", $data) ||(array_key_exists("return_code", $data) && $data['return_code'] != "SUCCESS"))
        {
            //TODO失败,不是支付成功的通知
            //如果有需要可以做失败时候的一些清理处理，并且做一些监控
            $msg = "异常异常";
            return false;
        }
        if(!array_key_exists("transaction_id", $data))
        {
            $msg = "输入参数不正确";
            return false;
        }
        //TODO 2、进行签名验证
        try {
            $checkResult = $objData->CheckSign($config);
            if($checkResult == false){
                //签名错误
                return false;
            }
        } catch(Exception $e) {
            //
        }
        //TODO 3、处理业务逻辑
        $notfiyOutput = array();
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }

        $ordersn = $data["out_trade_no"];
        $this->orders->payforOrder($ordersn,'wxpay');
        return true;
    }

    public function getUserInfo()
    {
        return $this->tools->GetUserInfoByToken($this->wxdata['openid'],$this->wxdata['access_token']);
    }

    public function getwxopenid($reget = false)
    {
    	if(!$_SESSION['openid'] || $reget)
        {
            $_SESSION['openid'] = $this->tools->GetOpenid();
            $this->wxdata = $this->tools->data;
        }
        return $_SESSION['openid'];
    }
	
	public function outMpPay($order)
    {
        $openid = $this->getwxopenid();
        $config = new \WxPayConfig($this->agent);
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['ordertitle']);
        $input->SetAttach("购买课程、题库");
        $input->SetOut_trade_no($order['ordersn']);
        $input->SetTotal_fee(intval($order['orderprice'] * 100));
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url(WP."api/wxnotify.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openid);
        $order = \WxPayApi::unifiedOrder($config,$input);
        $jsApiParameters = $this->tools->GetJsApiParameters($order);
        return $jsApiParameters;
    }

    public function outJsPay($order)
    {
        $openid = $this->getwxopenid();
        $config = new \WxPayConfig($this->agent);
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['ordertitle']);
        $input->SetAttach("购买课程、题库");
        $input->SetOut_trade_no($order['ordersn']);
        $input->SetTotal_fee(intval($order['orderprice'] * 100));
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url(WP."api/wxnotify.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openid);
        $order = \WxPayApi::unifiedOrder($config,$input);
        $jsApiParameters = $this->tools->GetJsApiParameters($order);
        return $jsApiParameters;
    }

    /**
     *
     * 生成直接支付url，支付url有效期为2小时,模式二
     * @param UnifiedOrderInput $input
     */
    public function GetPayUrl($input)
    {
        if($input->GetTrade_type() == "NATIVE" || $input->GetTrade_type() == "MWEB")
        {
            $result = \WxPayApi::unifiedOrder(new \WxPayConfig($this->agent),$input);
            return $result;
        }
    }

    public function outNativeUrl($order)
    {
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['ordertitle']);
        $input->SetAttach("购买课程、题库");
        $input->SetOut_trade_no($order['ordersn']);
        $price = intval($order['orderprice']*100);
        $input->SetTotal_fee($price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($order['ordertitle']);
        $input->SetNotify_url(WP."api/wxnotify.php");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($order['ordersn']);
        $result = $this->GetPayUrl($input);
        return $result;
    }

    public function outMwebUrl($order)
    {
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['ordertitle']);
        $input->SetAttach("购买课程、题库");
        $input->SetOut_trade_no($order['ordersn']);
        $price = intval($order['orderprice']*100);
        $input->SetTotal_fee($price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($order['ordertitle']);
        $input->SetNotify_url(WP."api/wxnotify.php");
        $input->SetTrade_type("MWEB");
        $input->SetProduct_id($order['ordersn']);
        $result = $this->GetPayUrl($input);
        return $result;
    }
}

?>