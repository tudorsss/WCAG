<?php
/**
 * Dashboard view
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/admin/views
 */

// Get statistics
$platforms = Journey_Testing_Platform::get_all();
$recent_runs = Journey_Testing_Test_Run::get_all(array('limit' => 5));

// Calculate overall statistics
$total_runs = 0;
$completed_runs = 0;
$in_progress_runs = 0;

foreach ($platforms as &$platform) {
    $platform['journeys'] = Journey_Testing_Journey::get_by_platform($platform['id']);
    $platform['total_runs'] = 0;
    $platform['completed_runs'] = 0;
    
    foreach ($platform['journeys'] as &$journey) {
        $stats = Journey_Testing_Journey::get_statistics($journey['id']);
        $journey['stats'] = $stats;
        $platform['total_runs'] += $stats['total_runs'];
        $platform['completed_runs'] += $stats['completed_runs'];
    }
    
    $total_runs += $platform['total_runs'];
    $completed_runs += $platform['completed_runs'];
}

// Get active runs
$active_runs = Journey_Testing_Test_Run::get_all(array('status' => 'in_progress', 'limit' => 100));
$in_progress_runs = count($active_runs);

?>

<div class="wrap journey-testing-dashboard">
    <h1><?php _e('Journey Testing Dashboard', 'journey-testing'); ?></h1>
    
    <!-- Overview Cards -->
    <div class="journey-cards">
        <div class="journey-card">
            <div class="card-icon">
                <span class="dashicons dashicons-clipboard"></span>
            </div>
            <div class="card-content">
                <h3><?php echo $total_runs; ?></h3>
                <p><?php _e('Total Test Runs', 'journey-testing'); ?></p>
            </div>
        </div>
        
        <div class="journey-card">
            <div class="card-icon">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="card-content">
                <h3><?php echo $completed_runs; ?></h3>
                <p><?php _e('Completed Runs', 'journey-testing'); ?></p>
            </div>
        </div>
        
        <div class="journey-card">
            <div class="card-icon">
                <span class="dashicons dashicons-update"></span>
            </div>
            <div class="card-content">
                <h3><?php echo $in_progress_runs; ?></h3>
                <p><?php _e('In Progress', 'journey-testing'); ?></p>
            </div>
        </div>
        
        <div class="journey-card">
            <div class="card-icon">
                <span class="dashicons dashicons-chart-bar"></span>
            </div>
            <div class="card-content">
                <h3><?php echo $total_runs > 0 ? round(($completed_runs / $total_runs) * 100) : 0; ?>%</h3>
                <p><?php _e('Completion Rate', 'journey-testing'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Platform Overview -->
    <div class="journey-section">
        <h2><?php _e('Platform Overview', 'journey-testing'); ?></h2>
        
        <div class="platform-grid">
            <?php foreach ($platforms as $platform): ?>
                <div class="platform-box">
                    <div class="platform-header">
                        <span class="dashicons <?php echo esc_attr($platform['icon']); ?>"></span>
                        <h3><?php echo esc_html($platform['name']); ?></h3>
                    </div>
                    
                    <div class="platform-stats">
                        <div class="stat">
                            <span class="stat-value"><?php echo count($platform['journeys']); ?></span>
                            <span class="stat-label"><?php _e('Journeys', 'journey-testing'); ?></span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo $platform['total_runs']; ?></span>
                            <span class="stat-label"><?php _e('Total Runs', 'journey-testing'); ?></span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo $platform['completed_runs']; ?></span>
                            <span class="stat-label"><?php _e('Completed', 'journey-testing'); ?></span>
                        </div>
                    </div>
                    
                    <div class="platform-journeys">
                        <?php foreach ($platform['journeys'] as $journey): ?>
                            <div class="journey-item">
                                <div class="journey-name"><?php echo esc_html($journey['name']); ?></div>
                                <div class="journey-stats">
                                    <?php if ($journey['stats']['latest_run']): ?>
                                        <span class="pass-rate"><?php echo $journey['stats']['pass_rate']; ?>% pass</span>
                                    <?php else: ?>
                                        <span class="no-data"><?php _e('No runs yet', 'journey-testing'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="platform-actions">
                        <a href="<?php echo admin_url('admin.php?page=journey-testing-platform-' . $platform['slug']); ?>" class="button">
                            <?php _e('View Details', 'journey-testing'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=journey-testing-new-run&platform=' . $platform['id']); ?>" class="button button-primary">
                            <?php _e('Start Test', 'journey-testing'); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Recent Test Runs -->
    <div class="journey-section">
        <h2><?php _e('Recent Test Runs', 'journey-testing'); ?></h2>
        
        <?php if (!empty($recent_runs)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Journey', 'journey-testing'); ?></th>
                        <th><?php _e('Platform', 'journey-testing'); ?></th>
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
                            <td>
                                <span class="dashicons <?php echo esc_attr($run['platform_icon']); ?>"></span>
                                <?php echo esc_html($run['platform_name']); ?>
                            </td>
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
                <a href="<?php echo admin_url('admin.php?page=journey-testing-runs'); ?>" class="button">
                    <?php _e('View All Test Runs', 'journey-testing'); ?>
                </a>
            </p>
        <?php else: ?>
            <p><?php _e('No test runs yet.', 'journey-testing'); ?></p>
            <p>
                <a href="<?php echo admin_url('admin.php?page=journey-testing-new-run'); ?>" class="button button-primary">
                    <?php _e('Start Your First Test', 'journey-testing'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</div>

<style>
.journey-testing-dashboard {
    max-width: 1200px;
}

.journey-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.journey-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.card-icon {
    font-size: 40px;
    color: #2271b1;
}

.card-content h3 {
    margin: 0;
    font-size: 28px;
    color: #1d2327;
}

.card-content p {
    margin: 5px 0 0;
    color: #646970;
}

.journey-section {
    margin: 30px 0;
}

.platform-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.platform-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
}

.platform-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.platform-header .dashicons {
    font-size: 24px;
    color: #2271b1;
}

.platform-header h3 {
    margin: 0;
    font-size: 18px;
}

.platform-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.stat {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #1d2327;
}

.stat-label {
    display: block;
    font-size: 12px;
    color: #646970;
}

.platform-journeys {
    margin: 15px 0;
}

.journey-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.journey-item:last-child {
    border-bottom: none;
}

.journey-name {
    font-weight: 500;
}

.pass-rate {
    color: #00a32a;
    font-size: 13px;
}

.no-data {
    color: #8c8f94;
    font-size: 13px;
}

.platform-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
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