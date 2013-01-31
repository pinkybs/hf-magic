<?php
define('SERVER_ID', '1004');
define('APP_SERVER_TYPE', 1); //1 正服  2 测服 3 开发服

define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', '/home/admin/logs/magic/renren/debug');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('ENABLE_DEBUG', true);

define('PRODUCT_ID', 'hf_magic');

define('SNS_ID', 'hf_magic_rr');
define('APP_ID', '136260');
define('APP_KEY', '2710a960f3354779afe5a33e57836093');
define('APP_SECRET', 'bfa7a7ef6d4247458e45acd0b702b738');
define('APP_NAME', 'happymagic');

define('DATABASE_NODE_NUM', 4);
define('MEMCACHED_NODE_NUM', 10);

define('HOST', 'http://rrmagic.happyfishgame.com.cn');
define('STATIC_HOST', 'http://img01.hapyfish.com/renren');

define('SEND_ACTIVITY', true);
define('SEND_MESSAGE', false);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 0);

define('ECODE_NUM', 4);