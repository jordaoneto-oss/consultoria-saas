<?php

define('CP_PLUGIN_DIR', dirname(__DIR__) . '/wp-content/plugins/consultoria-platform/');
define('CP_VERSION', '1.0.0');
define('CP_DB_VERSION', '1.0.0');

require_once CP_PLUGIN_DIR . 'src/Helpers/Logger.php';
require_once CP_PLUGIN_DIR . 'src/Helpers/Functions.php';
require_once CP_PLUGIN_DIR . 'src/Exceptions/BaseException.php';
