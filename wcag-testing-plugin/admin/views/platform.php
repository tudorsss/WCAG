<?php
/**
 * Platform view
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/admin/views
 */

// Platform is already loaded in the admin class
// $platform variable is available

// Get journeys for this platform
$journeys = Journey_Testing_Journey::get_by_platform($platform['id']);

// Get recent test runs for this platform
$recent_runs = Journey_Testing_Test_Run::get_all(array(
    'platform_id' => $platform['id'],
    'limit' => 10
));

// Calculate platform statistics
$total_journeys = count($journeys);
$total_test_runs = 0;
$completed_runs = 0;
$total_pass_rate = 0;
$journey_stats = array();

foreach ($journeys as &$journey) {
    $stats = Journey_Testing_Journey::get_statistics($journey['id']);
    $journey['stats'] = $stats;
    $total_test_runs += $stats['total_runs'];
    $completed_runs += $stats['completed_runs'];
    if ($stats['pass_rate'] > 0) {
        $total_pass_rate += $stats['pass_rate'];
    }
}

$average_pass_rate = $total_journeys > 0 ? round($total_pass_rate / $total_journeys, 1) : 0;
?>

<div class="wrap journey-testing-platform">
    <h1>
        <span class="dashicons <?php echo esc_attr($platform['icon']); ?>"></span>
        <?php echo esc_html($platform['name']); ?> <?php _e('Testing', 'journey-testing'); ?>
        <a href="<?php echo admin_url('admin.php?page=journey-testing-new-run&platform=' . $platform['id']); ?>" class="page-title-action">
            <?php _e('Start New Test', 'journey-testing'); ?>
        </a>
    </h1>
    
    <?php if ($platform['description']): ?>
        <p class="description"><?php echo esc_html($platform['description']); ?></p>
    <?php endif; ?>
    
    <!-- Platform Statistics -->
    <div class="platform-stats-cards">
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-book"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo $total_journeys; ?></h3>
                <p><?php _e('User Journeys', 'journey-testing'); ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-clipboard"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo $total_test_runs; ?></h3>
                <p><?php _e('Total Test Runs', 'journey-testing'); ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo $completed_runs; ?></h3>
                <p><?php _e('Completed Runs', 'journey-testing'); ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-chart-area"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo $average_pass_rate; ?>%</h3>
                <p><?php _e('Average Pass Rate', 'journey-testing'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Journeys Overview -->
    <div class="journeys-section">
        <h2><?php _e('User Journeys', 'journey-testing'); ?></h2>
        
        <?php if (empty($journeys)): ?>
            <p><?php _e('No journeys defined for this platform yet.', 'journey-testing'); ?></p>
            <p>
                <a href="<?php echo admin_url('admin.php?page=journey-testing-manage&platform=' . $platform['id']); ?>" class="button button-primary">
                    <?php _e('Create First Journey', 'journey-testing'); ?>
                </a>
            </p>
        <?php else: ?>
            <div class="journey-cards">
                <?php foreach ($journeys as $journey): 
                    $steps_count = count(Journey_Testing_Test_Step::get_by_journey($journey['id']));
                ?>
                    <div class="journey-card">
                        <div class="journey-header">
                            <h3><?php echo esc_html($journey['name']); ?></h3>
                            <span class="version">v<?php echo esc_html($journey['version']); ?></span>
                        </div>
                        
                        <p class="journey-description"><?php echo esc_html($journey['description']); ?></p>
                        
                        <div class="journey-meta">
                            <div class="meta-item">
                                <span class="dashicons dashicons-editor-ol"></span>
                                <?php printf(_n('%d step', '%d steps', $steps_count, 'journey-testing'), $steps_count); ?>
                            </div>
                            <div class="meta-item">
                                <span class="dashicons dashicons-clipboard"></span>
                                <?php printf(_n('%d run', '%d runs', $journey['stats']['total_runs'], 'journey-testing'), $journey['stats']['total_runs']); ?>
                            </div>
                        </div>
                        
                        <?php if ($journey['stats']['latest_run']): ?>
                            <div class="journey-status">
                                <div class="status-bar">
                                    <div class="status-fill" style="width: <?php echo $journey['stats']['pass_rate']; ?>%"></div>
                                </div>
                                <span class="status-text"><?php echo $journey['stats']['pass_rate']; ?>% <?php _e('Pass Rate', 'journey-testing'); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="journey-actions">
                            <a href="<?php echo admin_url('admin.php?page=journey-testing-new-run&platform=' . $platform['id'] . '&journey=' . $journey['id']); ?>" 
                               class="button button-primary">
                                <?php _e('Start Test', 'journey-testing'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=journey-testing-manage&edit=' . $journey['id']); ?>" 
                               class="button">
                                <?php _e('Edit', 'journey-testing'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <p class="manage-link">
                <a href="<?php echo admin_url('admin.php?page=journey-testing-manage&platform=' . $platform['id']); ?>" class="button">
                    <?php _e('Manage All Journeys', 'journey-testing'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
    
    <!-- Recent Test Runs -->
    <div class="recent-runs-section">
        <h2><?php _e('Recent Test Runs', 'journey-testing'); ?></h2>
        
        <?php if (empty($recent_runs)): ?>
            <p><?php _e('No test runs yet for this platform.', 'journey-testing'); ?></p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Journey', 'journey-testing'); ?></th>
                        <th><?php _e('Tester', 'journey-testing'); ?></th>
                        <th><?php _e('Started', 'journey-testing'); ?></th>
                        <th><?php _e('Status', 'journey-testing'); ?></th>
                        <th><?php _e('Progress', 'journey-testing'); ?></th>
                        <th><?php _e('Actions', 'journey-testing'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_runs as $run): 
                        $progress = Journey_Testing_Test_Run::get_progress($run['id']);
                    ?>
                        <tr>
                            <td><?php echo esc_html($run['journey_name']); ?></td>
                            <td><?php echo esc_html($run['tester_name']); ?></td>
                            <td><?php echo human_time_diff(strtotime($run['started_at']), current_time('timestamp')) . ' ' . __('ago', 'journey-testing'); ?></td>
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
                            <td>
                                <?php if ($run['status'] === 'in_progress'): ?>
                                    <a href="<?php echo admin_url('admin.php?page=journey-testing-execute&run=' . $run['id']); ?>" class="button button-small">
                                        <?php _e('Continue', 'journey-testing'); ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo admin_url('admin.php?page=journey-testing-report&run=' . $run['id']); ?>" class="button button-small">
                                        <?php _e('View Report', 'journey-testing'); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <p>
                <a href="<?php echo admin_url('admin.php?page=journey-testing-runs&platform=' . $platform['id']); ?>" class="button">
                    <?php _e('View All Test Runs', 'journey-testing'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</div>

<style>
.journey-testing-platform {
    max-width: 1200px;
}

.platform-stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    font-size: 40px;
    color: #2271b1;
}

.stat-content h3 {
    margin: 0;
    font-size: 28px;
    color: #1d2327;
}

.stat-content p {
    margin: 5px 0 0;
    color: #646970;
}

.journeys-section,
.recent-runs-section {
    margin: 30px 0;
}

.journey-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.journey-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    flex-direction: column;
}

.journey-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.journey-header h3 {
    margin: 0;
    font-size: 18px;
}

.version {
    background: #f0f0f0;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 12px;
    color: #646970;
}

.journey-description {
    color: #646970;
    margin: 10px 0;
    flex-grow: 1;
}

.journey-meta {
    display: flex;
    gap: 20px;
    margin: 15px 0;
    padding: 15px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: #646970;
}

.journey-status {
    margin: 15px 0;
}

.status-bar {
    background: #f0f0f0;
    height: 20px;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 5px;
}

.status-fill {
    background: #00a32a;
    height: 100%;
    transition: width 0.3s ease;
}

.status-text {
    font-size: 12px;
    color: #646970;
}

.journey-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.manage-link {
    margin-top: 20px;
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
</style>