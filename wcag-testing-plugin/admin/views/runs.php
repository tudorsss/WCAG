<?php
/**
 * Test Runs view
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/admin/views
 */

// Get filter parameters
$filter_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$filter_platform = isset($_GET['platform']) ? intval($_GET['platform']) : 0;
$filter_tester = isset($_GET['tester']) ? intval($_GET['tester']) : 0;

// Pagination
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get test runs
$args = array(
    'limit' => $per_page,
    'offset' => $offset,
    'status' => $filter_status,
    'platform_id' => $filter_platform,
    'tester_id' => $filter_tester
);

$test_runs = Journey_Testing_Test_Run::get_all($args);

// Get total count for pagination
global $wpdb;
$where_clauses = array('1=1');
if ($filter_status) {
    $where_clauses[] = $wpdb->prepare('r.status = %s', $filter_status);
}
if ($filter_tester) {
    $where_clauses[] = $wpdb->prepare('r.tester_id = %d', $filter_tester);
}
if ($filter_platform) {
    $where_clauses[] = $wpdb->prepare('j.platform_id = %d', $filter_platform);
}
$where_sql = implode(' AND ', $where_clauses);

$total_items = $wpdb->get_var(
    "SELECT COUNT(*) 
     FROM {$wpdb->prefix}journey_test_runs r
     LEFT JOIN {$wpdb->prefix}journey_definitions j ON r.journey_id = j.id
     WHERE $where_sql"
);

$total_pages = ceil($total_items / $per_page);

// Get platforms for filter
$platforms = Journey_Testing_Platform::get_all();

// Get testers for filter
$testers = get_users(array(
    'role__in' => array('administrator', 'editor', 'author'),
    'orderby' => 'display_name'
));

// Check for completion message
if (isset($_GET['completed'])) {
    $completed_id = intval($_GET['completed']);
    echo '<div class="notice notice-success"><p>' . sprintf(__('Test run #%d has been completed successfully.', 'journey-testing'), $completed_id) . '</p></div>';
}
?>

