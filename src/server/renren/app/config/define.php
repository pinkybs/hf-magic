<?php
define('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'config');
define('MODELS_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'models');
define('MODULES_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'lib');
define('DOC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'www');
define('LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs');
define('TEMP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'temp');
define('SMARTY_TEMPLATES_C', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates_c');

define('ENABLE_DEBUG', true);
define('SERVER_ID', '1');

define('SNS_PLATFORM', 'renren');
define('LANGUAGE', 'CN');
define('APP_ID', 94949);
define('APP_NAME', 'magic');
define('APP_KEY', 'ac058bfe285946798a4947f1c2ebf00b');
define('APP_SECRET', '95181cf2fefc4ebcbe44aa6e8b10e57b');

define('MONGODB', 'mongodb://127.0.0.1:27017');
define('MONGODB_NAME', SNS_PLATFORM . '_' . APP_NAME);
//define('MONGODB_1', 'mongodb://192.168.1.125:27117');

define('SEND_ACTIVITY', false);
define('SEND_MESSAGE', true);

define('APP_STATUS', 1);
define('APP_STATUS_DEV', 1);
