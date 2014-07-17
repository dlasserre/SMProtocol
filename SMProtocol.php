#!/usr/bin/php
<?php
/** Global defined */
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(__DIR__));

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

/** Require interface and extended class */
require_once(APPLICATION_PATH.'/src/interfaces/hook.php');
require_once(APPLICATION_PATH.'/src/server/sender.php');
require_once(APPLICATION_PATH . '/src/abstracts/hook.php');
require_once(APPLICATION_PATH.'/src/interfaces/definition.php');
require_once(APPLICATION_PATH . '/src/abstracts/definition.php');

/** Require_exception files */
require_once(APPLICATION_PATH.'/src/exception/SMProtocol.php');
require_once(APPLICATION_PATH.'/src/exception/server.php');
require_once(APPLICATION_PATH.'/src/exception/client.php');
require_once(APPLICATION_PATH.'/src/exception/socket.php');

/** Require server, socket, signal files */
require_once(APPLICATION_PATH.'/src/server/signal.php');
require_once(APPLICATION_PATH.'/src/server/initialize.php');
require_once(APPLICATION_PATH.'/src/server/server.php');
require_once(APPLICATION_PATH.'/src/server/socket.php');

/** Bootstrap application */
/** including all protocols in protocol directory */
require_once(APPLICATION_PATH.'/src/SMProtocol.php');

new \engine\SMProtocol();
