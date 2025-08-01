<?php
/**
 * Test Report view
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/admin/views
 */

// Get test run ID
$run_id = isset($_GET['run']) ? intval($_GET['run']) : 0;
if (!$run_id) {
    wp_die(__('Invalid test run.', 'journey-testing'));
}

// Get test run details
$test_run = Journey_Testing_Test_Run::get($run_id);
if (!$test_run) {
    wp_die(__('Test run not found.', 'journey-testing'));
}

// Get test steps and results
$test_steps = Journey_Testing_Test_Step::get_by_journey($test_run['journey_id']);

// Get all results for this test run
global $wpdb;
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT r.*, i.* 
         FROM {$wpdb->prefix}journey_test_results r
         LEFT JOIN {$wpdb->prefix}journey_issues i ON i.test_result_id = r.id
         WHERE r.test_run_id = %d",
        $run_id
    ),
    OBJECT_K
);

// Get progress
$progress = Journey_Testing_Test_Run::get_progress($run_id);

// Calculate statistics
$total_steps = count($test_steps);
$required_steps = 0;
$required_passed = 0;
$issues_by_severity = array('low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0);

foreach ($test_steps as $step) {
    if ($step['is_required']) {
        $required_steps++;
        if (isset($results[$step['id']]) && $results[$step['id']]->result === 'pass') {
            $required_passed++;
        }
    }
    
    if (isset($results[$step['id']]) && $results[$step['id']]->severity) {
        $issues_by_severity[$results[$step['id']]->severity]++;
    }
}

$overall_status = 'passed';
if ($progress['results']['fail'] > 0 || $required_passed < $required_steps) {
    $overall_status = 'failed';
} elseif ($progress['results']['blocked'] > 0) {
    $overall_status = 'blocked';
}

// Export functionality
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="test-report-' . $run_id . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Headers
    fputcsv($output, array('Step #', 'Step Title', 'Result', 'Required', 'Notes', 'Issue Severity', 'Issue Description'));
    
    // Data
    foreach ($test_steps as $index => $step) {
        $result = isset($results[$step['id']]) ? $results[$step['id']] : null;
        fputcsv($output, array(
            $index + 1,
            $step['title'],
            $result ? ucfirst($result->result) : 'Not Tested',
            $step['is_required'] ? 'Yes' : 'No',
            $result ? $result->notes : '',
            $result && $result->severity ? ucfirst($result->severity) : '',
            $result && $result->description ? $result->description : ''
        ));
    }
    
    fclose($output);
    exit;
}
?>

