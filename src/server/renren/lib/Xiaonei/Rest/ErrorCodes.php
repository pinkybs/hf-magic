<?

class Xiaonei_Rest_ErrorCodes
{

  public static $DESCRIPTIONS = array(
      0           => 'Success',
      1           => '一个未知的错误发生',
      2           => '服务临时不可用',
      3           => '未知的方法',
      4           => '应用已达到设定的请求上限',
      5           => '请求来自未经授权的IP地址',
      6           => '当前用户session key过期了（已过期）',
      7           => 'rest api调用次数超过了限制',
      
      100         => '无效未知参数',
      101         => '无效的API_KEY',
      102         => '无效的SESSION_KEY',
      103         => '必须是POST提交',
      104         => '无效的签名',
      
      200         => 'USER PERMISSIONS ERRORS',
      210         => 'API_EC_PERMISSION_USER',
      220         => 'API_EC_PERMISSION_ALBUM',
      221         => 'API_EC_PERMISSION_PHOTO',
      
      450         => '当前用户的sessionKey过期',
      451         => 'Session key specified cannot be used to call this method',
      452         => 'Session key 无效. 可能传入的sessionKey格式出现错误',
      453         => '调用此方法时，session key 是一个必须的参数',
      
      2000        => '没有得到auth_token',
      2001        => 'token对象中没有得到userId',
      2002        => '用户没有登录校内网',
      
      10000       => '登录失败',
      
      10201       => 'API_EC_REST_LACKO_API_KEY',
      10202       => 'API_EC_REST_LACKOF_SESSION_KEY',
      10203       => 'API_EC_REST_LACKOF_CALL_ID',
      
      10600       => '此接口的调用规则是: 48小时之内，一个用户最多可以调用10次',
      10601       => 'Feed标题模板是无效的，标题模板中必须含有 \"{actor}\" 变量，且只含有一个',
      10602       => '文本空，显示内容应该在30个字符之内',
      10603       => 'if {target} is used, then target_ids becomes a required parameter',
      10604       => 'title_data 参数不是一个有效的JSON 格式数组',
      10605       => 'Feed的标题模板缺少必须的参数，或者title_data JSON数组定义的参数不完全。',
      10606       => '只能包含<a>或者<xn:name>标签。',
      10607       => '内容部分是可选的，内容部分的最终显示的字符数应该控制在100个以内',
      10608       => 'Feed story photos could not be retrieved, or bad image links were provided',
      10609       => 'title_data 只能包含<a>或者<xn:name>标签。',
      10610       => 'body_data 只能包含<a>或者<xn:name>标签。',
      10611       => 'Applications are limited to calling this function once every 12 hours for each user',
      10612       => 'the memcache Error',
      10613       => 'The word "message" is disallowed in a feed story',
      10614       => 'the title is required',
      10615       => 'Feed的标题模板缺少必须的参数，或者title_data JSON数组定义的参数不完全。',
      10616       => 'Feed标题模板中定义的变量和title_data JSON数组中的定义不匹配',
      10617       => '没有注册模板',
      10618       => 'url错误',
      10619       => 'body_data 参数不是一个有效的JSON 格式数组',
      10620       => 'body_data 只能包含<a>或者<xn:name>标签。',
      
      10700       => '传入文本信息错误',
      10701       => '传入接口者id错误',
      10702       => '发送者已超过当天发送配额',
      10703       => 'AppToUser已超过发送配额',
      10704       => '通知发送过快',
      
      10800       => '传递订单号已失效，无法获取到token',
      10801       => '无效的订单号 (小于零)',
      10802       => '消费金额无效:不支持大笔消费金额>100或者小于零',
      10803       => '校内网支付平台应用资料审核未通过，没有使用平台的资格',
      10804       => '该订单不存在',
      
      20201       => '需要用户授予status_update权限',
      20204       => '需要用户授予read_stream权限'

  );    
}