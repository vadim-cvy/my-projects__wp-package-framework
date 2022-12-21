<?php

namespace YOUR_NAMESPACE;

use \YOUR_NAMESPACE\framework\inc\package\Package_Autoloader;

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/inc/package/Package_Autoloader.php';

new Package_Autoloader( __NAMESPACE__, __DIR__ . '/..' );