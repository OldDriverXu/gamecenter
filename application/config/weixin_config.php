<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['token'] = 'yourtoken';
$config['access_token_url'] = 'https://api.weixin.qq.com/cgi-bin/token';

// grant_type	 是获取access_token填写client_credential
// appid	 是第三方用户唯一凭证
// secret	 是第三方用户唯一凭证密钥，即appsecret
$config['grant_type'] = 'client_credential';
$config['appid'] = 'appid';
$config['secret'] = 'appsecret';

// oauth
$config['oauth_grant_type'] = 'authorization_code';
$config['scope'] = 'snsapi_userinfo';  //应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息）
$config['oauth_url'] = 'https://api.weixin.qq.com/sns/oauth2/access_token';

?>
