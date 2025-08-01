<?php
/**
 * Journey Model
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/includes/models
 */

class Journey_Testing_Journey {
    
    /**
     * Get all journeys for a platform
     */
    public static function get_by_platform($platform_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_definitions';
        
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name 
                WHERE platform_id = %d AND status = 'active' 
                ORDER BY display_order ASC",
                $platform_id
            ),
            ARRAY_A
        );
    }
    
    /**
     * Get a journey by ID
     */
    public static function get($journey_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_definitions';
        
        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $journey_id),
            ARRAY_A
        );
    }
    
    /**
     * Get a journey with its platform info
     */
    public static function get_with_platform($journey_id) {
        global $wpdb;
        $journeys_table = $wpdb->prefix . 'journey_definitions';
        $platforms_table = $wpdb->prefix . 'journey_platforms';
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT j.*, p.name as platform_name, p.slug as platform_slug, p.icon as platform_icon
                FROM $journeys_table j
                LEFT JOIN $platforms_table p ON j.platform_id = p.id
                WHERE j.id = %d",
                $journey_id
            ),
            ARRAY_A
        );
    }
    
    /**
     * Create a new journey
     */
    public static function create($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_definitions';
        
        $wpdb->insert($table_name, array(
            'platform_id' => $data['platform_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => isset($data['description']) ? $data['description'] : '',
            'version' => isset($data['version']) ? $data['version'] : '1.0.0',
            'status' => isset($data['status']) ? $data['status'] : 'active',
            'display_order' => isset($data['display_order']) ? $data['display_order'] : 0,
            'created_by' => get_current_user_id()
        ));
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update a journey
     */
    public static function update($journey_id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_definitions';
        
        // Don't allow updating certain fields directly
        unset($data['id']);
        unset($data['created_by']);
        unset($data['created_at']);
        
        return $wpdb->update(
            $table_name,
            $data,
            array('id' => $journey_id)
        );
    }
    
    /**
     * Delete a journey (soft delete by setting status)
     */
    public static function delete($journey_id) {
        return self::update($journey_id, array('status' => 'deleted'));
    }
    
    /**
     * Get journey statistics
     */
    public static function get_statistics($journey_id) {
        global $wpdb;
        $runs_table = $wpdb->prefix . 'journey_test_runs';
        $results_table = $wpdb->prefix . 'journey_test_results';
        $steps_table = $wpdb->prefix . 'journey_test_steps';
        
        // Get total runs
        $total_runs = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $runs_table WHERE journey_id = %d",
                $journey_id
            )
        );
        
        // Get completed runs
        $completed_runs = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $runs_table 
                WHERE journey_id = %d AND status = 'completed'",
                $journey_id
            )
        );
        
        // Get latest run info
        $latest_run = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $runs_table 
                WHERE journey_id = %d 
                ORDER BY started_at DESC 
                LIMIT 1",
                $journey_id
            ),
            ARRAY_A
        );
        
        // Get pass rate from latest completed run
        $pass_rate = 0;
        if ($latest_run && $latest_run['status'] === 'completed') {
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT result, COUNT(*) as count 
                    FROM $results_table 
                    WHERE test_run_id = %d 
                    GROUP BY result",
                    $latest_run['id']
                ),
                ARRAY_A
            );
            
            $total = 0;
            $passed = 0;
            foreach ($results as $result) {
                $total += $result['count'];
                if ($result['result'] === 'pass') {
                    $passed = $result['count'];
                }
            }
            
            if ($total > 0) {
                $pass_rate = round(($passed / $total) * 100, 1);
            }
        }
        
        return array(
            'total_runs' => $total_runs,
            'completed_runs' => $completed_runs,
            'latest_run' => $latest_run,
            'pass_rate' => $pass_rate
        );
    }
}