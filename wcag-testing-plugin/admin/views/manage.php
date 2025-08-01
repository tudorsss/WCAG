<?php
/**
 * Manage Journeys view
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/admin/views
 */

// Get platforms
$platforms = Journey_Testing_Platform::get_all();

// Handle journey actions
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'create_journey' && wp_verify_nonce($_POST['journey_nonce'], 'manage_journey')) {
        $journey_id = Journey_Testing_Journey::create(array(
            'platform_id' => intval($_POST['platform_id']),
            'name' => sanitize_text_field($_POST['name']),
            'slug' => sanitize_title($_POST['slug']),
            'description' => sanitize_textarea_field($_POST['description']),
            'version' => sanitize_text_field($_POST['version']),
            'display_order' => intval($_POST['display_order'])
        ));
        
        if ($journey_id) {
            echo '<div class="notice notice-success"><p>' . __('Journey created successfully.', 'journey-testing') . '</p></div>';
        }
    } elseif ($_POST['action'] === 'update_journey' && wp_verify_nonce($_POST['journey_nonce'], 'manage_journey')) {
        Journey_Testing_Journey::update(intval($_POST['journey_id']), array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
            'version' => sanitize_text_field($_POST['version']),
            'display_order' => intval($_POST['display_order']),
            'status' => sanitize_text_field($_POST['status'])
        ));
        
        echo '<div class="notice notice-success"><p>' . __('Journey updated successfully.', 'journey-testing') . '</p></div>';
    }
}

// Handle test step actions via AJAX (defined below)

// Get selected platform
$selected_platform = isset($_GET['platform']) ? intval($_GET['platform']) : 0;
$edit_journey = isset($_GET['edit']) ? intval($_GET['edit']) : 0;

// Get journey details if editing
$journey = null;
$test_steps = array();
if ($edit_journey) {
    $journey = Journey_Testing_Journey::get($edit_journey);
    if ($journey) {
        $test_steps = Journey_Testing_Test_Step::get_by_journey($edit_journey);
        $selected_platform = $journey['platform_id'];
    }
}

// Get journeys for selected platform
$journeys = array();
if ($selected_platform && !$edit_journey) {
    $journeys = Journey_Testing_Journey::get_by_platform($selected_platform);
}
?>

