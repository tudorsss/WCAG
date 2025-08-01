<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/admin
 */

class Journey_Testing_Admin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        // Include model classes
        $this->include_models();
    }
    
    /**
     * Include model classes
     */
    private function include_models() {
        require_once JOURNEY_TESTING_PLUGIN_DIR . 'includes/models/class-journey-testing-platform.php';
        require_once JOURNEY_TESTING_PLUGIN_DIR . 'includes/models/class-journey-testing-journey.php';
        require_once JOURNEY_TESTING_PLUGIN_DIR . 'includes/models/class-journey-testing-test-step.php';
        require_once JOURNEY_TESTING_PLUGIN_DIR . 'includes/models/class-journey-testing-test-run.php';
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, JOURNEY_TESTING_PLUGIN_URL . 'assets/css/admin.css', array(), $this->version, 'all');
        wp_enqueue_style('wp-color-picker');
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, JOURNEY_TESTING_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), $this->version, false);
        wp_enqueue_media();
        
        // Localize script
        wp_localize_script($this->plugin_name, 'journey_testing_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('journey_testing_ajax'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this?', 'journey-testing'),
                'saving' => __('Saving...', 'journey-testing'),
                'saved' => __('Saved!', 'journey-testing'),
                'error' => __('An error occurred. Please try again.', 'journey-testing'),
                'uploading' => __('Uploading...', 'journey-testing'),
                'upload_complete' => __('Upload complete!', 'journey-testing'),
                'file_too_large' => __('File size exceeds the maximum allowed.', 'journey-testing'),
                'invalid_file_type' => __('Invalid file type.', 'journey-testing')
            )
        ));
    }

    /**
     * Register the administration menu for this plugin.
     */
    public function add_plugin_admin_menu() {
        // Main menu
        add_menu_page(
            __('Journey Testing', 'journey-testing'),
            __('Journey Testing', 'journey-testing'),
            'manage_options',
            'journey-testing',
            array($this, 'display_dashboard_page'),
            'dashicons-chart-line',
            30
        );
        
        // Dashboard (rename the first submenu)
        add_submenu_page(
            'journey-testing',
            __('Dashboard', 'journey-testing'),
            __('Dashboard', 'journey-testing'),
            'manage_options',
            'journey-testing',
            array($this, 'display_dashboard_page')
        );
        
        // Platforms submenu
        $platforms = Journey_Testing_Platform::get_all();
        foreach ($platforms as $platform) {
            add_submenu_page(
                'journey-testing',
                sprintf(__('%s Tests', 'journey-testing'), $platform['name']),
                sprintf('<span class="dashicons %s"></span> %s', $platform['icon'], $platform['name']),
                'manage_options',
                'journey-testing-platform-' . $platform['slug'],
                array($this, 'display_platform_page')
            );
        }
        
        // Separator
        add_submenu_page(
            'journey-testing',
            '',
            '<span style="display:block; margin:1px 0 1px -5px; padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>',
            'manage_options',
            '#'
        );
        
        // New Test Run
        add_submenu_page(
            'journey-testing',
            __('New Test Run', 'journey-testing'),
            __('New Test Run', 'journey-testing'),
            'manage_options',
            'journey-testing-new-run',
            array($this, 'display_new_run_page')
        );
        
        // Test Runs
        add_submenu_page(
            'journey-testing',
            __('Test Runs', 'journey-testing'),
            __('Test Runs', 'journey-testing'),
            'manage_options',
            'journey-testing-runs',
            array($this, 'display_runs_page')
        );
        
        // Reports
        add_submenu_page(
            'journey-testing',
            __('Reports', 'journey-testing'),
            __('Reports', 'journey-testing'),
            'manage_options',
            'journey-testing-reports',
            array($this, 'display_reports_page')
        );
        
        // Manage Journeys
        add_submenu_page(
            'journey-testing',
            __('Manage Journeys', 'journey-testing'),
            __('Manage Journeys', 'journey-testing'),
            'manage_options',
            'journey-testing-manage',
            array($this, 'display_manage_page')
        );
        
        // Settings
        add_submenu_page(
            'journey-testing',
            __('Settings', 'journey-testing'),
            __('Settings', 'journey-testing'),
            'manage_options',
            'journey-testing-settings',
            array($this, 'display_settings_page')
        );
        
        // Hidden pages (not shown in menu)
        add_submenu_page(
            null, // parent slug null to hide from menu
            __('Execute Test', 'journey-testing'),
            __('Execute Test', 'journey-testing'),
            'manage_options',
            'journey-testing-execute',
            array($this, 'display_execute_page')
        );
        
        add_submenu_page(
            null, // parent slug null to hide from menu
            __('Test Report', 'journey-testing'),
            __('Test Report', 'journey-testing'),
            'manage_options',
            'journey-testing-report',
            array($this, 'display_report_page')
        );
    }
    
    /**
     * Display the dashboard page
     */
    public function display_dashboard_page() {
        include_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    /**
     * Display a platform page
     */
    public function display_platform_page() {
        $page = $_GET['page'];
        $platform_slug = str_replace('journey-testing-platform-', '', $page);
        $platform = Journey_Testing_Platform::get_by_slug($platform_slug);
        
        if (!$platform) {
            wp_die(__('Platform not found.', 'journey-testing'));
        }
        
        include_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/views/platform.php';
    }
    
    /**
     * Display the new test run page
     */
    public function display_new_run_page() {
        include_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/views/new-run.php';
    }
    
    /**
     * Display the test runs page
     */
    public function display_runs_page() {
        include_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/views/runs.php';
    }
    
    /**
     * Display the reports page
     */
    public function display_reports_page() {
        include_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/views/reports.php';
    }
    
    /**
     * Display the manage journeys page
     */
    public function display_manage_page() {
        include_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/views/manage.php';
    }
    
    /**
     * Display the settings page
     */
    public function display_settings_page() {
        include_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/views/settings.php';
    }
    
    /**
     * Display the test execution page
     */
    public function display_execute_page() {
        include_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/views/execute.php';
    }
    
    /**
     * Display the test report page
     */
    public function display_report_page() {
        include_once JOURNEY_TESTING_PLUGIN_DIR . 'admin/views/report.php';
    }
    
    /**
     * Get all journeys grouped by platform
     */
    public function get_all_journeys_grouped() {
        $platforms = Journey_Testing_Platform::get_all();
        $grouped = array();
        
        foreach ($platforms as $platform) {
            $journeys = Journey_Testing_Journey::get_by_platform($platform['id']);
            foreach ($journeys as &$journey) {
                $journey['steps'] = Journey_Testing_Test_Step::get_by_journey($journey['id']);
            }
            $grouped[$platform['id']] = $journeys;
        }
        
        return $grouped;
    }
    
    /**
     * AJAX handler for saving test results
     */
    public function ajax_save_test_result() {
        check_ajax_referer('journey_testing_ajax', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'journey-testing'));
        }
        
        $test_run_id = intval($_POST['test_run_id']);
        $test_step_id = intval($_POST['test_step_id']);
        $result = sanitize_text_field($_POST['result']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_results';
        
        // Check if result already exists
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM $table_name WHERE test_run_id = %d AND test_step_id = %d",
                $test_run_id,
                $test_step_id
            )
        );
        
        if ($existing) {
            // Update existing result
            $wpdb->update(
                $table_name,
                array(
                    'result' => $result,
                    'notes' => $notes,
                    'tested_at' => current_time('mysql')
                ),
                array('id' => $existing->id)
            );
            $result_id = $existing->id;
        } else {
            // Insert new result
            $wpdb->insert(
                $table_name,
                array(
                    'test_run_id' => $test_run_id,
                    'test_step_id' => $test_step_id,
                    'result' => $result,
                    'notes' => $notes
                )
            );
            $result_id = $wpdb->insert_id;
        }
        
        // Get updated progress
        $progress = Journey_Testing_Test_Run::get_progress($test_run_id);
        
        wp_send_json_success(array(
            'result_id' => $result_id,
            'progress' => $progress
        ));
    }
    
    /**
     * AJAX handler for uploading attachments
     */
    public function ajax_upload_attachment() {
        check_ajax_referer('journey_testing_ajax', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'journey-testing'));
        }
        
        if (!isset($_FILES['file'])) {
            wp_send_json_error(__('No file uploaded', 'journey-testing'));
        }
        
        $file = $_FILES['file'];
        $object_type = sanitize_text_field($_POST['object_type']);
        $object_id = intval($_POST['object_id']);
        
        // Check file size
        $max_size = get_option('journey_testing_max_file_size', 10485760);
        if ($file['size'] > $max_size) {
            wp_send_json_error(__('File size exceeds maximum allowed', 'journey-testing'));
        }
        
        // Check file type
        $allowed_types = get_option('journey_testing_allowed_file_types', array('jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'pdf'));
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) {
            wp_send_json_error(__('Invalid file type', 'journey-testing'));
        }
        
        // Upload file
        $upload_dir = wp_upload_dir();
        $journey_dir = $upload_dir['basedir'] . '/' . get_option('journey_testing_upload_dir', 'journey-testing-uploads');
        
        if (!file_exists($journey_dir)) {
            wp_mkdir_p($journey_dir);
        }
        
        $filename = wp_unique_filename($journey_dir, $file['name']);
        $filepath = $journey_dir . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Save to database
            global $wpdb;
            $table_name = $wpdb->prefix . 'journey_attachments';
            
            $wpdb->insert(
                $table_name,
                array(
                    'object_type' => $object_type,
                    'object_id' => $object_id,
                    'file_name' => $filename,
                    'file_path' => $filepath,
                    'file_type' => $file['type'],
                    'file_size' => $file['size'],
                    'uploaded_by' => get_current_user_id()
                )
            );
            
            $attachment_id = $wpdb->insert_id;
            $file_url = $upload_dir['baseurl'] . '/' . get_option('journey_testing_upload_dir', 'journey-testing-uploads') . '/' . $filename;
            
            wp_send_json_success(array(
                'attachment_id' => $attachment_id,
                'file_url' => $file_url,
                'file_name' => $filename
            ));
        } else {
            wp_send_json_error(__('Failed to upload file', 'journey-testing'));
        }
    }
}