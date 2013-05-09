<?php
/**
 * wechat php test
 */

//define your token
define("TOKEN", "mygoare");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

class wechatCallbackapiTest
{
  public function valid()
  {
    $echoStr = $_GET["echostr"];

    //valid signature , option
    if($this->checkSignature()){
      echo $echoStr;
      $this->responseMsg();
      exit;
    }
  }

  public function responseMsg()
  {
    //get post data, May be due to the different environments
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

    //extract post data
    if (!empty($postStr)){

      $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
      $msgType = $postObj->MsgType;
      switch ($msgType) {
        case "text" :
          $result = $this->receivedText($postObj);
          break;
      }
      echo $result;
    }else {
      echo "";
      exit;
    }
  }

  private function receivedText($postObj)
  {
    $fromUsername = $postObj->FromUserName;
    $toUsername = $postObj->ToUserName;
    $keyword = trim($postObj->Content);
    $time = time();
    $textTpl = "<xml>
      <ToUserName><![CDATA[%s]]></ToUserName>
      <FromUserName><![CDATA[%s]]></FromUserName>
      <CreateTime>%s</CreateTime>
      <MsgType><![CDATA[%s]]></MsgType>
      <Content><![CDATA[%s]]></Content>
      <FuncFlag>0</FuncFlag>
      </xml>";             
    if(!empty( $keyword ))
    {
      $msgType = "text";
      $contentStr = "Welcome to wechat world!";
      $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
      return $resultStr;
    }else{
      return "Input something...";
    }
  }

  private function receivedGeo($postObj)
  {
  }

  private function checkSignature()
  {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];  

    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );

    if( $tmpStr == $signature ){
      return true;
    }else{
      return false;
    }
  }
}

?>