<div class="wrap journey-testing-report">
    <h1>
        <?php _e('Test Report', 'journey-testing'); ?>
        <a href="<?php echo add_query_arg('export', 'csv'); ?>" class="page-title-action">
            <?php _e('Export CSV', 'journey-testing'); ?>
        </a>
        <a href="javascript:window.print();" class="page-title-action">
            <?php _e('Print', 'journey-testing'); ?>
        </a>
    </h1>
    
    <!-- Report Header -->
    <div class="report-header">
        <div class="report-info">
            <h2><?php echo esc_html($test_run['journey_name']); ?></h2>
            <table class="report-meta">
                <tr>
                    <td><strong><?php _e('Platform:', 'journey-testing'); ?></strong></td>
                    <td>
                        <span class="dashicons <?php echo esc_attr($test_run['platform_icon']); ?>"></span>
                        <?php echo esc_html($test_run['platform_name']); ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php _e('Tester:', 'journey-testing'); ?></strong></td>
                    <td><?php echo esc_html($test_run['tester_name']); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Test Date:', 'journey-testing'); ?></strong></td>
                    <td>
                        <?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($test_run['started_at'])); ?>
                        <?php if ($test_run['completed_at']): ?>
                            - <?php echo date_i18n(get_option('time_format'), strtotime($test_run['completed_at'])); ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php _e('Status:', 'journey-testing'); ?></strong></td>
                    <td>
                        <span class="overall-status status-<?php echo $overall_status; ?>">
                            <?php echo ucfirst($overall_status); ?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="report-summary">
            <h3><?php _e('Test Summary', 'journey-testing'); ?></h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-value"><?php echo $progress['progress_percentage']; ?>%</span>
                    <span class="summary-label"><?php _e('Completion', 'journey-testing'); ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-value"><?php echo $required_passed; ?>/<?php echo $required_steps; ?></span>
                    <span class="summary-label"><?php _e('Required Steps Passed', 'journey-testing'); ?></span>
                </div>
            </div>
            
            <div class="results-breakdown">
                <div class="result-item pass">
                    <span class="result-value"><?php echo $progress['results']['pass']; ?></span>
                    <span class="result-label"><?php _e('Passed', 'journey-testing'); ?></span>
                </div>
                <div class="result-item fail">
                    <span class="result-value"><?php echo $progress['results']['fail']; ?></span>
                    <span class="result-label"><?php _e('Failed', 'journey-testing'); ?></span>
                </div>
                <div class="result-item blocked">
                    <span class="result-value"><?php echo $progress['results']['blocked']; ?></span>
                    <span class="result-label"><?php _e('Blocked', 'journey-testing'); ?></span>
                </div>
                <div class="result-item skipped">
                    <span class="result-value"><?php echo $progress['results']['skipped']; ?></span>
                    <span class="result-label"><?php _e('Skipped', 'journey-testing'); ?></span>
                </div>
            </div>
            
            <?php if ($progress['results']['fail'] > 0): ?>
                <div class="issues-summary">
                    <h4><?php _e('Issues by Severity', 'journey-testing'); ?></h4>
                    <div class="severity-breakdown">
                        <?php foreach ($issues_by_severity as $severity => $count): ?>
                            <?php if ($count > 0): ?>
                                <span class="severity-item severity-<?php echo $severity; ?>">
                                    <?php echo $count; ?> <?php echo ucfirst($severity); ?>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Test Results Details -->
    <div class="report-details">
        <h3><?php _e('Test Step Results', 'journey-testing'); ?></h3>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="column-step-number">#</th>
                    <th><?php _e('Test Step', 'journey-testing'); ?></th>
                    <th class="column-result"><?php _e('Result', 'journey-testing'); ?></th>
                    <th><?php _e('Notes', 'journey-testing'); ?></th>
                    <th class="column-attachments"><?php _e('Attachments', 'journey-testing'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($test_steps as $index => $step): 
                    $result = isset($results[$step['id']]) ? $results[$step['id']] : null;
                    $result_status = $result ? $result->result : 'not-tested';
                ?>
                    <tr class="test-step-row <?php echo 'result-' . $result_status; ?>">
                        <td class="column-step-number"><?php echo $index + 1; ?></td>
                        <td>
                            <strong><?php echo esc_html($step['title']); ?></strong>
                            <?php if ($step['is_required']): ?>
                                <span class="required-indicator">*</span>
                            <?php endif; ?>
                            
                            <?php if ($result && $result->result === 'fail' && $result->description): ?>
                                <div class="issue-details-inline">
                                    <div class="issue-header">
                                        <span class="dashicons dashicons-warning"></span>
                                        <?php _e('Issue:', 'journey-testing'); ?>
                                        <span class="severity-badge severity-<?php echo $result->severity; ?>">
                                            <?php echo ucfirst($result->severity); ?>
                                        </span>
                                    </div>
                                    <p class="issue-description"><?php echo esc_html($result->description); ?></p>
                                    
                                    <?php if ($result->steps_to_reproduce): ?>
                                        <p><strong><?php _e('Steps to Reproduce:', 'journey-testing'); ?></strong><br>
                                        <?php echo nl2br(esc_html($result->steps_to_reproduce)); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($result->recommendation): ?>
                                        <p><strong><?php _e('Recommendation:', 'journey-testing'); ?></strong><br>
                                        <?php echo nl2br(esc_html($result->recommendation)); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="column-result">
                            <?php if ($result): ?>
                                <span class="result-badge result-<?php echo $result->result; ?>">
                                    <?php echo ucfirst($result->result); ?>
                                </span>
                            <?php else: ?>
                                <span class="result-badge result-not-tested">
                                    <?php _e('Not Tested', 'journey-testing'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($result && $result->notes): ?>
                                <?php echo nl2br(esc_html($result->notes)); ?>
                            <?php else: ?>
                                <span class="no-notes">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="column-attachments">
                            <?php
                            if ($result) {
                                $attachments = $wpdb->get_results(
                                    $wpdb->prepare(
                                        "SELECT * FROM {$wpdb->prefix}journey_attachments 
                                         WHERE object_type = 'test_result' AND object_id = %d",
                                        $result->id
                                    )
                                );
                                
                                if ($attachments) {
                                    echo '<div class="attachment-icons">';
                                    foreach ($attachments as $attachment) {
                                        $icon = 'media-default';
                                        if (strpos($attachment->file_type, 'image') !== false) {
                                            $icon = 'format-image';
                                        } elseif (strpos($attachment->file_type, 'video') !== false) {
                                            $icon = 'format-video';
                                        }
                                        
                                        echo '<a href="' . esc_url($attachment->file_path) . '" target="_blank" title="' . esc_attr($attachment->file_name) . '">';
                                        echo '<span class="dashicons dashicons-' . $icon . '"></span>';
                                        echo '</a>';
                                    }
                                    echo '</div>';
                                } else {
                                    echo '<span class="no-attachments">-</span>';
                                }
                            } else {
                                echo '<span class="no-attachments">-</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p class="description">
            <span class="required-indicator">*</span> <?php _e('Required test step', 'journey-testing'); ?>
        </p>
    </div>
    
    <!-- Notes Section -->
    <?php if ($test_run['notes']): ?>
        <div class="report-notes">
            <h3><?php _e('Test Run Notes', 'journey-testing'); ?></h3>
            <div class="notes-content">
                <?php echo nl2br(esc_html($test_run['notes'])); ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Actions -->
    <div class="report-actions">
        <a href="<?php echo admin_url('admin.php?page=journey-testing-runs'); ?>" class="button">
            <?php _e('Back to Test Runs', 'journey-testing'); ?>
        </a>
        <?php if ($test_run['status'] === 'in_progress'): ?>
            <a href="<?php echo admin_url('admin.php?page=journey-testing-execute&run=' . $run_id); ?>" class="button button-primary">
                <?php _e('Continue Testing', 'journey-testing'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
/* Report Styles */
.journey-testing-report {
    max-width: 1200px;
}

.report-header {
    display: flex;
    gap: 30px;
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin: 20px 0;
    border-radius: 4px;
}

.report-info {
    flex: 1;
}

.report-info h2 {
    margin-top: 0;
}

.report-meta {
    margin-top: 15px;
}

.report-meta td {
    padding: 5px 15px 5px 0;
}

.overall-status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 3px;
    font-weight: 500;
}

.status-passed {
    background: #edfaef;
    color: #00a32a;
}

.status-failed {
    background: #fcf0f1;
    color: #d63638;
}

.status-blocked {
    background: #fcf9e8;
    color: #996800;
}

.report-summary {
    flex: 1;
    text-align: center;
}

.summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin: 20px 0;
}

.summary-item {
    text-align: center;
}

.summary-value {
    display: block;
    font-size: 36px;
    font-weight: bold;
    color: #1d2327;
}

.summary-label {
    display: block;
    font-size: 13px;
    color: #646970;
}

.results-breakdown {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 20px 0;
}

.result-item {
    text-align: center;
}

.result-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
}

.result-label {
    display: block;
    font-size: 12px;
}

.result-item.pass .result-value { color: #00a32a; }
.result-item.fail .result-value { color: #d63638; }
.result-item.blocked .result-value { color: #dba617; }
.result-item.skipped .result-value { color: #8c8f94; }

.issues-summary {
    margin-top: 20px;
}

.severity-breakdown {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}

.severity-item {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.severity-low { background: #f0f6fc; color: #0073aa; }
.severity-medium { background: #fcf9e8; color: #996800; }
.severity-high { background: #fcf4f0; color: #cc4125; }
.severity-critical { background: #fcf0f1; color: #d63638; }

.report-details {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin: 20px 0;
    border-radius: 4px;
}

.column-step-number {
    width: 50px;
}

.column-result {
    width: 100px;
}

.column-attachments {
    width: 100px;
}

.required-indicator {
    color: #d63638;
    font-weight: bold;
}

.result-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
}

.result-pass { background: #edfaef; color: #00a32a; }
.result-fail { background: #fcf0f1; color: #d63638; }
.result-blocked { background: #fcf9e8; color: #996800; }
.result-skipped { background: #f0f0f0; color: #666; }
.result-not-tested { background: #f0f0f0; color: #8c8f94; }

.test-step-row.result-fail {
    background-color: #fcf0f1;
}

.issue-details-inline {
    margin-top: 10px;
    padding: 10px;
    background: #fff;
    border: 1px solid #d63638;
    border-radius: 3px;
}

.issue-header {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 10px;
}

.issue-header .dashicons {
    color: #d63638;
}

.severity-badge {
    margin-left: auto;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
}

.issue-description {
    margin: 10px 0;
}

.attachment-icons {
    display: flex;
    gap: 5px;
}

.attachment-icons .dashicons {
    font-size: 20px;
    color: #2271b1;
}

.no-notes,
.no-attachments {
    color: #8c8f94;
}

.report-notes {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin: 20px 0;
    border-radius: 4px;
}

.notes-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 3px;
}

.report-actions {
    margin: 20px 0;
}

/* Print styles */
@media print {
    .wrap > h1 .page-title-action,
    .report-actions,
    #adminmenumain,
    #wpadminbar {
        display: none !important;
    }
    
    .report-header,
    .report-details {
        border: 1px solid #000;
    }
    
    .test-step-row {
        page-break-inside: avoid;
    }
}
</style>