<div class="wrap journey-testing-manage">
    <h1>
        <?php _e('Manage Journeys', 'journey-testing'); ?>
        <?php if (!$edit_journey): ?>
            <a href="#" class="page-title-action" id="add-new-journey">
                <?php _e('Add New Journey', 'journey-testing'); ?>
            </a>
        <?php endif; ?>
    </h1>
    
    <?php if (!$edit_journey): ?>
        <!-- Platform Selection -->
        <div class="platform-selector">
            <h2><?php _e('Select Platform', 'journey-testing'); ?></h2>
            <div class="platform-buttons">
                <?php foreach ($platforms as $platform): ?>
                    <a href="<?php echo add_query_arg('platform', $platform['id']); ?>" 
                       class="platform-button <?php echo $selected_platform == $platform['id'] ? 'active' : ''; ?>">
                        <span class="dashicons <?php echo esc_attr($platform['icon']); ?>"></span>
                        <span><?php echo esc_html($platform['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if ($selected_platform): ?>
            <!-- Journeys List -->
            <div class="journeys-list">
                <h2><?php _e('Journeys', 'journey-testing'); ?></h2>
                
                <?php if (empty($journeys)): ?>
                    <p><?php _e('No journeys found for this platform.', 'journey-testing'); ?></p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th class="column-order"><?php _e('Order', 'journey-testing'); ?></th>
                                <th><?php _e('Journey Name', 'journey-testing'); ?></th>
                                <th><?php _e('Description', 'journey-testing'); ?></th>
                                <th><?php _e('Version', 'journey-testing'); ?></th>
                                <th><?php _e('Steps', 'journey-testing'); ?></th>
                                <th><?php _e('Status', 'journey-testing'); ?></th>
                                <th><?php _e('Actions', 'journey-testing'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($journeys as $journey_item): 
                                $steps_count = count(Journey_Testing_Test_Step::get_by_journey($journey_item['id']));
                            ?>
                                <tr>
                                    <td class="column-order"><?php echo $journey_item['display_order']; ?></td>
                                    <td>
                                        <strong><?php echo esc_html($journey_item['name']); ?></strong>
                                    </td>
                                    <td><?php echo esc_html($journey_item['description']); ?></td>
                                    <td><?php echo esc_html($journey_item['version']); ?></td>
                                    <td><?php echo $steps_count; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $journey_item['status']; ?>">
                                            <?php echo ucfirst($journey_item['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo add_query_arg('edit', $journey_item['id']); ?>" class="button button-small">
                                            <?php _e('Edit', 'journey-testing'); ?>
                                        </a>
                                        <button class="button button-small duplicate-journey" data-journey-id="<?php echo $journey_item['id']; ?>">
                                            <?php _e('Duplicate', 'journey-testing'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- New Journey Form (Hidden by default) -->
        <div id="new-journey-form" style="display: none;">
            <h2><?php _e('Create New Journey', 'journey-testing'); ?></h2>
            <form method="post">
                <?php wp_nonce_field('manage_journey', 'journey_nonce'); ?>
                <input type="hidden" name="action" value="create_journey" />
                
                <table class="form-table">
                    <tr>
                        <th><label for="platform_id"><?php _e('Platform', 'journey-testing'); ?></label></th>
                        <td>
                            <select name="platform_id" id="platform_id" required>
                                <option value=""><?php _e('Select Platform', 'journey-testing'); ?></option>
                                <?php foreach ($platforms as $platform): ?>
                                    <option value="<?php echo $platform['id']; ?>" <?php selected($selected_platform, $platform['id']); ?>>
                                        <?php echo esc_html($platform['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="name"><?php _e('Journey Name', 'journey-testing'); ?></label></th>
                        <td><input type="text" name="name" id="name" class="regular-text" required /></td>
                    </tr>
                    <tr>
                        <th><label for="slug"><?php _e('Slug', 'journey-testing'); ?></label></th>
                        <td><input type="text" name="slug" id="slug" class="regular-text" required /></td>
                    </tr>
                    <tr>
                        <th><label for="description"><?php _e('Description', 'journey-testing'); ?></label></th>
                        <td><textarea name="description" id="description" rows="3" class="large-text"></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="version"><?php _e('Version', 'journey-testing'); ?></label></th>
                        <td><input type="text" name="version" id="version" class="small-text" value="1.0.0" required /></td>
                    </tr>
                    <tr>
                        <th><label for="display_order"><?php _e('Display Order', 'journey-testing'); ?></label></th>
                        <td><input type="number" name="display_order" id="display_order" class="small-text" value="0" /></td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Create Journey', 'journey-testing'); ?></button>
                    <button type="button" class="button" id="cancel-new-journey"><?php _e('Cancel', 'journey-testing'); ?></button>
                </p>
            </form>
        </div>
        
    <?php else: ?>
        <!-- Edit Journey -->
        <div class="edit-journey">
            <h2>
                <?php _e('Edit Journey:', 'journey-testing'); ?> <?php echo esc_html($journey['name']); ?>
                <a href="<?php echo remove_query_arg('edit'); ?>" class="page-title-action">
                    <?php _e('Back to List', 'journey-testing'); ?>
                </a>
            </h2>
            
            <!-- Journey Details Form -->
            <div class="journey-details">
                <form method="post">
                    <?php wp_nonce_field('manage_journey', 'journey_nonce'); ?>
                    <input type="hidden" name="action" value="update_journey" />
                    <input type="hidden" name="journey_id" value="<?php echo $journey['id']; ?>" />
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="name"><?php _e('Journey Name', 'journey-testing'); ?></label></th>
                            <td><input type="text" name="name" id="name" class="regular-text" value="<?php echo esc_attr($journey['name']); ?>" required /></td>
                        </tr>
                        <tr>
                            <th><label for="description"><?php _e('Description', 'journey-testing'); ?></label></th>
                            <td><textarea name="description" id="description" rows="3" class="large-text"><?php echo esc_textarea($journey['description']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th><label for="version"><?php _e('Version', 'journey-testing'); ?></label></th>
                            <td><input type="text" name="version" id="version" class="small-text" value="<?php echo esc_attr($journey['version']); ?>" required /></td>
                        </tr>
                        <tr>
                            <th><label for="display_order"><?php _e('Display Order', 'journey-testing'); ?></label></th>
                            <td><input type="number" name="display_order" id="display_order" class="small-text" value="<?php echo esc_attr($journey['display_order']); ?>" /></td>
                        </tr>
                        <tr>
                            <th><label for="status"><?php _e('Status', 'journey-testing'); ?></label></th>
                            <td>
                                <select name="status" id="status">
                                    <option value="active" <?php selected($journey['status'], 'active'); ?>><?php _e('Active', 'journey-testing'); ?></option>
                                    <option value="inactive" <?php selected($journey['status'], 'inactive'); ?>><?php _e('Inactive', 'journey-testing'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <button type="submit" class="button button-primary"><?php _e('Update Journey', 'journey-testing'); ?></button>
                    </p>
                </form>
            </div>
            
            <!-- Test Steps Management -->
            <div class="test-steps-management">
                <h3>
                    <?php _e('Test Steps', 'journey-testing'); ?>
                    <button class="button add-test-step" data-journey-id="<?php echo $journey['id']; ?>">
                        <?php _e('Add Test Step', 'journey-testing'); ?>
                    </button>
                </h3>
                
                <div id="test-steps-list">
                    <?php if (empty($test_steps)): ?>
                        <p><?php _e('No test steps defined yet.', 'journey-testing'); ?></p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed striped" id="steps-table">
                            <thead>
                                <tr>
                                    <th class="column-order"><?php _e('Order', 'journey-testing'); ?></th>
                                    <th><?php _e('Step Title', 'journey-testing'); ?></th>
                                    <th><?php _e('Description', 'journey-testing'); ?></th>
                                    <th><?php _e('Expected Result', 'journey-testing'); ?></th>
                                    <th><?php _e('Required', 'journey-testing'); ?></th>
                                    <th><?php _e('Actions', 'journey-testing'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="steps-tbody">
                                <?php foreach ($test_steps as $step): ?>
                                    <tr data-step-id="<?php echo $step['id']; ?>">
                                        <td class="column-order">
                                            <span class="step-order"><?php echo $step['step_order'] + 1; ?></span>
                                            <div class="row-actions">
                                                <span class="move-up"><a href="#" data-step-id="<?php echo $step['id']; ?>">↑</a></span> |
                                                <span class="move-down"><a href="#" data-step-id="<?php echo $step['id']; ?>">↓</a></span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo esc_html($step['title']); ?></strong>
                                        </td>
                                        <td><?php echo esc_html($step['description']); ?></td>
                                        <td><?php echo esc_html($step['expected_result']); ?></td>
                                        <td>
                                            <?php echo $step['is_required'] ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no-alt"></span>'; ?>
                                        </td>
                                        <td>
                                            <button class="button button-small edit-step" data-step='<?php echo json_encode($step); ?>'>
                                                <?php _e('Edit', 'journey-testing'); ?>
                                            </button>
                                            <button class="button button-small delete-step" data-step-id="<?php echo $step['id']; ?>">
                                                <?php _e('Delete', 'journey-testing'); ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Test Step Modal -->
<div id="step-modal" class="journey-modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="step-modal-title"><?php _e('Add Test Step', 'journey-testing'); ?></h2>
        <form id="step-form">
            <input type="hidden" id="step-id" value="" />
            <input type="hidden" id="step-journey-id" value="" />
            
            <p>
                <label for="step-title"><?php _e('Step Title:', 'journey-testing'); ?></label>
                <input type="text" id="step-title" class="widefat" required />
            </p>
            
            <p>
                <label for="step-description"><?php _e('Description:', 'journey-testing'); ?></label>
                <textarea id="step-description" rows="3" class="widefat"></textarea>
            </p>
            
            <p>
                <label for="step-expected"><?php _e('Expected Result:', 'journey-testing'); ?></label>
                <textarea id="step-expected" rows="3" class="widefat"></textarea>
            </p>
            
            <p>
                <label>
                    <input type="checkbox" id="step-required" checked />
                    <?php _e('This is a required step', 'journey-testing'); ?>
                </label>
            </p>
            
            <p>
                <button type="submit" class="button button-primary"><?php _e('Save Step', 'journey-testing'); ?></button>
                <button type="button" class="button cancel-step"><?php _e('Cancel', 'journey-testing'); ?></button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Show/hide new journey form
    $('#add-new-journey').on('click', function(e) {
        e.preventDefault();
        $('#new-journey-form').slideDown();
        $(this).hide();
    });
    
    $('#cancel-new-journey').on('click', function() {
        $('#new-journey-form').slideUp();
        $('#add-new-journey').show();
    });
    
    // Auto-generate slug
    $('#name').on('input', function() {
        var slug = $(this).val().toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        $('#slug').val(slug);
    });
    
    // Test step modal
    $('.add-test-step, .edit-step').on('click', function() {
        var isEdit = $(this).hasClass('edit-step');
        
        if (isEdit) {
            var step = $(this).data('step');
            $('#step-modal-title').text('<?php _e('Edit Test Step', 'journey-testing'); ?>');
            $('#step-id').val(step.id);
            $('#step-journey-id').val(step.journey_id);
            $('#step-title').val(step.title);
            $('#step-description').val(step.description);
            $('#step-expected').val(step.expected_result);
            $('#step-required').prop('checked', step.is_required == 1);
        } else {
            $('#step-modal-title').text('<?php _e('Add Test Step', 'journey-testing'); ?>');
            $('#step-form')[0].reset();
            $('#step-id').val('');
            $('#step-journey-id').val($(this).data('journey-id'));
        }
        
        $('#step-modal').show();
    });
    
    $('.close, .cancel-step').on('click', function() {
        $('#step-modal').hide();
    });
    
    // Save test step
    $('#step-form').on('submit', function(e) {
        e.preventDefault();
        
        var data = {
            action: 'journey_save_test_step',
            nonce: '<?php echo wp_create_nonce('journey_testing_ajax'); ?>',
            step_id: $('#step-id').val(),
            journey_id: $('#step-journey-id').val(),
            title: $('#step-title').val(),
            description: $('#step-description').val(),
            expected_result: $('#step-expected').val(),
            is_required: $('#step-required').is(':checked') ? 1 : 0
        };
        
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data || 'Error saving step');
            }
        });
    });
    
    // Delete step
    $(document).on('click', '.delete-step', function() {
        if (!confirm('<?php _e('Are you sure you want to delete this step?', 'journey-testing'); ?>')) {
            return;
        }
        
        var stepId = $(this).data('step-id');
        
        $.post(ajaxurl, {
            action: 'journey_delete_test_step',
            nonce: '<?php echo wp_create_nonce('journey_testing_ajax'); ?>',
            step_id: stepId
        }, function(response) {
            if (response.success) {
                location.reload();
            }
        });
    });
    
    // Reorder steps
    $(document).on('click', '.move-up a, .move-down a', function(e) {
        e.preventDefault();
        var $row = $(this).closest('tr');
        var stepId = $(this).data('step-id');
        var isUp = $(this).parent().hasClass('move-up');
        
        if (isUp && $row.prev().length) {
            $row.insertBefore($row.prev());
        } else if (!isUp && $row.next().length) {
            $row.insertAfter($row.next());
        }
        
        // Update order
        var stepIds = [];
        $('#steps-tbody tr').each(function() {
            stepIds.push($(this).data('step-id'));
        });
        
        $.post(ajaxurl, {
            action: 'journey_reorder_steps',
            nonce: '<?php echo wp_create_nonce('journey_testing_ajax'); ?>',
            journey_id: <?php echo $edit_journey ? $edit_journey : 0; ?>,
            step_ids: stepIds
        });
    });
    
    // Duplicate journey
    $('.duplicate-journey').on('click', function() {
        var journeyId = $(this).data('journey-id');
        var newName = prompt('<?php _e('Enter name for the duplicated journey:', 'journey-testing'); ?>');
        
        if (newName) {
            $.post(ajaxurl, {
                action: 'journey_duplicate',
                nonce: '<?php echo wp_create_nonce('journey_testing_ajax'); ?>',
                journey_id: journeyId,
                new_name: newName
            }, function(response) {
                if (response.success) {
                    location.reload();
                }
            });
        }
    });
});
</script>

<style>
.journey-testing-manage {
    max-width: 1200px;
}

.platform-selector {
    margin: 20px 0;
}

.platform-buttons {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.platform-button {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 25px;
    background: #fff;
    border: 2px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #1d2327;
    transition: all 0.2s;
}

.platform-button:hover {
    border-color: #2271b1;
    color: #2271b1;
}

.platform-button.active {
    background: #2271b1;
    border-color: #2271b1;
    color: #fff;
}

.platform-button .dashicons {
    font-size: 24px;
}

.journeys-list {
    margin-top: 30px;
}

.column-order {
    width: 80px;
}

.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.status-active {
    background: #edfaef;
    color: #00a32a;
}

.status-inactive {
    background: #f0f0f0;
    color: #646970;
}

#new-journey-form {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin: 20px 0;
    border-radius: 4px;
}

.edit-journey {
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.journey-details {
    margin-bottom: 30px;
}

.test-steps-management {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid #ddd;
}

.test-steps-management h3 {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.step-order {
    font-weight: bold;
}

.row-actions {
    font-size: 12px;
    margin-top: 5px;
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
</style>