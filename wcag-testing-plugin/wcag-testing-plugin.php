<?php
/**
 * Plugin Name: WCAG 2.2 Testing Suite
 * Plugin URI: https://github.com/yourdomain/wcag-testing-plugin
 * Description: Comprehensive WCAG 2.2 accessibility testing tool for WordPress sites
 * Version: 1.0.0
 * Author: BetterQA Team
 * Author URI: https://betterqa.co
 * License: GPL v2 or later
 * Text Domain: wcag-testing
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WCAG_TESTING_VERSION', '1.0.0');
define('WCAG_TESTING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WCAG_TESTING_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WCAG_TESTING_PLUGIN_FILE', __FILE__);

// Autoloader
require_once WCAG_TESTING_PLUGIN_DIR . 'includes/class-autoloader.php';
WCAG_Testing_Autoloader::register();

// Initialize the plugin
function wcag_testing_init() {
    $plugin = new WCAG_Testing_Plugin();
    $plugin->run();
}
add_action('plugins_loaded', 'wcag_testing_init');

// Activation hook
register_activation_hook(__FILE__, array('WCAG_Testing_Activator', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('WCAG_Testing_Deactivator', 'deactivate'));