<?php
/**
 * The core plugin class
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks, public-facing site hooks,
 * and register all actions and filters.
 */
class Journey_Testing_Plugin {
    
    /**
     * The loader that's responsible for maintaining and registering all hooks.
     */
    protected $loader;
    
    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;
    
    /**
     * The current version of the plugin.
     */
    protected $version;
    
    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        $this->version = JOURNEY_TESTING_VERSION;
        $this->plugin_name = 'journey-testing';
        
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }
    
    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters.
         */
        require_once JOURNEY_TESTING_PLUGIN_DIR . 'includes/class-journey-testing-loader.php';
        
        /**
         * The class responsible for defining all actions in the admin area.
         */
        require_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/class-journey-testing-admin.php';
        
        /**
         * The class responsible for defining all actions in the public area.
         */
        require_once JOURNEY_TESTING_PLUGIN_DIR . 'public/class-journey-testing-public.php';
        
        $this->loader = new Journey_Testing_Loader();
    }
    
    /**
     * Register all of the hooks related to the admin area functionality.
     */
    private function define_admin_hooks() {
        $plugin_admin = new Journey_Testing_Admin($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        
        // AJAX handlers
        $this->loader->add_action('wp_ajax_journey_save_test_result', $plugin_admin, 'ajax_save_test_result');
        $this->loader->add_action('wp_ajax_journey_upload_attachment', $plugin_admin, 'ajax_upload_attachment');
        $this->loader->add_action('wp_ajax_journey_get_test_steps', $plugin_admin, 'ajax_get_test_steps');
        $this->loader->add_action('wp_ajax_journey_complete_test_run', $plugin_admin, 'ajax_complete_test_run');
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        $plugin_public = new Journey_Testing_Public($this->get_plugin_name(), $this->get_version());
        
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }
    
    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }
    
    /**
     * The name of the plugin used to uniquely identify it.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }
    
    /**
     * The reference to the class that orchestrates the hooks.
     */
    public function get_loader() {
        return $this->loader;
    }
    
    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}