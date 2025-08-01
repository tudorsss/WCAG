<?php
/**
 * Test Step Model
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/includes/models
 */

class Journey_Testing_Test_Step {
    
    /**
     * Get all test steps for a journey
     */
    public static function get_by_journey($journey_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_steps';
        
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name 
                WHERE journey_id = %d 
                ORDER BY step_order ASC",
                $journey_id
            ),
            ARRAY_A
        );
    }
    
    /**
     * Get a test step by ID
     */
    public static function get($step_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_steps';
        
        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $step_id),
            ARRAY_A
        );
    }
    
    /**
     * Create a new test step
     */
    public static function create($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_steps';
        
        // Get the next order number if not provided
        if (!isset($data['step_order'])) {
            $max_order = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT MAX(step_order) FROM $table_name WHERE journey_id = %d",
                    $data['journey_id']
                )
            );
            $data['step_order'] = ($max_order !== null) ? $max_order + 1 : 0;
        }
        
        $wpdb->insert($table_name, array(
            'journey_id' => $data['journey_id'],
            'title' => $data['title'],
            'description' => isset($data['description']) ? $data['description'] : '',
            'expected_result' => isset($data['expected_result']) ? $data['expected_result'] : '',
            'step_order' => $data['step_order'],
            'is_required' => isset($data['is_required']) ? $data['is_required'] : 1
        ));
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update a test step
     */
    public static function update($step_id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_steps';
        
        // Don't allow updating certain fields directly
        unset($data['id']);
        unset($data['created_at']);
        
        return $wpdb->update(
            $table_name,
            $data,
            array('id' => $step_id)
        );
    }
    
    /**
     * Delete a test step
     */
    public static function delete($step_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_steps';
        
        // Get the step info before deleting
        $step = self::get($step_id);
        if (!$step) {
            return false;
        }
        
        // Delete the step
        $result = $wpdb->delete(
            $table_name,
            array('id' => $step_id)
        );
        
        // Reorder remaining steps
        if ($result) {
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE $table_name 
                    SET step_order = step_order - 1 
                    WHERE journey_id = %d AND step_order > %d",
                    $step['journey_id'],
                    $step['step_order']
                )
            );
        }
        
        return $result;
    }
    
    /**
     * Reorder test steps
     */
    public static function reorder($journey_id, $step_ids) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'journey_test_steps';
        
        foreach ($step_ids as $order => $step_id) {
            $wpdb->update(
                $table_name,
                array('step_order' => $order),
                array(
                    'id' => $step_id,
                    'journey_id' => $journey_id
                )
            );
        }
        
        return true;
    }
    
    /**
     * Duplicate steps from one journey to another
     */
    public static function duplicate_steps($source_journey_id, $target_journey_id) {
        $steps = self::get_by_journey($source_journey_id);
        
        foreach ($steps as $step) {
            $step['journey_id'] = $target_journey_id;
            unset($step['id']);
            unset($step['created_at']);
            unset($step['updated_at']);
            
            self::create($step);
        }
        
        return true;
    }
}