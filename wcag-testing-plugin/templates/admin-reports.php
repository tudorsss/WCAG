<?php
/**
 * Reports Page Template
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wcag-testing-reports">
    <h1>
        <?php echo esc_html(get_admin_page_title()); ?>
        <a href="<?php echo admin_url('admin.php?page=wcag-testing-new'); ?>" class="page-title-action"><?php _e('New Test', 'wcag-testing'); ?></a>
    </h1>
    
    <?php if (empty($reports)): ?>
        <div class="wcag-testing-empty">
            <p><?php _e('No reports found.', 'wcag-testing'); ?></p>
            <p><a href="<?php echo admin_url('admin.php?page=wcag-testing-new'); ?>" class="button button-primary"><?php _e('Start Your First Test', 'wcag-testing'); ?></a></p>
        </div>
    <?php else: ?>
        <div class="report-filters">
            <label for="filter-level"><?php _e('Filter by Level:', 'wcag-testing'); ?></label>
            <select id="filter-level">
                <option value=""><?php _e('All Levels', 'wcag-testing'); ?></option>
                <option value="A">Level A</option>
                <option value="AA">Level AA</option>
                <option value="AAA">Level AAA</option>
            </select>
            
            <label for="filter-status"><?php _e('Filter by Status:', 'wcag-testing'); ?></label>
            <select id="filter-status">
                <option value=""><?php _e('All Statuses', 'wcag-testing'); ?></option>
                <option value="draft"><?php _e('Draft', 'wcag-testing'); ?></option>
                <option value="final"><?php _e('Final', 'wcag-testing'); ?></option>
            </select>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="column-id"><?php _e('ID', 'wcag-testing'); ?></th>
                    <th class="column-url"><?php _e('URL', 'wcag-testing'); ?></th>
                    <th class="column-date"><?php _e('Test Date', 'wcag-testing'); ?></th>
                    <th class="column-tester"><?php _e('Tester', 'wcag-testing'); ?></th>
                    <th class="column-level"><?php _e('Level', 'wcag-testing'); ?></th>
                    <th class="column-status"><?php _e('Status', 'wcag-testing'); ?></th>
                    <th class="column-results"><?php _e('Results', 'wcag-testing'); ?></th>
                    <th class="column-actions"><?php _e('Actions', 'wcag-testing'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                <tr data-level="<?php echo esc_attr($report->level); ?>" data-status="<?php echo esc_attr($report->status); ?>">
                    <td class="column-id"><?php echo esc_html($report->id); ?></td>
                    <td class="column-url">
                        <a href="<?php echo esc_url($report->url); ?>" target="_blank">
                            <?php echo esc_html($report->url); ?>
                            <span class="dashicons dashicons-external"></span>
                        </a>
                    </td>
                    <td class="column-date">
                        <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($report->test_date))); ?>
                    </td>
                    <td class="column-tester"><?php echo esc_html($report->tester_name); ?></td>
                    <td class="column-level">
                        <span class="wcag-level wcag-level-<?php echo esc_attr(strtolower($report->level)); ?>">
                            <?php echo esc_html($report->level); ?>
                        </span>
                    </td>
                    <td class="column-status">
                        <span class="wcag-status wcag-status-<?php echo esc_attr($report->status); ?>">
                            <?php echo esc_html(ucfirst($report->status)); ?>
                        </span>
                    </td>
                    <td class="column-results">
                        <span class="wcag-result passed" title="<?php esc_attr_e('Passed', 'wcag-testing'); ?>">
                            ✓ <?php echo esc_html($report->passed_count); ?>
                        </span>
                        <span class="wcag-result failed" title="<?php esc_attr_e('Failed', 'wcag-testing'); ?>">
                            ✗ <?php echo esc_html($report->failed_count); ?>
                        </span>
                        <span class="wcag-result warning" title="<?php esc_attr_e('Warnings', 'wcag-testing'); ?>">
                            ⚠ <?php echo esc_html($report->warning_count); ?>
                        </span>
                    </td>
                    <td class="column-actions">
                        <button class="button button-small view-report" data-report-id="<?php echo esc_attr($report->id); ?>">
                            <?php _e('View', 'wcag-testing'); ?>
                        </button>
                        <button class="button button-small export-report" data-report-id="<?php echo esc_attr($report->id); ?>">
                            <?php _e('Export', 'wcag-testing'); ?>
                        </button>
                        <?php if ($report->status === 'draft'): ?>
                        <a href="<?php echo admin_url('admin.php?page=wcag-testing-new&report=' . $report->id); ?>" class="button button-small">
                            <?php _e('Continue', 'wcag-testing'); ?>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="export-actions">
            <h3><?php _e('Bulk Export', 'wcag-testing'); ?></h3>
            <button class="button export-all-csv"><?php _e('Export All as CSV', 'wcag-testing'); ?></button>
            <button class="button export-all-json"><?php _e('Export All as JSON', 'wcag-testing'); ?></button>
        </div>
    <?php endif; ?>
</div>

<!-- Report View Modal -->
<div id="wcag-report-modal" class="wcag-modal" style="display: none;">
    <div class="wcag-modal-content">
        <span class="wcag-modal-close">&times;</span>
        <div class="wcag-modal-body">
            <!-- Report content will be loaded here -->
        </div>
    </div>
</div>

<!-- Export Options Modal -->
<div id="wcag-export-modal" class="wcag-modal" style="display: none;">
    <div class="wcag-modal-content">
        <span class="wcag-modal-close">&times;</span>
        <h2><?php _e('Export Report', 'wcag-testing'); ?></h2>
        <div class="export-options">
            <button class="button button-primary export-format" data-format="csv">
                <span class="dashicons dashicons-media-spreadsheet"></span>
                <?php _e('Export as CSV', 'wcag-testing'); ?>
            </button>
            <button class="button button-primary export-format" data-format="json">
                <span class="dashicons dashicons-media-code"></span>
                <?php _e('Export as JSON', 'wcag-testing'); ?>
            </button>
            <button class="button button-primary export-format" data-format="pdf">
                <span class="dashicons dashicons-media-document"></span>
                <?php _e('Export as PDF', 'wcag-testing'); ?>
            </button>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Filter functionality
    $('#filter-level, #filter-status').on('change', function() {
        const level = $('#filter-level').val();
        const status = $('#filter-status').val();
        
        $('tbody tr').each(function() {
            const $row = $(this);
            const rowLevel = $row.data('level');
            const rowStatus = $row.data('status');
            
            let show = true;
            if (level && rowLevel !== level) show = false;
            if (status && rowStatus !== status) show = false;
            
            $row.toggle(show);
        });
    });
    
    // View report
    $('.view-report').on('click', function() {
        const reportId = $(this).data('report-id');
        // Load report details via AJAX
        $('#wcag-report-modal').show();
    });
    
    // Export report
    $('.export-report').on('click', function() {
        const reportId = $(this).data('report-id');
        $('#wcag-export-modal').data('report-id', reportId).show();
    });
    
    // Export format selection
    $('.export-format').on('click', function() {
        const format = $(this).data('format');
        const reportId = $('#wcag-export-modal').data('report-id');
        
        $.post(wcag_testing_ajax.ajax_url, {
            action: 'wcag_export_report',
            nonce: wcag_testing_ajax.nonce,
            report_id: reportId,
            format: format
        }, function(response) {
            if (response.success) {
                window.location.href = response.data.download_url;
                $('#wcag-export-modal').hide();
            }
        });
    });
    
    // Modal close
    $('.wcag-modal-close').on('click', function() {
        $(this).closest('.wcag-modal').hide();
    });
});
</script>