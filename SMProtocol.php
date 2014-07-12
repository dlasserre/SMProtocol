#!/usr/bin/php
<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(__DIR__));

/** Require interface and extended class */
require_once(APPLICATION_PATH.'/protocol/interfaces/interpret.php');
require_once(APPLICATION_PATH.'/protocol/definition.php');

/** Require_exception files */
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
