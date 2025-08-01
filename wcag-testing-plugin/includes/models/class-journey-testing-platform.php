<?php
/**
 * Platform Model
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/includes/models
 */

class Journey_Testing_Platform {
    
    /**
     * Get all platforms
     */
    public static function get_all() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_platforms';
        
        return $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY display_order ASC",
            ARRAY_A
        );
    }
    
    /**
     * Get a platform by ID
     */
    public static function get($platform_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_platforms';
        
        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $platform_id),
            ARRAY_A
        );
    }
    
    /**
     * Get a platform by slug
     */
    public static function get_by_slug($slug) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_platforms';
        
        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE slug = %s", $slug),
            ARRAY_A
        );
    }
    
    /**
     * Create a new platform
     */
    public static function create($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_platforms';
        
        $wpdb->insert($table_name, array(
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => isset($data['description']) ? $data['description'] : '',
            'icon' => isset($data['icon']) ? $data['icon'] : 'dashicons-admin-generic',
            'display_order' => isset($data['display_order']) ? $data['display_order'] : 0
        ));
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update a platform
     */
    public static function update($platform_id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_platforms';
        
        return $wpdb->update(
            $table_name,
            $data,
            array('id' => $platform_id)
        );
    }
    
    /**
     * Delete a platform
     */
    public static function delete($platform_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_platforms';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $platform_id)
        );
    }
}