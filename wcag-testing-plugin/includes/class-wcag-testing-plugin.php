<?php
/**
 * Main plugin class
 */
class WCAG_Testing_Plugin {
    
    /**
     * Plugin version
     */
    protected $version;
    
    /**
     * Plugin loader
     */
    protected $loader;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->version = WCAG_TESTING_VERSION;
        $this->loader = new WCAG_Testing_Loader();
        
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }
    
    /**
     * Set plugin locale
     */
    private function set_locale() {
        $plugin_i18n = new WCAG_Testing_I18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }
    
    /**
     * Register admin hooks
     */
    private function define_admin_hooks() {
        $plugin_admin = new WCAG_Testing_Admin($this->version);
        
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('wp_ajax_wcag_run_test', $plugin_admin, 'ajax_run_test');
        $this->loader->add_action('wp_ajax_wcag_save_report', $plugin_admin, 'ajax_save_report');
        $this->loader->add_action('wp_ajax_wcag_export_report', $plugin_admin, 'ajax_export_report');
    }
    
    /**
     * Register public hooks
     */
    private function define_public_hooks() {
        $plugin_public = new WCAG_Testing_Public($this->version);
        
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('wp_footer', $plugin_public, 'add_testing_toolbar');
    }
    
    /**
     * Run the plugin
     */
    public function run() {
        $this->loader->run();
    }
    
    /**
     * Get plugin version
     */
    public function get_version() {
        return $this->version;
    }
}