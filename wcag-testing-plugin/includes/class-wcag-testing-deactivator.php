<?php
/**
 * Fired during plugin deactivation
 */
class WCAG_Testing_Deactivator {
    
    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        // Clear any scheduled events
        wp_clear_scheduled_hook('wcag_testing_cleanup');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}