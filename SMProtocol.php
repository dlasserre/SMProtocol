#!/usr/bin/php
<?php
/** Global defined */
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(__DIR__));
defined('SMProtocol_PATH') || define('SMProtocol_PATH', APPLICATION_PATH.'/library/SMProtocol');

/** Application environment */
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (($_env = getenv('APPLICATION_ENV'))?$_env:'production'));

/** Application log */
defined('LOG_FILE') || define('LOG_FILE', APPLICATION_PATH.'/log/SMProtocol.log');
defined('LOG_IN_FILE') || define('LOG_IN_FILE', 1);
defined('LOG_IN_OUTPUT') || define('LOG_IN_OUTPUT', 2);
defined('LOG_LEVEL') || define('LOG_LEVEL', LOG_IN_FILE | LOG_IN_OUTPUT);

/** Color defined */
defined('COLOR_GREEN') || define('COLOR_GREEN', "\033[92m");
defined('COLOR_BLUE') || define('COLOR_BLUE', "\033[94m");
defined('COLOR_ORANGE') || define('COLOR_ORANGE', "\033[93m");
defined('COLOR_RED') || define('COLOR_RED', "\033[91m");
defined('COLOR_WHITE') || define('COLOR_WHITE', "\033[0m");
defined('COLOR_CYAN') || define('COLOR_CYAN', "\033[36m");

/** Cleaner */
require_once(SMProtocol_PATH.'/cleanup.php');
/** Require interface and extended class */
require_once(SMProtocol_PATH.'/interfaces/hook.php');
require_once(SMProtocol_PATH.'/server/sender.php');
require_once(SMProtocol_PATH . '/abstracts/hook.php');
require_once(SMProtocol_PATH.'/interfaces/definition.php');
require_once(SMProtocol_PATH . '/abstracts/definition.php');

/** Require_exception files */
require_once(SMProtocol_PATH.'/exception/SMProtocol.php');
require_once(SMProtocol_PATH.'/exception/server.php');
require_once(SMProtocol_PATH.'/exception/client.php');
require_once(SMProtocol_PATH.'/exception/socket.php');

/** Require server, socket, signal files */
require_once(SMProtocol_PATH.'/server/signal.php');
require_once(SMProtocol_PATH.'/server/initialize.php');
require_once(SMProtocol_PATH.'/server/server.php');

/** Require download object */
require_once(APPLICATION_PATH.'/protocol/plugins/download/download.php');
require_once(APPLICATION_PATH.'/protocol/plugins/mysql.php');
/** Bootstrap application */
/** including all protocols in protocol directory */
require_once(SMProtocol_PATH.'/SMProtocol.php');

new \library\SMProtocol\SMProtocol();
