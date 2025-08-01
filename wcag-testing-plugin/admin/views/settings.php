<?php
/**
 * Settings view
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/admin/views
 */

// Handle form submission
if (isset($_POST['save_settings']) && wp_verify_nonce($_POST['settings_nonce'], 'journey_testing_settings')) {
    update_option('journey_testing_upload_dir', sanitize_text_field($_POST['upload_dir']));
    update_option('journey_testing_max_file_size', intval($_POST['max_file_size']) * 1048576); // Convert MB to bytes
    update_option('journey_testing_allowed_file_types', array_map('sanitize_text_field', explode(',', $_POST['allowed_file_types'])));
    
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'journey-testing') . '</p></div>';
}

// Get current settings
$upload_dir = get_option('journey_testing_upload_dir', 'journey-testing-uploads');
$max_file_size = get_option('journey_testing_max_file_size', 10485760) / 1048576; // Convert bytes to MB
$allowed_file_types = implode(', ', get_option('journey_testing_allowed_file_types', array('jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'pdf', 'doc', 'docx')));
?>

<div class="wrap journey-testing-settings">
    <h1><?php _e('Journey Testing Settings', 'journey-testing'); ?></h1>
    
    <form method="post">
        <?php wp_nonce_field('journey_testing_settings', 'settings_nonce'); ?>
        
        <h2><?php _e('File Upload Settings', 'journey-testing'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="upload_dir"><?php _e('Upload Directory', 'journey-testing'); ?></label>
                </th>
                <td>
                    <input type="text" id="upload_dir" name="upload_dir" value="<?php echo esc_attr($upload_dir); ?>" class="regular-text" />
                    <p class="description">
                        <?php _e('Directory name within wp-content/uploads/ where test attachments will be stored.', 'journey-testing'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="max_file_size"><?php _e('Maximum File Size', 'journey-testing'); ?></label>
                </th>
                <td>
                    <input type="number" id="max_file_size" name="max_file_size" value="<?php echo esc_attr($max_file_size); ?>" class="small-text" min="1" max="100" />
                    <?php _e('MB', 'journey-testing'); ?>
                    <p class="description">
                        <?php _e('Maximum file size allowed for attachments.', 'journey-testing'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="allowed_file_types"><?php _e('Allowed File Types', 'journey-testing'); ?></label>
                </th>
                <td>
                    <input type="text" id="allowed_file_types" name="allowed_file_types" value="<?php echo esc_attr($allowed_file_types); ?>" class="large-text" />
                    <p class="description">
                        <?php _e('Comma-separated list of allowed file extensions (e.g., jpg, png, pdf, mp4).', 'journey-testing'); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <h2><?php _e('Database Information', 'journey-testing'); ?></h2>
        <?php
        global $wpdb;
        $tables = array(
            'journey_platforms' => __('Platforms', 'journey-testing'),
            'journey_definitions' => __('Journey Definitions', 'journey-testing'),
            'journey_test_steps' => __('Test Steps', 'journey-testing'),
            'journey_test_runs' => __('Test Runs', 'journey-testing'),
            'journey_test_results' => __('Test Results', 'journey-testing'),
            'journey_issues' => __('Issues', 'journey-testing'),
            'journey_attachments' => __('Attachments', 'journey-testing')
        );
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Table', 'journey-testing'); ?></th>
                    <th><?php _e('Records', 'journey-testing'); ?></th>
                    <th><?php _e('Size', 'journey-testing'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $table => $name): 
                    $full_table_name = $wpdb->prefix . $table;
                    $count = $wpdb->get_var("SELECT COUNT(*) FROM $full_table_name");
                    
                    // Get table size
                    $size_result = $wpdb->get_row(
                        $wpdb->prepare(
                            "SELECT 
                                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                            FROM information_schema.TABLES 
                            WHERE table_schema = %s 
                            AND table_name = %s",
                            DB_NAME,
                            $full_table_name
                        )
                    );
                    $size = $size_result ? $size_result->size_mb . ' MB' : __('Unknown', 'journey-testing');
                ?>
                    <tr>
                        <td><strong><?php echo esc_html($name); ?></strong></td>
                        <td><?php echo number_format_i18n($count); ?></td>
                        <td><?php echo esc_html($size); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2><?php _e('Export/Import', 'journey-testing'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Export Data', 'journey-testing'); ?></th>
                <td>
                    <a href="<?php echo admin_url('admin.php?page=journey-testing-settings&export=journeys'); ?>" class="button">
                        <?php _e('Export Journey Definitions', 'journey-testing'); ?>
                    </a>
                    <p class="description">
                        <?php _e('Export all journey definitions and test steps as JSON.', 'journey-testing'); ?>
                    </p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="submit" name="save_settings" class="button button-primary">
                <?php _e('Save Settings', 'journey-testing'); ?>
            </button>
        </p>
    </form>
    
    <div class="journey-testing-info">
        <h2><?php _e('About Journey Testing Suite', 'journey-testing'); ?></h2>
        <p>
            <?php _e('Version:', 'journey-testing'); ?> <strong><?php echo JOURNEY_TESTING_VERSION; ?></strong><br>
            <?php _e('Developed for Festool GmbH by the BetterQA team.', 'journey-testing'); ?>
        </p>
        
        <h3><?php _e('Quick Guide', 'journey-testing'); ?></h3>
        <ol>
            <li><?php _e('Create or manage journeys from the Manage Journeys page', 'journey-testing'); ?></li>
            <li><?php _e('Start a new test run from New Test Run page', 'journey-testing'); ?></li>
            <li><?php _e('Execute tests step by step, recording results and issues', 'journey-testing'); ?></li>
            <li><?php _e('View comprehensive reports and export results', 'journey-testing'); ?></li>
        </ol>
    </div>
</div>

<style>
.journey-testing-settings {
    max-width: 800px;
}

.journey-testing-info {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin-top: 30px;
    border-radius: 4px;
}

.journey-testing-info h3 {
    margin-top: 20px;
}
</style>

<?php
// Handle export
if (isset($_GET['export']) && $_GET['export'] === 'journeys') {
    $export_data = array(
        'platforms' => Journey_Testing_Platform::get_all(),
        'journeys' => array()
    );
    
    foreach ($export_data['platforms'] as $platform) {
        $journeys = Journey_Testing_Journey::get_by_platform($platform['id']);
        foreach ($journeys as $journey) {
            $journey['test_steps'] = Journey_Testing_Test_Step::get_by_journey($journey['id']);
            $export_data['journeys'][] = $journey;
        }
    }
    
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="journey-testing-export-' . date('Y-m-d') . '.json"');
    echo json_encode($export_data, JSON_PRETTY_PRINT);
    exit;
}
?>