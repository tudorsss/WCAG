<?php
/**
 * Plugin Name: User Journey Testing Suite
 * Plugin URI: https://github.com/festool/user-journey-testing
 * Description: Comprehensive user journey testing tool for Festool web and app platforms
 * Version: 2.0.0
 * Author: BetterQA Team for Festool
 * Author URI: https://betterqa.co
 * License: GPL v2 or later
 * Text Domain: journey-testing
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('JOURNEY_TESTING_VERSION', '2.0.0');
define('JOURNEY_TESTING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JOURNEY_TESTING_PLUGIN_URL', plugin_dir_url(__FILE__));
define('JOURNEY_TESTING_PLUGIN_FILE', __FILE__);

// Autoloader
require_once JOURNEY_TESTING_PLUGIN_DIR . 'includes/class-autoloader.php';
Journey_Testing_Autoloader::register();

// Initialize the plugin
function journey_testing_init() {
    $plugin = new Journey_Testing_Plugin();
    $plugin->run();
}
add_action('plugins_loaded', 'journey_testing_init');

// Activation hook
register_activation_hook(__FILE__, array('Journey_Testing_Activator', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('Journey_Testing_Deactivator', 'deactivate'));