<?php
/**
 * Admin Dashboard Template
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wcag-testing-dashboard">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="wcag-testing-welcome">
        <h2><?php _e('Welcome to WCAG 2.2 Testing Suite', 'wcag-testing'); ?></h2>
        <p><?php _e('This plugin helps you test your WordPress site for WCAG 2.2 compliance. The latest version includes 9 new success criteria focusing on cognitive disabilities, motor impairments, and mobile accessibility.', 'wcag-testing'); ?></p>
    </div>
    
    <div class="wcag-testing-stats">
        <div class="stat-box">
            <h3><?php _e('Total Reports', 'wcag-testing'); ?></h3>
            <div class="stat-number"><?php echo esc_html($total_reports); ?></div>
        </div>
        
        <div class="stat-box">
            <h3><?php _e('Open Issues', 'wcag-testing'); ?></h3>
            <div class="stat-number"><?php echo esc_html($open_issues); ?></div>
        </div>
        
        <div class="stat-box critical">
            <h3><?php _e('Critical Issues', 'wcag-testing'); ?></h3>
            <div class="stat-number"><?php echo esc_html($critical_issues); ?></div>
        </div>
    </div>
    
    <div class="wcag-testing-actions">
        <a href="<?php echo admin_url('admin.php?page=wcag-testing-new'); ?>" class="button button-primary button-hero">
            <span class="dashicons dashicons-plus-alt"></span>
            <?php _e('Start New Test', 'wcag-testing'); ?>
        </a>
    </div>
    
    <?php if (!empty($recent_reports)): ?>
    <div class="wcag-testing-recent">
        <h2><?php _e('Recent Reports', 'wcag-testing'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('URL', 'wcag-testing'); ?></th>
                    <th><?php _e('Date', 'wcag-testing'); ?></th>
                    <th><?php _e('Level', 'wcag-testing'); ?></th>
                    <th><?php _e('Status', 'wcag-testing'); ?></th>
                    <th><?php _e('Results', 'wcag-testing'); ?></th>
                    <th><?php _e('Actions', 'wcag-testing'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_reports as $report): ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url($report->url); ?>" target="_blank">
                            <?php echo esc_html($report->url); ?>
                            <span class="dashicons dashicons-external"></span>
                        </a>
                    </td>
                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($report->test_date))); ?></td>
                    <td><span class="wcag-level wcag-level-<?php echo esc_attr(strtolower($report->level)); ?>"><?php echo esc_html($report->level); ?></span></td>
                    <td>
                        <span class="wcag-status wcag-status-<?php echo esc_attr($report->status); ?>">
                            <?php echo esc_html(ucfirst($report->status)); ?>
                        </span>
                    </td>
                    <td>
                        <span class="wcag-result passed"><?php echo esc_html($report->passed_count); ?> <?php _e('Passed', 'wcag-testing'); ?></span>
                        <span class="wcag-result failed"><?php echo esc_html($report->failed_count); ?> <?php _e('Failed', 'wcag-testing'); ?></span>
                        <span class="wcag-result warning"><?php echo esc_html($report->warning_count); ?> <?php _e('Warnings', 'wcag-testing'); ?></span>
                    </td>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=wcag-testing-reports&report=' . $report->id); ?>" class="button button-small">
                            <?php _e('View', 'wcag-testing'); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p><a href="<?php echo admin_url('admin.php?page=wcag-testing-reports'); ?>"><?php _e('View All Reports', 'wcag-testing'); ?> →</a></p>
    </div>
    <?php endif; ?>
    
    <div class="wcag-testing-info">
        <h2><?php _e('What\'s New in WCAG 2.2', 'wcag-testing'); ?></h2>
        <div class="wcag-new-criteria">
            <h3>🆕 <?php _e('New Success Criteria', 'wcag-testing'); ?></h3>
            <ul>
                <li><strong>2.4.11 Focus Not Obscured (Minimum)</strong> - <?php _e('Ensures focused elements aren\'t completely hidden', 'wcag-testing'); ?></li>
                <li><strong>2.4.12 Focus Not Obscured (Enhanced)</strong> - <?php _e('Focused elements must be fully visible', 'wcag-testing'); ?></li>
                <li><strong>2.4.13 Focus Appearance</strong> - <?php _e('Focus indicators must meet size and contrast requirements', 'wcag-testing'); ?></li>
                <li><strong>2.5.7 Dragging Movements</strong> - <?php _e('Dragging actions must have single-pointer alternatives', 'wcag-testing'); ?></li>
                <li><strong>2.5.8 Target Size (Minimum)</strong> - <?php _e('Interactive elements must be at least 24×24 CSS pixels', 'wcag-testing'); ?></li>
                <li><strong>3.2.6 Consistent Help</strong> - <?php _e('Help mechanisms must appear in consistent locations', 'wcag-testing'); ?></li>
                <li><strong>3.3.7 Redundant Entry</strong> - <?php _e('Previously entered information should auto-populate', 'wcag-testing'); ?></li>
                <li><strong>3.3.8 Accessible Authentication (Minimum)</strong> - <?php _e('No cognitive function tests for authentication', 'wcag-testing'); ?></li>
                <li><strong>3.3.9 Accessible Authentication (Enhanced)</strong> - <?php _e('Enhanced authentication without cognitive tests', 'wcag-testing'); ?></li>
            </ul>
        </div>
    </div>
</div>