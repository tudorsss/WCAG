<?php
/**
 * New Test Run view
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/admin/views
 */

// Check if user has an active test run
$active_run = Journey_Testing_Test_Run::get_active_for_user();

// Get platforms and journeys
$platforms = Journey_Testing_Platform::get_all();
$selected_platform = isset($_GET['platform']) ? intval($_GET['platform']) : 0;

// Get all users who can be testers
$users = get_users(array(
    'role__in' => array('administrator', 'editor', 'author'),
    'orderby' => 'display_name'
));

// Handle form submission
if (isset($_POST['start_test_run']) && wp_verify_nonce($_POST['journey_testing_nonce'], 'start_test_run')) {
    $journey_id = intval($_POST['journey_id']);
    $tester_id = intval($_POST['tester_id']);
    $notes = sanitize_textarea_field($_POST['notes']);
    
    // Create new test run
    $run_id = Journey_Testing_Test_Run::create(array(
        'journey_id' => $journey_id,
        'tester_id' => $tester_id,
        'notes' => $notes
    ));
    
    if ($run_id) {
        // Redirect to test execution page
        wp_redirect(admin_url('admin.php?page=journey-testing-execute&run=' . $run_id));
        exit;
    }
}
?>

<div class="wrap journey-testing-new-run">
    <h1><?php _e('Start New Test Run', 'journey-testing'); ?></h1>
    
    <?php if ($active_run): ?>
        <div class="notice notice-warning">
            <p>
                <?php _e('You have an active test run in progress.', 'journey-testing'); ?>
                <a href="<?php echo admin_url('admin.php?page=journey-testing-execute&run=' . $active_run['id']); ?>" class="button">
                    <?php _e('Continue Test Run', 'journey-testing'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>
    
    <form method="post" class="journey-test-form">
        <?php wp_nonce_field('start_test_run', 'journey_testing_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="platform_id"><?php _e('Platform', 'journey-testing'); ?></label>
                </th>
                <td>
                    <select name="platform_id" id="platform_id" class="regular-text" required>
                        <option value=""><?php _e('Select Platform', 'journey-testing'); ?></option>
                        <?php foreach ($platforms as $platform): ?>
                            <option value="<?php echo esc_attr($platform['id']); ?>" <?php selected($selected_platform, $platform['id']); ?>>
                                <?php echo esc_html($platform['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="journey_id"><?php _e('Journey', 'journey-testing'); ?></label>
                </th>
                <td>
                    <select name="journey_id" id="journey_id" class="regular-text" required disabled>
                        <option value=""><?php _e('Select Platform First', 'journey-testing'); ?></option>
                    </select>
                    <p class="description" id="journey-description"></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="tester_id"><?php _e('Tester', 'journey-testing'); ?></label>
                </th>
                <td>
                    <select name="tester_id" id="tester_id" class="regular-text" required>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo esc_attr($user->ID); ?>" <?php selected(get_current_user_id(), $user->ID); ?>>
                                <?php echo esc_html($user->display_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="notes"><?php _e('Notes', 'journey-testing'); ?></label>
                </th>
                <td>
                    <textarea name="notes" id="notes" rows="4" class="large-text" placeholder="<?php _e('Any notes about this test run...', 'journey-testing'); ?>"></textarea>
                </td>
            </tr>
        </table>
        
        <div class="journey-preview" id="journey-preview" style="display: none;">
            <h3><?php _e('Journey Preview', 'journey-testing'); ?></h3>
            <div class="journey-info">
                <p><strong><?php _e('Version:', 'journey-testing'); ?></strong> <span id="journey-version"></span></p>
                <p><strong><?php _e('Steps:', 'journey-testing'); ?></strong> <span id="journey-steps-count"></span></p>
            </div>
            <div class="journey-steps-preview" id="journey-steps-preview"></div>
        </div>
        
        <p class="submit">
            <button type="submit" name="start_test_run" class="button button-primary button-large" disabled>
                <?php _e('Start Test Run', 'journey-testing'); ?>
            </button>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    var journeys = <?php 
        // Get journeys data
        $admin = new Journey_Testing_Admin('journey-testing', JOURNEY_TESTING_VERSION);
        echo json_encode($admin->get_all_journeys_grouped()); 
    ?>;
    
    $('#platform_id').on('change', function() {
        var platformId = $(this).val();
        var $journeySelect = $('#journey_id');
        var $submitButton = $('button[name="start_test_run"]');
        
        $journeySelect.empty().prop('disabled', true);
        $('#journey-preview').hide();
        $submitButton.prop('disabled', true);
        
        if (platformId) {
            $journeySelect.append('<option value=""><?php _e('Select Journey', 'journey-testing'); ?></option>');
            
            if (journeys[platformId]) {
                $.each(journeys[platformId], function(i, journey) {
                    $journeySelect.append(
                        $('<option></option>')
                            .attr('value', journey.id)
                            .text(journey.name)
                            .data('journey', journey)
                    );
                });
                $journeySelect.prop('disabled', false);
            }
        } else {
            $journeySelect.append('<option value=""><?php _e('Select Platform First', 'journey-testing'); ?></option>');
        }
    });
    
    $('#journey_id').on('change', function() {
        var $selected = $(this).find('option:selected');
        var journey = $selected.data('journey');
        var $submitButton = $('button[name="start_test_run"]');
        
        if (journey) {
            $('#journey-description').text(journey.description);
            $('#journey-version').text(journey.version);
            $('#journey-steps-count').text(journey.steps.length);
            
            var stepsHtml = '<ol>';
            $.each(journey.steps, function(i, step) {
                stepsHtml += '<li>' + step.title + '</li>';
            });
            stepsHtml += '</ol>';
            
            $('#journey-steps-preview').html(stepsHtml);
            $('#journey-preview').show();
            $submitButton.prop('disabled', false);
        } else {
            $('#journey-description').text('');
            $('#journey-preview').hide();
            $submitButton.prop('disabled', true);
        }
    });
    
    // Trigger change if platform is pre-selected
    if ($('#platform_id').val()) {
        $('#platform_id').trigger('change');
    }
});
</script>

<style>
.journey-test-form {
    max-width: 800px;
}

.journey-preview {
    background: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.journey-preview h3 {
    margin-top: 0;
}

.journey-info {
    margin-bottom: 15px;
}

.journey-info p {
    margin: 5px 0;
}

.journey-steps-preview {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px 15px 15px 35px;
    max-height: 200px;
    overflow-y: auto;
}

.journey-steps-preview ol {
    margin: 0;
}

.journey-steps-preview li {
    margin-bottom: 5px;
}
</style>