<?php
define('SERVER_ID', '99');
define('APP_SERVER_TYPE', 2); //1 正服  2 测服 3 开发服

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('ENABLE_DEBUG', true);

define('PRODUCT_ID', 'hf_magic');

define('SNS_ID', 'hf_magic_rr_test');
define('APP_ID', '137954');
define('APP_KEY', 'f58f38e2ce9245ffa0e593fb564fb082');
define('APP_SECRET', '332a88fd32674919bb95655e1d80e553');
define('APP_NAME', 'testmagic');

define('DATABASE_NODE_NUM', 4);
define('MEMCACHED_NODE_NUM', 10);

define('HOST', 'http://testmagic-rr.happyfish001.com');
define('STATIC_HOST', 'http://testmagicstatic.happyfish001.com/renren');

define('SEND_ACTIVITY', true);
define('SEND_MESSAGE', false);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 0);

define('ECODE_NUM', 4);