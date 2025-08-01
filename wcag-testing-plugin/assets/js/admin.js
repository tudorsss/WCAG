/**
 * WCAG Testing Plugin - Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize test form if present
        if ($('#wcag-test-form').length) {
            initTestForm();
        }
    });
    
    /**
     * Initialize the test form
     */
    function initTestForm() {
        // Toggle guideline sections
        $('.toggle-guideline').on('click', function() {
            const $button = $(this);
            const $criteriaList = $button.closest('.wcag-guideline').find('.wcag-criteria-list');
            const isExpanded = $button.attr('aria-expanded') === 'true';
            
            $button.attr('aria-expanded', !isExpanded);
            $criteriaList.slideToggle(300);
        });
        
        // Expand/Collapse all buttons
        $('.expand-all').on('click', function() {
            $('.toggle-guideline').attr('aria-expanded', 'true');
            $('.wcag-criteria-list').slideDown(300);
        });
        
        $('.collapse-all').on('click', function() {
            $('.toggle-guideline').attr('aria-expanded', 'false');
            $('.wcag-criteria-list').slideUp(300);
        });
        
        // Handle result changes
        $('input[type="radio"][name^="result_"]').on('change', function() {
            const $criterion = $(this).closest('.wcag-criterion');
            const value = $(this).val();
            const $issueSection = $criterion.find('.criterion-issue');
            
            // Show/hide issue details based on result
            if (value === 'fail' || value === 'warning') {
                $issueSection.slideDown(300);
            } else {
                $issueSection.slideUp(300);
            }
            
            // Update summary
            updateSummary();
        });
        
        // Form submission
        $('#wcag-test-form').on('submit', function(e) {
            e.preventDefault();
            saveReport($(this));
        });
        
        // Initialize summary
        updateSummary();
    }
    
    /**
     * Update test summary
     */
    function updateSummary() {
        let passed = 0;
        let failed = 0;
        let warnings = 0;
        let na = 0;
        
        $('input[type="radio"][name^="result_"]:checked').each(function() {
            const value = $(this).val();
            switch(value) {
                case 'pass':
                    passed++;
                    break;
                case 'fail':
                    failed++;
                    break;
                case 'warning':
                    warnings++;
                    break;
                case 'na':
                    na++;
                    break;
            }
        });
        
        $('#summary-passed').text(passed);
        $('#summary-failed').text(failed);
        $('#summary-warnings').text(warnings);
        $('#summary-na').text(na);
    }
    
    /**
     * Save report
     */
    function saveReport($form) {
        const formData = $form.serializeArray();
        const $submitButton = $form.find('button[type="submit"]:focus');
        const isFinal = $submitButton.attr('name') === 'save_final';
        
        // Collect test results
        const results = {};
        const issues = [];
        
        $('input[type="radio"][name^="result_"]:checked').each(function() {
            const criterion = $(this).attr('name').replace('result_', '');
            const value = $(this).val();
            results[criterion] = value;
            
            // Collect issue details if failed or warning
            if (value === 'fail' || value === 'warning') {
                const $criterion = $(this).closest('.wcag-criterion');
                issues.push({
                    criterion: criterion,
                    level: $criterion.data('level'),
                    severity: $(`#issue_severity_${criterion}`).val(),
                    element: $(`#issue_element_${criterion}`).val(),
                    description: $(`#issue_description_${criterion}`).val(),
                    recommendation: $(`#issue_recommendation_${criterion}`).val()
                });
            }
        });
        
        // Prepare AJAX data
        const data = {
            action: 'wcag_save_report',
            nonce: wcag_testing_ajax.nonce,
            url: $('#test-url').val(),
            level: $('#test-level').val(),
            tester_name: $('#tester-name').val(),
            status: isFinal ? 'final' : 'draft',
            passed: $('#summary-passed').text(),
            failed: $('#summary-failed').text(),
            warnings: $('#summary-warnings').text(),
            report_data: results,
            issues: issues
        };
        
        // Show saving indicator
        $submitButton.prop('disabled', true).text(wcag_testing_ajax.strings.saving);
        
        // Send AJAX request
        $.post(wcag_testing_ajax.ajax_url, data, function(response) {
            if (response.success) {
                showNotification(response.data.message, 'success');
                
                // Redirect to reports page after a moment
                setTimeout(function() {
                    window.location.href = `admin.php?page=wcag-testing-reports&report=${response.data.report_id}`;
                }, 1500);
            } else {
                showNotification(response.data || wcag_testing_ajax.strings.error, 'error');
                $submitButton.prop('disabled', false).text(isFinal ? 'Save Final Report' : 'Save as Draft');
            }
        }).fail(function() {
            showNotification(wcag_testing_ajax.strings.error, 'error');
            $submitButton.prop('disabled', false).text(isFinal ? 'Save Final Report' : 'Save as Draft');
        });
    }
    
    /**
     * Show notification
     */
    function showNotification(message, type) {
        const $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap > h1').after($notice);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
})(jQuery);