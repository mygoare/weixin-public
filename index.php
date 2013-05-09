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
        case "location" :
          $result = $this->receivedGeo($postObj);
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
    $fromUsername = $postObj->FromUserName;
    $toUsername = $postObj->ToUserName;
    $location_X = $postObj->Location_X;
    $location_Y = $postObj->Location_Y;
    $time = time();
    $textTpl = "<xml>
      <ToUserName><![CDATA[%s]]></ToUserName>
      <FromUserName><![CDATA[%s]]></FromUserName>
      <CreateTime>%s</CreateTime>
      <MsgType><![CDATA[%s]]></MsgType>
      <ArticleCount>1</ArticleCount>
      <Articles>
      <item>
      <Title><![CDATA[Geo]]></Title> 
      <Description><![CDATA[Your Geo Info]]></Description>
      <PicUrl><![CDATA[%s]]></PicUrl>
      <Url><![CDATA[%s]]></Url>
      </item>
      </Articles>
      <FuncFlag>1</FuncFlag>
      </xml>";             
    if($location_X && $location_Y)
    {
      $msgType = "news";
      $picUrl = "http://st.map.soso.com/api?size=400*300&center=".$location_Y.",".$location_X."&zoom=16&markers=".$location_Y.",".$location_X;
      $url = "http://www.soso.com";
      $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $picUrl, $url);
      return $resultStr;
    }else{
      return "Input something...";
    }
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
