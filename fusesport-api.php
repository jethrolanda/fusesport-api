<?php

/**
 * Plugin Name: Fuse Sport API
 * Description: Fuse Sport API
 * Version: 1.0
 * Author: Jethrolanda
 * Author URI: jethrolanda.com
 * Text Domain: fusesport-api
 * Domain Path: /languages/
 * Requires at least: 5.7
 * Requires PHP: 7.2
 */

defined('ABSPATH') || exit;

// Path Constants ======================================================================================================

define('FSA_PLUGIN_URL',             plugins_url() . '/fusespot-api/');
define('FSA_PLUGIN_DIR',             plugin_dir_path(__FILE__));
define('FSA_CSS_ROOT_URL',           FSA_PLUGIN_URL . 'css/');
define('FSA_JS_ROOT_URL',            FSA_PLUGIN_URL . 'js/');
define('FSA_TEMPLATES_ROOT_URL',     FSA_PLUGIN_URL . 'templates/');
define('FSA_TEMPLATES_ROOT_DIR',     FSA_PLUGIN_DIR . 'templates/');
define('FSA_BLOCKS_ROOT_URL',        FSA_PLUGIN_URL . 'blocks/');
define('FSA_BLOCKS_ROOT_DIR',        FSA_PLUGIN_DIR . 'blocks/');

// Require autoloader
require_once 'inc/autoloader.php';

// Require settings
require_once "settings/my-first-gutenberg-app.php";

// Run
require_once 'fusesport-api.plugin.php';
$GLOBALS['wppb'] = new WP_Plugin_Boilerplate();
