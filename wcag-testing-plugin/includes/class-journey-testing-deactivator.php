<?php
/**
 * Fired during plugin deactivation
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class Journey_Testing_Deactivator {

    /**
     * Deactivate the plugin.
     *
     * Currently just flushes rewrite rules. Database tables are preserved.
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Note: We don't delete database tables on deactivation
        // This preserves user data if they temporarily deactivate the plugin
    }
}