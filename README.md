# Wechat gamecenter
This is a project using CodeIgniter to generate Wechat Gamecenter API Server.

##How to use? 
1. Download the code to your host (PHP required).
2. Edit config files.
    - /gamecenter/application/config/config.php
    <pre><code>$config['base_url']="http://yourdomain.com"</code></pre>
    - /gamecenter/application/config/database.php
    <pre><code>
    $db['default']['hostname'] = 'hostname';
    $db['default']['username'] = 'username';
    $db['default']['password'] = 'password';
    $db['default']['database'] = 'gamecenter';
    $db['default']['dbdriver'] = 'mysql';
    </code></pre>
    - /gamecenter/application/config/weixin_config.php
    <pre><code>
    $config['token'] = 'yourtoken';  // 微信公共平台连接token
    $config['grant_type'] = 'client_credential'; // grant_type 是获取access_token填写client_credential
    $config['appid'] = 'appid';  // appid 是第三方用户唯一凭证
    $config['secret'] = '即appsecret'; // secret 是第三方用户唯一凭证密钥，即appsecret
    </code></pre>
3. Create database.
    - Create database if 'gamecenter' is not exist.
    - Run http://yourdomain.com/gamecenter/database to create tables.

  
    
    
