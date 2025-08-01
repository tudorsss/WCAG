<?php
/**
 * Fired during plugin activation
 */
class WCAG_Testing_Activator {
    
    /**
     * Plugin activation
     */
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Reports table
        $table_name = $wpdb->prefix . 'wcag_reports';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            url varchar(255) NOT NULL,
            test_date datetime DEFAULT CURRENT_TIMESTAMP,
            level varchar(10) NOT NULL,
            passed_count int(11) DEFAULT 0,
            failed_count int(11) DEFAULT 0,
            warning_count int(11) DEFAULT 0,
            report_data longtext,
            tester_name varchar(100),
            status varchar(20) DEFAULT 'draft',
            PRIMARY KEY (id),
            KEY url (url),
            KEY test_date (test_date)
        ) $charset_collate;";
        
        // Issues table
        $issues_table = $wpdb->prefix . 'wcag_issues';
        $sql2 = "CREATE TABLE $issues_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            report_id mediumint(9) NOT NULL,
            criterion varchar(20) NOT NULL,
            level varchar(10) NOT NULL,
            severity varchar(20) NOT NULL,
            element text,
            description text,
            recommendation text,
            status varchar(20) DEFAULT 'open',
            PRIMARY KEY (id),
            KEY report_id (report_id),
            KEY criterion (criterion)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        dbDelta($sql2);
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        add_option('wcag_testing_default_level', 'AA');
        add_option('wcag_testing_enable_toolbar', 'yes');
        add_option('wcag_testing_auto_save', 'yes');
        add_option('wcag_testing_email_notifications', 'no');
    }
}