<div class="wrap journey-testing-runs">
    <h1>
        <?php _e('Test Runs', 'journey-testing'); ?>
        <a href="<?php echo admin_url('admin.php?page=journey-testing-new-run'); ?>" class="page-title-action">
            <?php _e('New Test Run', 'journey-testing'); ?>
        </a>
    </h1>
    
    <!-- Filters -->
    <div class="tablenav top">
        <form method="get" class="filter-form">
            <input type="hidden" name="page" value="journey-testing-runs" />
            
            <select name="status">
                <option value=""><?php _e('All Statuses', 'journey-testing'); ?></option>
                <option value="in_progress" <?php selected($filter_status, 'in_progress'); ?>><?php _e('In Progress', 'journey-testing'); ?></option>
                <option value="completed" <?php selected($filter_status, 'completed'); ?>><?php _e('Completed', 'journey-testing'); ?></option>
            </select>
            
            <select name="platform">
                <option value=""><?php _e('All Platforms', 'journey-testing'); ?></option>
                <?php foreach ($platforms as $platform): ?>
                    <option value="<?php echo esc_attr($platform['id']); ?>" <?php selected($filter_platform, $platform['id']); ?>>
                        <?php echo esc_html($platform['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="tester">
                <option value=""><?php _e('All Testers', 'journey-testing'); ?></option>
                <?php foreach ($testers as $tester): ?>
                    <option value="<?php echo esc_attr($tester->ID); ?>" <?php selected($filter_tester, $tester->ID); ?>>
                        <?php echo esc_html($tester->display_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="submit" class="button" value="<?php _e('Filter', 'journey-testing'); ?>" />
            
            <?php if ($filter_status || $filter_platform || $filter_tester): ?>
                <a href="<?php echo admin_url('admin.php?page=journey-testing-runs'); ?>" class="button">
                    <?php _e('Clear Filters', 'journey-testing'); ?>
                </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Test Runs Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th class="column-id"><?php _e('ID', 'journey-testing'); ?></th>
                <th><?php _e('Journey', 'journey-testing'); ?></th>
                <th><?php _e('Platform', 'journey-testing'); ?></th>
                <th><?php _e('Tester', 'journey-testing'); ?></th>
                <th><?php _e('Started', 'journey-testing'); ?></th>
                <th><?php _e('Completed', 'journey-testing'); ?></th>
                <th><?php _e('Status', 'journey-testing'); ?></th>
                <th><?php _e('Progress', 'journey-testing'); ?></th>
                <th><?php _e('Results', 'journey-testing'); ?></th>
                <th><?php _e('Actions', 'journey-testing'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($test_runs)): ?>
                <tr>
                    <td colspan="10" class="no-items"><?php _e('No test runs found.', 'journey-testing'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($test_runs as $run): 
                    $progress = Journey_Testing_Test_Run::get_progress($run['id']);
                ?>
                    <tr>
                        <td class="column-id">#<?php echo $run['id']; ?></td>
                        <td>
                            <strong><?php echo esc_html($run['journey_name']); ?></strong>
                        </td>
                        <td>
                            <span class="dashicons <?php echo esc_attr($run['platform_icon']); ?>"></span>
                            <?php echo esc_html($run['platform_name']); ?>
                        </td>
                        <td><?php echo esc_html($run['tester_name']); ?></td>
                        <td><?php echo date_i18n(get_option('date_format'), strtotime($run['started_at'])); ?></td>
                        <td>
                            <?php if ($run['completed_at']): ?>
                                <?php echo date_i18n(get_option('date_format'), strtotime($run['completed_at'])); ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo esc_attr($run['status']); ?>">
                                <?php echo ucfirst($run['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $progress['progress_percentage']; ?>%"></div>
                                <span class="progress-text"><?php echo $progress['progress_percentage']; ?>%</span>
                            </div>
                        </td>
                        <td class="results-column">
                            <?php if ($progress['completed_steps'] > 0): ?>
                                <span class="result-count pass"><?php echo $progress['results']['pass']; ?></span>
                                <span class="result-count fail"><?php echo $progress['results']['fail']; ?></span>
                                <span class="result-count blocked"><?php echo $progress['results']['blocked']; ?></span>
                                <span class="result-count skipped"><?php echo $progress['results']['skipped']; ?></span>
                            <?php else: ?>
                                <span class="no-results">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($run['status'] === 'in_progress'): ?>
                                <a href="<?php echo admin_url('admin.php?page=journey-testing-execute&run=' . $run['id']); ?>" 
                                   class="button button-small button-primary">
                                    <?php _e('Continue', 'journey-testing'); ?>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo admin_url('admin.php?page=journey-testing-report&run=' . $run['id']); ?>" 
                                   class="button button-small">
                                    <?php _e('View Report', 'journey-testing'); ?>
                                </a>
                            <?php endif; ?>
                            
                            <button class="button button-small view-notes" data-notes="<?php echo esc_attr($run['notes']); ?>">
                                <?php _e('Notes', 'journey-testing'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <span class="displaying-num">
                    <?php printf(_n('%s item', '%s items', $total_items, 'journey-testing'), number_format_i18n($total_items)); ?>
                </span>
                
                <?php
                $pagination_args = array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'total' => $total_pages,
                    'current' => $page,
                    'show_all' => false,
                    'end_size' => 1,
                    'mid_size' => 2,
                    'prev_next' => true,
                    'prev_text' => __('&laquo; Previous', 'journey-testing'),
                    'next_text' => __('Next &raquo;', 'journey-testing'),
                    'type' => 'plain',
                );
                
                echo paginate_links($pagination_args);
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Notes Modal -->
<div id="notes-modal" class="journey-modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Test Run Notes', 'journey-testing'); ?></h2>
        <div id="notes-content"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // View notes
    $('.view-notes').on('click', function() {
        var notes = $(this).data('notes');
        $('#notes-content').html(notes ? '<p>' + notes + '</p>' : '<p><?php _e('No notes for this test run.', 'journey-testing'); ?></p>');
        $('#notes-modal').show();
    });
    
    $('.close').on('click', function() {
        $('#notes-modal').hide();
    });
    
    $(window).on('click', function(e) {
        if ($(e.target).is('#notes-modal')) {
            $('#notes-modal').hide();
        }
    });
});
</script>

<style>
.journey-testing-runs .column-id {
    width: 50px;
}

.filter-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
}

.status-in_progress {
    background: #f0f6fc;
    color: #0073aa;
}

.status-completed {
    background: #edfaef;
    color: #00a32a;
}

.progress-bar {
    background: #f0f0f0;
    border-radius: 10px;
    height: 20px;
    position: relative;
    overflow: hidden;
    width: 100px;
}

.progress-fill {
    background: #2271b1;
    height: 100%;
    transition: width 0.3s ease;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 11px;
    font-weight: 500;
    color: #1d2327;
}

.results-column {
    white-space: nowrap;
}

.result-count {
    display: inline-block;
    min-width: 20px;
    padding: 2px 6px;
    margin-right: 4px;
    border-radius: 3px;
    font-size: 11px;
    text-align: center;
}

.result-count.pass {
    background: #edfaef;
    color: #00a32a;
}

.result-count.fail {
    background: #fcf0f1;
    color: #d63638;
}

.result-count.blocked {
    background: #fcf9e8;
    color: #996800;
}

.result-count.skipped {
    background: #f0f0f0;
    color: #666;
}

.no-results {
    color: #8c8f94;
}

.no-items {
    text-align: center;
    color: #8c8f94;
    padding: 40px !important;
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
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 500px;
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