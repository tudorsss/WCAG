<?php
/**
 * Test Execution view
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

// Check if user can access this test run
if ($test_run['tester_id'] != get_current_user_id() && !current_user_can('manage_options')) {
    wp_die(__('You do not have permission to access this test run.', 'journey-testing'));
}

// Get test steps
$test_steps = Journey_Testing_Test_Step::get_by_journey($test_run['journey_id']);

// Get existing results
global $wpdb;
$results_table = $wpdb->prefix . 'journey_test_results';
$existing_results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $results_table WHERE test_run_id = %d",
        $run_id
    ),
    OBJECT_K
);

// Get progress
$progress = Journey_Testing_Test_Run::get_progress($run_id);

// Handle completion
if (isset($_POST['complete_test_run']) && wp_verify_nonce($_POST['journey_testing_nonce'], 'complete_test_run')) {
    $completion_notes = sanitize_textarea_field($_POST['completion_notes']);
    Journey_Testing_Test_Run::complete($run_id, $completion_notes);
    
    wp_redirect(admin_url('admin.php?page=journey-testing-runs&completed=' . $run_id));
    exit;
}
?>

<div class="wrap journey-testing-execute">
    <h1>
        <?php _e('Test Execution', 'journey-testing'); ?>: 
        <?php echo esc_html($test_run['journey_name']); ?>
    </h1>
    
    <div class="test-run-header">
        <div class="test-run-info">
            <p>
                <strong><?php _e('Platform:', 'journey-testing'); ?></strong> 
                <span class="dashicons <?php echo esc_attr($test_run['platform_icon']); ?>"></span>
                <?php echo esc_html($test_run['platform_name']); ?>
            </p>
            <p>
                <strong><?php _e('Tester:', 'journey-testing'); ?></strong> 
                <?php echo esc_html($test_run['tester_name']); ?>
            </p>
            <p>
                <strong><?php _e('Started:', 'journey-testing'); ?></strong> 
                <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($test_run['started_at']))); ?>
            </p>
        </div>
        
        <div class="test-run-progress">
            <h3><?php _e('Progress', 'journey-testing'); ?></h3>
            <div class="progress-stats">
                <div class="progress-stat">
                    <span class="stat-value"><?php echo $progress['completed_steps']; ?>/<?php echo $progress['total_steps']; ?></span>
                    <span class="stat-label"><?php _e('Steps', 'journey-testing'); ?></span>
                </div>
                <div class="progress-stat">
                    <span class="stat-value"><?php echo $progress['progress_percentage']; ?>%</span>
                    <span class="stat-label"><?php _e('Complete', 'journey-testing'); ?></span>
                </div>
            </div>
            <div class="progress-bar-large">
                <div class="progress-fill" style="width: <?php echo $progress['progress_percentage']; ?>%"></div>
            </div>
            <div class="result-summary">
                <span class="result-pass"><?php echo $progress['results']['pass']; ?> <?php _e('Pass', 'journey-testing'); ?></span>
                <span class="result-fail"><?php echo $progress['results']['fail']; ?> <?php _e('Fail', 'journey-testing'); ?></span>
                <span class="result-blocked"><?php echo $progress['results']['blocked']; ?> <?php _e('Blocked', 'journey-testing'); ?></span>
                <span class="result-skipped"><?php echo $progress['results']['skipped']; ?> <?php _e('Skipped', 'journey-testing'); ?></span>
            </div>
        </div>
    </div>
    
    <div class="test-steps-container">
        <?php foreach ($test_steps as $index => $step): 
            $step_result = isset($existing_results[$step['id']]) ? $existing_results[$step['id']] : null;
            $result_status = $step_result ? $step_result->result : '';
        ?>
            <div class="test-step <?php echo $result_status ? 'has-result result-' . $result_status : ''; ?>" data-step-id="<?php echo $step['id']; ?>">
                <div class="step-header">
                    <div class="step-number"><?php echo $index + 1; ?></div>
                    <div class="step-title">
                        <h3><?php echo esc_html($step['title']); ?></h3>
                        <?php if ($step['is_required']): ?>
                            <span class="required-badge"><?php _e('Required', 'journey-testing'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="step-status">
                        <?php if ($result_status): ?>
                            <span class="status-indicator status-<?php echo esc_attr($result_status); ?>">
                                <?php echo ucfirst($result_status); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="step-content">
                    <div class="step-description">
                        <h4><?php _e('Description', 'journey-testing'); ?></h4>
                        <p><?php echo nl2br(esc_html($step['description'])); ?></p>
                    </div>
                    
                    <div class="step-expected">
                        <h4><?php _e('Expected Result', 'journey-testing'); ?></h4>
                        <p><?php echo nl2br(esc_html($step['expected_result'])); ?></p>
                    </div>
                    
                    <div class="step-testing">
                        <h4><?php _e('Test Result', 'journey-testing'); ?></h4>
                        <div class="result-buttons">
                            <button class="button result-button result-pass <?php echo $result_status === 'pass' ? 'active' : ''; ?>" 
                                    data-result="pass" data-step-id="<?php echo $step['id']; ?>">
                                <span class="dashicons dashicons-yes"></span> <?php _e('Pass', 'journey-testing'); ?>
                            </button>
                            <button class="button result-button result-fail <?php echo $result_status === 'fail' ? 'active' : ''; ?>" 
                                    data-result="fail" data-step-id="<?php echo $step['id']; ?>">
                                <span class="dashicons dashicons-no"></span> <?php _e('Fail', 'journey-testing'); ?>
                            </button>
                            <button class="button result-button result-blocked <?php echo $result_status === 'blocked' ? 'active' : ''; ?>" 
                                    data-result="blocked" data-step-id="<?php echo $step['id']; ?>">
                                <span class="dashicons dashicons-warning"></span> <?php _e('Blocked', 'journey-testing'); ?>
                            </button>
                            <button class="button result-button result-skipped <?php echo $result_status === 'skipped' ? 'active' : ''; ?>" 
                                    data-result="skipped" data-step-id="<?php echo $step['id']; ?>">
                                <span class="dashicons dashicons-controls-forward"></span> <?php _e('Skipped', 'journey-testing'); ?>
                            </button>
                        </div>
                        
                        <div class="step-notes">
                            <label><?php _e('Notes:', 'journey-testing'); ?></label>
                            <textarea class="step-notes-field" data-step-id="<?php echo $step['id']; ?>" 
                                      placeholder="<?php _e('Add any notes about this test step...', 'journey-testing'); ?>"><?php 
                                echo $step_result ? esc_textarea($step_result->notes) : ''; 
                            ?></textarea>
                        </div>
                        
                        <div class="issue-details" style="<?php echo $result_status === 'fail' ? 'display: block;' : 'display: none;'; ?>">
                            <h4><?php _e('Issue Details', 'journey-testing'); ?></h4>
                            <button class="button button-secondary open-issue-form" data-step-id="<?php echo $step['id']; ?>">
                                <span class="dashicons dashicons-edit"></span> <?php _e('Document Issue', 'journey-testing'); ?>
                            </button>
                            <?php 
                            // Check if issue exists
                            if ($step_result) {
                                $issue = $wpdb->get_row(
                                    $wpdb->prepare(
                                        "SELECT * FROM {$wpdb->prefix}journey_issues WHERE test_result_id = %d",
                                        $step_result->id
                                    )
                                );
                                if ($issue) {
                                    echo '<p class="issue-documented"><span class="dashicons dashicons-yes"></span> ' . __('Issue documented', 'journey-testing') . '</p>';
                                }
                            }
                            ?>
                        </div>
                        
                        <div class="step-attachments">
                            <h4><?php _e('Attachments', 'journey-testing'); ?></h4>
                            <div class="attachments-list" data-step-id="<?php echo $step['id']; ?>">
                                <?php
                                if ($step_result) {
                                    $attachments = $wpdb->get_results(
                                        $wpdb->prepare(
                                            "SELECT * FROM {$wpdb->prefix}journey_attachments 
                                             WHERE object_type = 'test_result' AND object_id = %d",
                                            $step_result->id
                                        )
                                    );
                                    foreach ($attachments as $attachment) {
                                        echo '<div class="attachment-item">';
                                        echo '<a href="' . esc_url($attachment->file_path) . '" target="_blank">';
                                        echo esc_html($attachment->file_name);
                                        echo '</a>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                            <button class="button upload-attachment" data-step-id="<?php echo $step['id']; ?>">
                                <span class="dashicons dashicons-upload"></span> <?php _e('Upload File', 'journey-testing'); ?>
                            </button>
                            <input type="file" class="attachment-input" data-step-id="<?php echo $step['id']; ?>" style="display: none;" />
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="test-completion">
        <form method="post">
            <?php wp_nonce_field('complete_test_run', 'journey_testing_nonce'); ?>
            <h3><?php _e('Complete Test Run', 'journey-testing'); ?></h3>
            <textarea name="completion_notes" rows="4" class="large-text" 
                      placeholder="<?php _e('Final notes about this test run...', 'journey-testing'); ?>"><?php 
                echo esc_textarea($test_run['notes']); 
            ?></textarea>
            <p>
                <button type="submit" name="complete_test_run" class="button button-primary button-large">
                    <?php _e('Complete Test Run', 'journey-testing'); ?>
                </button>
                <a href="<?php echo admin_url('admin.php?page=journey-testing-runs'); ?>" class="button button-large">
                    <?php _e('Save & Exit', 'journey-testing'); ?>
                </a>
            </p>
        </form>
    </div>
</div>

<!-- Issue Modal -->
<div id="issue-modal" class="journey-modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Document Issue', 'journey-testing'); ?></h2>
        <form id="issue-form">
            <input type="hidden" id="issue-step-id" />
            
            <p>
                <label><?php _e('Severity:', 'journey-testing'); ?></label>
                <select id="issue-severity" required>
                    <option value="low"><?php _e('Low', 'journey-testing'); ?></option>
                    <option value="medium"><?php _e('Medium', 'journey-testing'); ?></option>
                    <option value="high"><?php _e('High', 'journey-testing'); ?></option>
                    <option value="critical"><?php _e('Critical', 'journey-testing'); ?></option>
                </select>
            </p>
            
            <p>
                <label><?php _e('Description:', 'journey-testing'); ?></label>
                <textarea id="issue-description" rows="3" required></textarea>
            </p>
            
            <p>
                <label><?php _e('Steps to Reproduce:', 'journey-testing'); ?></label>
                <textarea id="issue-steps" rows="3"></textarea>
            </p>
            
            <p>
                <label><?php _e('Expected Behavior:', 'journey-testing'); ?></label>
                <textarea id="issue-expected" rows="2"></textarea>
            </p>
            
            <p>
                <label><?php _e('Actual Behavior:', 'journey-testing'); ?></label>
                <textarea id="issue-actual" rows="2"></textarea>
            </p>
            
            <p>
                <label><?php _e('Recommendation:', 'journey-testing'); ?></label>
                <textarea id="issue-recommendation" rows="2"></textarea>
            </p>
            
            <p>
                <button type="submit" class="button button-primary"><?php _e('Save Issue', 'journey-testing'); ?></button>
                <button type="button" class="button cancel-issue"><?php _e('Cancel', 'journey-testing'); ?></button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var testRunId = <?php echo $run_id; ?>;
    
    // Result button click
    $('.result-button').on('click', function() {
        var $button = $(this);
        var stepId = $button.data('step-id');
        var result = $button.data('result');
        var $step = $button.closest('.test-step');
        var notes = $step.find('.step-notes-field').val();
        
        // Update UI
        $step.find('.result-button').removeClass('active');
        $button.addClass('active');
        
        // Show/hide issue section
        if (result === 'fail') {
            $step.find('.issue-details').show();
        } else {
            $step.find('.issue-details').hide();
        }
        
        // Save result
        $.post(journey_testing_ajax.ajax_url, {
            action: 'journey_save_test_result',
            nonce: journey_testing_ajax.nonce,
            test_run_id: testRunId,
            test_step_id: stepId,
            result: result,
            notes: notes
        }, function(response) {
            if (response.success) {
                // Update progress
                updateProgress(response.data.progress);
                
                // Update step status
                $step.removeClass('result-pass result-fail result-blocked result-skipped')
                     .addClass('has-result result-' + result);
                
                var statusHtml = '<span class="status-indicator status-' + result + '">' + 
                               result.charAt(0).toUpperCase() + result.slice(1) + '</span>';
                $step.find('.step-status').html(statusHtml);
            }
        });
    });
    
    // Auto-save notes
    var noteTimers = {};
    $('.step-notes-field').on('input', function() {
        var $textarea = $(this);
        var stepId = $textarea.data('step-id');
        
        clearTimeout(noteTimers[stepId]);
        noteTimers[stepId] = setTimeout(function() {
            var $step = $textarea.closest('.test-step');
            var $activeButton = $step.find('.result-button.active');
            if ($activeButton.length) {
                $activeButton.trigger('click');
            }
        }, 1000);
    });
    
    // File upload
    $('.upload-attachment').on('click', function() {
        var stepId = $(this).data('step-id');
        $('.attachment-input[data-step-id="' + stepId + '"]').click();
    });
    
    $('.attachment-input').on('change', function() {
        var $input = $(this);
        var stepId = $input.data('step-id');
        var file = this.files[0];
        
        if (file) {
            var formData = new FormData();
            formData.append('action', 'journey_upload_attachment');
            formData.append('nonce', journey_testing_ajax.nonce);
            formData.append('file', file);
            formData.append('object_type', 'test_step');
            formData.append('object_id', stepId);
            
            $.ajax({
                url: journey_testing_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        var html = '<div class="attachment-item">' +
                                  '<a href="' + response.data.file_url + '" target="_blank">' +
                                  response.data.file_name + '</a></div>';
                        $('.attachments-list[data-step-id="' + stepId + '"]').append(html);
                    } else {
                        alert(response.data);
                    }
                }
            });
        }
    });
    
    // Issue modal
    $('.open-issue-form').on('click', function() {
        var stepId = $(this).data('step-id');
        $('#issue-step-id').val(stepId);
        $('#issue-modal').show();
    });
    
    $('.close, .cancel-issue').on('click', function() {
        $('#issue-modal').hide();
        $('#issue-form')[0].reset();
    });
    
    $('#issue-form').on('submit', function(e) {
        e.preventDefault();
        // Save issue logic here
        $('#issue-modal').hide();
        this.reset();
    });
    
    // Update progress display
    function updateProgress(progress) {
        $('.progress-fill').css('width', progress.progress_percentage + '%');
        $('.progress-stat:first .stat-value').text(progress.completed_steps + '/' + progress.total_steps);
        $('.progress-stat:last .stat-value').text(progress.progress_percentage + '%');
        $('.result-pass').text(progress.results.pass + ' Pass');
        $('.result-fail').text(progress.results.fail + ' Fail');
        $('.result-blocked').text(progress.results.blocked + ' Blocked');
        $('.result-skipped').text(progress.results.skipped + ' Skipped');
    }
});
</script>

<style>
.journey-testing-execute {
    max-width: 1200px;
}

.test-run-header {
    display: flex;
    justify-content: space-between;
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin: 20px 0;
    border-radius: 4px;
}

.test-run-progress {
    text-align: right;
}

.progress-stats {
    display: flex;
    gap: 20px;
    justify-content: flex-end;
    margin-bottom: 10px;
}

.progress-stat {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
}

.stat-label {
    display: block;
    font-size: 12px;
    color: #666;
}

.progress-bar-large {
    width: 300px;
    height: 30px;
    background: #f0f0f0;
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    height: 100%;
    background: #2271b1;
    transition: width 0.3s ease;
}

.result-summary {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    font-size: 13px;
}

.result-summary span {
    padding: 3px 8px;
    border-radius: 3px;
}

.result-pass { background: #edfaef; color: #00a32a; }
.result-fail { background: #fcf0f1; color: #d63638; }
.result-blocked { background: #fcf9e8; color: #996800; }
.result-skipped { background: #f0f0f0; color: #666; }

.test-step {
    background: #fff;
    border: 1px solid #ddd;
    margin-bottom: 20px;
    border-radius: 4px;
    overflow: hidden;
}

.test-step.has-result {
    border-left: 4px solid #ddd;
}

.test-step.result-pass { border-left-color: #00a32a; }
.test-step.result-fail { border-left-color: #d63638; }
.test-step.result-blocked { border-left-color: #dba617; }
.test-step.result-skipped { border-left-color: #8c8f94; }

.step-header {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
}

.step-number {
    width: 40px;
    height: 40px;
    background: #2271b1;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 15px;
}

.step-title {
    flex: 1;
}

.step-title h3 {
    margin: 0;
    font-size: 16px;
}

.required-badge {
    display: inline-block;
    background: #d63638;
    color: white;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 3px;
    margin-left: 10px;
}

.status-indicator {
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
}

.status-pass { background: #edfaef; color: #00a32a; }
.status-fail { background: #fcf0f1; color: #d63638; }
.status-blocked { background: #fcf9e8; color: #996800; }
.status-skipped { background: #f0f0f0; color: #666; }

.step-content {
    padding: 20px;
}

.step-description, .step-expected {
    margin-bottom: 20px;
}

.step-content h4 {
    margin: 0 0 10px;
    color: #1d2327;
    font-size: 14px;
}

.result-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.result-button {
    display: flex;
    align-items: center;
    gap: 5px;
}

.result-button.active {
    background: #2271b1;
    color: white;
}

.result-button.active .dashicons {
    color: white;
}

.step-notes-field {
    width: 100%;
    min-height: 60px;
}

.issue-details {
    margin-top: 15px;
    padding: 15px;
    background: #fcf0f1;
    border-radius: 4px;
}

.issue-documented {
    color: #00a32a;
    margin: 10px 0 0;
}

.step-attachments {
    margin-top: 20px;
}

.attachments-list {
    margin: 10px 0;
}

.attachment-item {
    display: inline-block;
    margin-right: 10px;
    margin-bottom: 5px;
}

.test-completion {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin-top: 30px;
    border-radius: 4px;
}

/* Modal styles */
.journey-modal {
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 600px;
    max-width: 90%;
    border-radius: 4px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #000;
}

#issue-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

#issue-form textarea,
#issue-form select {
    width: 100%;
}
</style>