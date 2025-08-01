<?php
/**
 * Public-facing functionality
 */
class WCAG_Testing_Public {
    
    /**
     * Plugin version
     */
    private $version;
    
    /**
     * Constructor
     */
    public function __construct($version) {
        $this->version = $version;
    }
    
    /**
     * Register public styles
     */
    public function enqueue_styles() {
        if ($this->should_show_toolbar()) {
            wp_enqueue_style(
                'wcag-testing-public',
                WCAG_TESTING_PLUGIN_URL . 'assets/css/public.css',
                array(),
                $this->version,
                'all'
            );
        }
    }
    
    /**
     * Register public scripts
     */
    public function enqueue_scripts() {
        if ($this->should_show_toolbar()) {
            wp_enqueue_script(
                'wcag-testing-public',
                WCAG_TESTING_PLUGIN_URL . 'assets/js/public.js',
                array('jquery'),
                $this->version,
                true
            );
            
            wp_localize_script('wcag-testing-public', 'wcag_testing_public', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wcag_testing_public_nonce'),
                'criteria' => WCAG_Testing_Criteria::get_criteria_by_level(get_option('wcag_testing_default_level', 'AA')),
                'strings' => array(
                    'testing' => __('Testing...', 'wcag-testing'),
                    'pass' => __('Pass', 'wcag-testing'),
                    'fail' => __('Fail', 'wcag-testing'),
                    'warning' => __('Warning', 'wcag-testing'),
                    'na' => __('N/A', 'wcag-testing'),
                    'expand' => __('Expand', 'wcag-testing'),
                    'collapse' => __('Collapse', 'wcag-testing'),
                    'run_test' => __('Run Test', 'wcag-testing'),
                    'save_report' => __('Save Report', 'wcag-testing'),
                    'export' => __('Export', 'wcag-testing'),
                    'minimize' => __('Minimize', 'wcag-testing'),
                    'maximize' => __('Maximize', 'wcag-testing')
                )
            ));
        }
    }
    
    /**
     * Add testing toolbar to footer
     */
    public function add_testing_toolbar() {
        if ($this->should_show_toolbar()) {
            require_once WCAG_TESTING_PLUGIN_DIR . 'templates/public-toolbar.php';
        }
    }
    
    /**
     * Check if toolbar should be shown
     */
    private function should_show_toolbar() {
        // Check if toolbar is enabled
        if (get_option('wcag_testing_enable_toolbar', 'yes') !== 'yes') {
            return false;
        }
        
        // Check if user has permission
        if (!current_user_can('manage_options')) {
            return false;
        }
        
        // Don't show in admin
        if (is_admin()) {
            return false;
        }
        
        return true;
    }
}