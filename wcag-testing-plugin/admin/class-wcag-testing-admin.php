<?php
/**
 * Admin functionality
 */
class WCAG_Testing_Admin {
    
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
     * Register admin styles
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'wcag-testing-admin',
            WCAG_TESTING_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register admin scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'wcag-testing-admin',
            WCAG_TESTING_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-ajax-response'),
            $this->version,
            true
        );
        
        wp_localize_script('wcag-testing-admin', 'wcag_testing_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wcag_testing_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this report?', 'wcag-testing'),
                'testing' => __('Testing...', 'wcag-testing'),
                'saving' => __('Saving...', 'wcag-testing'),
                'error' => __('An error occurred. Please try again.', 'wcag-testing')
            )
        ));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('WCAG Testing', 'wcag-testing'),
            __('WCAG Testing', 'wcag-testing'),
            'manage_options',
            'wcag-testing',
            array($this, 'display_dashboard'),
            'dashicons-universal-access',
            30
        );
        
        add_submenu_page(
            'wcag-testing',
            __('Dashboard', 'wcag-testing'),
            __('Dashboard', 'wcag-testing'),
            'manage_options',
            'wcag-testing',
            array($this, 'display_dashboard')
        );
        
        add_submenu_page(
            'wcag-testing',
            __('New Test', 'wcag-testing'),
            __('New Test', 'wcag-testing'),
            'manage_options',
            'wcag-testing-new',
            array($this, 'display_new_test')
        );
        
        add_submenu_page(
            'wcag-testing',
            __('Reports', 'wcag-testing'),
            __('Reports', 'wcag-testing'),
            'manage_options',
            'wcag-testing-reports',
            array($this, 'display_reports')
        );
        
        add_submenu_page(
            'wcag-testing',
            __('Settings', 'wcag-testing'),
            __('Settings', 'wcag-testing'),
            'manage_options',
            'wcag-testing-settings',
            array($this, 'display_settings')
        );
    }
    
    /**
     * Display dashboard page
     */
    public function display_dashboard() {
        global $wpdb;
        
        // Get statistics
        $table_name = $wpdb->prefix . 'wcag_reports';
        $total_reports = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $recent_reports = $wpdb->get_results("SELECT * FROM $table_name ORDER BY test_date DESC LIMIT 5");
        
        // Get issue statistics
        $issues_table = $wpdb->prefix . 'wcag_issues';
        $open_issues = $wpdb->get_var("SELECT COUNT(*) FROM $issues_table WHERE status = 'open'");
        $critical_issues = $wpdb->get_var("SELECT COUNT(*) FROM $issues_table WHERE severity = 'critical' AND status = 'open'");
        
        require_once WCAG_TESTING_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }
    
    /**
     * Display new test page
     */
    public function display_new_test() {
        $criteria = WCAG_Testing_Criteria::get_all_criteria();
        $default_level = get_option('wcag_testing_default_level', 'AA');
        
        require_once WCAG_TESTING_PLUGIN_DIR . 'templates/admin-new-test.php';
    }
    
    /**
     * Display reports page
     */
    public function display_reports() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wcag_reports';
        $reports = $wpdb->get_results("SELECT * FROM $table_name ORDER BY test_date DESC");
        
        require_once WCAG_TESTING_PLUGIN_DIR . 'templates/admin-reports.php';
    }
    
    /**
     * Display settings page
     */
    public function display_settings() {
        if (isset($_POST['submit'])) {
            $this->save_settings();
        }
        
        $settings = array(
            'default_level' => get_option('wcag_testing_default_level', 'AA'),
            'enable_toolbar' => get_option('wcag_testing_enable_toolbar', 'yes'),
            'auto_save' => get_option('wcag_testing_auto_save', 'yes'),
            'email_notifications' => get_option('wcag_testing_email_notifications', 'no'),
            'notification_email' => get_option('wcag_testing_notification_email', get_option('admin_email'))
        );
        
        require_once WCAG_TESTING_PLUGIN_DIR . 'templates/admin-settings.php';
    }
    
    /**
     * Save settings
     */
    private function save_settings() {
        if (!wp_verify_nonce($_POST['wcag_settings_nonce'], 'wcag_settings')) {
            wp_die(__('Security check failed', 'wcag-testing'));
        }
        
        update_option('wcag_testing_default_level', sanitize_text_field($_POST['default_level']));
        update_option('wcag_testing_enable_toolbar', sanitize_text_field($_POST['enable_toolbar']));
        update_option('wcag_testing_auto_save', sanitize_text_field($_POST['auto_save']));
        update_option('wcag_testing_email_notifications', sanitize_text_field($_POST['email_notifications']));
        update_option('wcag_testing_notification_email', sanitize_email($_POST['notification_email']));
        
        add_settings_error('wcag_settings', 'settings_updated', __('Settings saved.', 'wcag-testing'), 'updated');
    }
    
    /**
     * AJAX handler for running tests
     */
    public function ajax_run_test() {
        check_ajax_referer('wcag_testing_nonce', 'nonce');
        
        $url = esc_url_raw($_POST['url']);
        $level = sanitize_text_field($_POST['level']);
        $criterion = sanitize_text_field($_POST['criterion']);
        
        // Here you would implement the actual testing logic
        // For now, we'll return a mock response
        $result = array(
            'success' => true,
            'status' => 'pass', // or 'fail', 'warning'
            'message' => __('Test completed successfully', 'wcag-testing'),
            'details' => __('All checks passed for this criterion', 'wcag-testing')
        );
        
        wp_send_json($result);
    }
    
    /**
     * AJAX handler for saving reports
     */
    public function ajax_save_report() {
        check_ajax_referer('wcag_testing_nonce', 'nonce');
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcag_reports';
        
        $data = array(
            'url' => esc_url_raw($_POST['url']),
            'level' => sanitize_text_field($_POST['level']),
            'passed_count' => intval($_POST['passed']),
            'failed_count' => intval($_POST['failed']),
            'warning_count' => intval($_POST['warnings']),
            'report_data' => wp_json_encode($_POST['report_data']),
            'tester_name' => sanitize_text_field($_POST['tester_name']),
            'status' => sanitize_text_field($_POST['status'])
        );
        
        $result = $wpdb->insert($table_name, $data);
        
        if ($result) {
            $report_id = $wpdb->insert_id;
            
            // Save individual issues
            if (!empty($_POST['issues'])) {
                $issues_table = $wpdb->prefix . 'wcag_issues';
                foreach ($_POST['issues'] as $issue) {
                    $issue_data = array(
                        'report_id' => $report_id,
                        'criterion' => sanitize_text_field($issue['criterion']),
                        'level' => sanitize_text_field($issue['level']),
                        'severity' => sanitize_text_field($issue['severity']),
                        'element' => sanitize_text_field($issue['element']),
                        'description' => sanitize_textarea_field($issue['description']),
                        'recommendation' => sanitize_textarea_field($issue['recommendation']),
                        'status' => 'open'
                    );
                    $wpdb->insert($issues_table, $issue_data);
                }
            }
            
            wp_send_json_success(array(
                'message' => __('Report saved successfully', 'wcag-testing'),
                'report_id' => $report_id
            ));
        } else {
            wp_send_json_error(__('Failed to save report', 'wcag-testing'));
        }
    }
    
    /**
     * AJAX handler for exporting reports
     */
    public function ajax_export_report() {
        check_ajax_referer('wcag_testing_nonce', 'nonce');
        
        $report_id = intval($_POST['report_id']);
        $format = sanitize_text_field($_POST['format']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'wcag_reports';
        $report = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $report_id));
        
        if (!$report) {
            wp_send_json_error(__('Report not found', 'wcag-testing'));
        }
        
        // Get issues
        $issues_table = $wpdb->prefix . 'wcag_issues';
        $issues = $wpdb->get_results($wpdb->prepare("SELECT * FROM $issues_table WHERE report_id = %d", $report_id));
        
        switch ($format) {
            case 'pdf':
                $export_url = $this->export_to_pdf($report, $issues);
                break;
            case 'csv':
                $export_url = $this->export_to_csv($report, $issues);
                break;
            case 'json':
                $export_url = $this->export_to_json($report, $issues);
                break;
            default:
                wp_send_json_error(__('Invalid export format', 'wcag-testing'));
        }
        
        wp_send_json_success(array(
            'download_url' => $export_url
        ));
    }
    
    /**
     * Export report to PDF
     */
    private function export_to_pdf($report, $issues) {
        // Implementation would use a PDF library
        // For now, return a placeholder
        return '#';
    }
    
    /**
     * Export report to CSV
     */
    private function export_to_csv($report, $issues) {
        $upload_dir = wp_upload_dir();
        $filename = 'wcag-report-' . $report->id . '-' . date('Y-m-d') . '.csv';
        $filepath = $upload_dir['path'] . '/' . $filename;
        
        $csv_data = array();
        $csv_data[] = array('WCAG 2.2 Accessibility Report');
        $csv_data[] = array('URL:', $report->url);
        $csv_data[] = array('Test Date:', $report->test_date);
        $csv_data[] = array('Level:', $report->level);
        $csv_data[] = array('');
        $csv_data[] = array('Summary');
        $csv_data[] = array('Passed:', $report->passed_count);
        $csv_data[] = array('Failed:', $report->failed_count);
        $csv_data[] = array('Warnings:', $report->warning_count);
        $csv_data[] = array('');
        $csv_data[] = array('Issues');
        $csv_data[] = array('Criterion', 'Level', 'Severity', 'Element', 'Description', 'Recommendation');
        
        foreach ($issues as $issue) {
            $csv_data[] = array(
                $issue->criterion,
                $issue->level,
                $issue->severity,
                $issue->element,
                $issue->description,
                $issue->recommendation
            );
        }
        
        $fp = fopen($filepath, 'w');
        foreach ($csv_data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        
        return $upload_dir['url'] . '/' . $filename;
    }
    
    /**
     * Export report to JSON
     */
    private function export_to_json($report, $issues) {
        $upload_dir = wp_upload_dir();
        $filename = 'wcag-report-' . $report->id . '-' . date('Y-m-d') . '.json';
        $filepath = $upload_dir['path'] . '/' . $filename;
        
        $json_data = array(
            'report' => $report,
            'issues' => $issues,
            'metadata' => array(
                'wcag_version' => '2.2',
                'plugin_version' => $this->version,
                'export_date' => date('Y-m-d H:i:s')
            )
        );
        
        file_put_contents($filepath, wp_json_encode($json_data, JSON_PRETTY_PRINT));
        
        return $upload_dir['url'] . '/' . $filename;
    }
}