<?php
/**
 * Test Run Model
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/includes/models
 */

class Journey_Testing_Test_Run {
    
    /**
     * Get all test runs
     */
    public static function get_all($args = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_runs';
        
        $defaults = array(
            'limit' => 20,
            'offset' => 0,
            'status' => null,
            'tester_id' => null,
            'journey_id' => null,
            'platform_id' => null,
            'orderby' => 'started_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where_clauses = array('1=1');
        $where_values = array();
        
        if ($args['status']) {
            $where_clauses[] = 'r.status = %s';
            $where_values[] = $args['status'];
        }
        
        if ($args['tester_id']) {
            $where_clauses[] = 'r.tester_id = %d';
            $where_values[] = $args['tester_id'];
        }
        
        if ($args['journey_id']) {
            $where_clauses[] = 'r.journey_id = %d';
            $where_values[] = $args['journey_id'];
        }
        
        if ($args['platform_id']) {
            $where_clauses[] = 'j.platform_id = %d';
            $where_values[] = $args['platform_id'];
        }
        
        $where_sql = implode(' AND ', $where_clauses);
        
        $query = "SELECT r.*, j.name as journey_name, j.platform_id, 
                         p.name as platform_name, p.icon as platform_icon,
                         u.display_name as tester_name
                  FROM {$wpdb->prefix}journey_test_runs r
                  LEFT JOIN {$wpdb->prefix}journey_definitions j ON r.journey_id = j.id
                  LEFT JOIN {$wpdb->prefix}journey_platforms p ON j.platform_id = p.id
                  LEFT JOIN {$wpdb->users} u ON r.tester_id = u.ID
                  WHERE $where_sql
                  ORDER BY r.{$args['orderby']} {$args['order']}
                  LIMIT %d OFFSET %d";
        
        $where_values[] = $args['limit'];
        $where_values[] = $args['offset'];
        
        return $wpdb->get_results(
            $wpdb->prepare($query, $where_values),
            ARRAY_A
        );
    }
    
    /**
     * Get a test run by ID
     */
    public static function get($run_id) {
        global $wpdb;
        
        $query = "SELECT r.*, j.name as journey_name, j.platform_id, 
                         p.name as platform_name, p.icon as platform_icon,
                         u.display_name as tester_name
                  FROM {$wpdb->prefix}journey_test_runs r
                  LEFT JOIN {$wpdb->prefix}journey_definitions j ON r.journey_id = j.id
                  LEFT JOIN {$wpdb->prefix}journey_platforms p ON j.platform_id = p.id
                  LEFT JOIN {$wpdb->users} u ON r.tester_id = u.ID
                  WHERE r.id = %d";
        
        return $wpdb->get_row(
            $wpdb->prepare($query, $run_id),
            ARRAY_A
        );
    }
    
    /**
     * Create a new test run
     */
    public static function create($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_runs';
        
        $wpdb->insert($table_name, array(
            'journey_id' => $data['journey_id'],
            'tester_id' => isset($data['tester_id']) ? $data['tester_id'] : get_current_user_id(),
            'status' => 'in_progress',
            'notes' => isset($data['notes']) ? $data['notes'] : ''
        ));
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update a test run
     */
    public static function update($run_id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_runs';
        
        // Don't allow updating certain fields directly
        unset($data['id']);
        unset($data['started_at']);
        
        // If completing the run, set completed_at
        if (isset($data['status']) && $data['status'] === 'completed' && !isset($data['completed_at'])) {
            $data['completed_at'] = current_time('mysql');
        }
        
        return $wpdb->update(
            $table_name,
            $data,
            array('id' => $run_id)
        );
    }
    
    /**
     * Complete a test run
     */
    public static function complete($run_id, $notes = '') {
        return self::update($run_id, array(
            'status' => 'completed',
            'notes' => $notes
        ));
    }
    
    /**
     * Get test run progress
     */
    public static function get_progress($run_id) {
        global $wpdb;
        $results_table = $wpdb->prefix . 'journey_test_results';
        $steps_table = $wpdb->prefix . 'journey_test_steps';
        
        // Get the journey ID
        $run = self::get($run_id);
        if (!$run) {
            return null;
        }
        
        // Get total steps
        $total_steps = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $steps_table WHERE journey_id = %d",
                $run['journey_id']
            )
        );
        
        // Get completed steps
        $completed_steps = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT test_step_id) FROM $results_table WHERE test_run_id = %d",
                $run_id
            )
        );
        
        // Get results breakdown
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT result, COUNT(*) as count 
                FROM $results_table 
                WHERE test_run_id = %d 
                GROUP BY result",
                $run_id
            ),
            ARRAY_A
        );
        
        $breakdown = array(
            'pass' => 0,
            'fail' => 0,
            'blocked' => 0,
            'skipped' => 0
        );
        
        foreach ($results as $result) {
            $breakdown[$result['result']] = (int) $result['count'];
        }
        
        return array(
            'total_steps' => (int) $total_steps,
            'completed_steps' => (int) $completed_steps,
            'progress_percentage' => $total_steps > 0 ? round(($completed_steps / $total_steps) * 100) : 0,
            'results' => $breakdown
        );
    }
    
    /**
     * Get active test run for a user
     */
    public static function get_active_for_user($user_id = null) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_runs';
        
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name 
                WHERE tester_id = %d AND status = 'in_progress' 
                ORDER BY started_at DESC 
                LIMIT 1",
                $user_id
            ),
            ARRAY_A
        );
    }
}