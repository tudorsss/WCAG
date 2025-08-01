<?php
/**
 * New Test Template
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wcag-testing-new-test">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form id="wcag-test-form" method="post" action="">
        <div class="wcag-test-settings">
            <h2><?php _e('Test Configuration', 'wcag-testing'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="test-url"><?php _e('URL to Test', 'wcag-testing'); ?></label>
                    </th>
                    <td>
                        <input type="url" id="test-url" name="test_url" class="regular-text" value="<?php echo esc_url(home_url()); ?>" required />
                        <p class="description"><?php _e('Enter the full URL of the page you want to test', 'wcag-testing'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="test-level"><?php _e('Conformance Level', 'wcag-testing'); ?></label>
                    </th>
                    <td>
                        <select id="test-level" name="test_level">
                            <option value="A" <?php selected($default_level, 'A'); ?>>Level A</option>
                            <option value="AA" <?php selected($default_level, 'AA'); ?>>Level AA</option>
                            <option value="AAA" <?php selected($default_level, 'AAA'); ?>>Level AAA</option>
                        </select>
                        <p class="description"><?php _e('Most organizations target Level AA compliance', 'wcag-testing'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="tester-name"><?php _e('Tester Name', 'wcag-testing'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="tester-name" name="tester_name" class="regular-text" value="<?php echo esc_attr(wp_get_current_user()->display_name); ?>" />
                    </td>
                </tr>
            </table>
            
            <div class="test-actions">
                <button type="submit" class="button button-primary"><?php _e('Start Testing', 'wcag-testing'); ?></button>
            </div>
        </div>
        
        <div class="wcag-test-criteria">
            <h2><?php _e('Success Criteria Checklist', 'wcag-testing'); ?></h2>
            <p><?php _e('Work through each criterion and mark the result. Items marked with 🆕 are new in WCAG 2.2.', 'wcag-testing'); ?></p>
            
            <div class="wcag-test-controls">
                <button type="button" class="button expand-all"><?php _e('Expand All', 'wcag-testing'); ?></button>
                <button type="button" class="button collapse-all"><?php _e('Collapse All', 'wcag-testing'); ?></button>
            </div>
            
            <?php foreach ($criteria as $principle_key => $principle): ?>
                <div class="wcag-principle" data-principle="<?php echo esc_attr($principle_key); ?>">
                    <h3><?php echo esc_html(ucfirst($principle_key)); ?></h3>
                    
                    <?php foreach ($principle as $guideline_key => $guideline): ?>
                        <div class="wcag-guideline" data-guideline="<?php echo esc_attr($guideline_key); ?>">
                            <h4>
                                <button type="button" class="toggle-guideline" aria-expanded="false">
                                    <span class="dashicons dashicons-arrow-right"></span>
                                    <?php echo esc_html($guideline_key . ' ' . $guideline['name']); ?>
                                </button>
                            </h4>
                            
                            <div class="wcag-criteria-list" style="display: none;">
                                <?php foreach ($guideline['criteria'] as $criterion_key => $criterion): ?>
                                    <div class="wcag-criterion" data-criterion="<?php echo esc_attr($criterion_key); ?>" data-level="<?php echo esc_attr($criterion['level']); ?>">
                                        <div class="criterion-header">
                                            <h5>
                                                <?php if (isset($criterion['is_new']) && $criterion['is_new']): ?>
                                                    <span class="new-badge">🆕</span>
                                                <?php endif; ?>
                                                <?php echo esc_html($criterion_key . ' ' . $criterion['name']); ?>
                                                <span class="level-badge level-<?php echo esc_attr($criterion['level']); ?>"><?php echo esc_html($criterion['level']); ?></span>
                                            </h5>
                                            <div class="criterion-result">
                                                <label>
                                                    <input type="radio" name="result_<?php echo esc_attr($criterion_key); ?>" value="pass" />
                                                    <span class="result-pass"><?php _e('Pass', 'wcag-testing'); ?></span>
                                                </label>
                                                <label>
                                                    <input type="radio" name="result_<?php echo esc_attr($criterion_key); ?>" value="fail" />
                                                    <span class="result-fail"><?php _e('Fail', 'wcag-testing'); ?></span>
                                                </label>
                                                <label>
                                                    <input type="radio" name="result_<?php echo esc_attr($criterion_key); ?>" value="warning" />
                                                    <span class="result-warning"><?php _e('Warning', 'wcag-testing'); ?></span>
                                                </label>
                                                <label>
                                                    <input type="radio" name="result_<?php echo esc_attr($criterion_key); ?>" value="na" checked />
                                                    <span class="result-na"><?php _e('N/A', 'wcag-testing'); ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="criterion-details">
                                            <p class="description"><?php echo esc_html($criterion['description']); ?></p>
                                            
                                            <?php if (!empty($criterion['tests'])): ?>
                                                <div class="criterion-tests">
                                                    <h6><?php _e('Tests:', 'wcag-testing'); ?></h6>
                                                    <ul>
                                                        <?php foreach ($criterion['tests'] as $test): ?>
                                                            <li>
                                                                <label>
                                                                    <input type="checkbox" name="test_<?php echo esc_attr($criterion_key); ?>[]" value="<?php echo esc_attr($test); ?>" />
                                                                    <?php echo esc_html($test); ?>
                                                                </label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="criterion-notes">
                                                <label for="notes_<?php echo esc_attr($criterion_key); ?>"><?php _e('Notes:', 'wcag-testing'); ?></label>
                                                <textarea id="notes_<?php echo esc_attr($criterion_key); ?>" name="notes_<?php echo esc_attr($criterion_key); ?>" rows="3" class="large-text"></textarea>
                                            </div>
                                            
                                            <div class="criterion-issue" style="display: none;">
                                                <h6><?php _e('Issue Details', 'wcag-testing'); ?></h6>
                                                <label for="issue_element_<?php echo esc_attr($criterion_key); ?>"><?php _e('Element/Location:', 'wcag-testing'); ?></label>
                                                <input type="text" id="issue_element_<?php echo esc_attr($criterion_key); ?>" name="issue_element_<?php echo esc_attr($criterion_key); ?>" class="regular-text" />
                                                
                                                <label for="issue_description_<?php echo esc_attr($criterion_key); ?>"><?php _e('Description:', 'wcag-testing'); ?></label>
                                                <textarea id="issue_description_<?php echo esc_attr($criterion_key); ?>" name="issue_description_<?php echo esc_attr($criterion_key); ?>" rows="3" class="large-text"></textarea>
                                                
                                                <label for="issue_recommendation_<?php echo esc_attr($criterion_key); ?>"><?php _e('Recommendation:', 'wcag-testing'); ?></label>
                                                <textarea id="issue_recommendation_<?php echo esc_attr($criterion_key); ?>" name="issue_recommendation_<?php echo esc_attr($criterion_key); ?>" rows="3" class="large-text"></textarea>
                                                
                                                <label for="issue_severity_<?php echo esc_attr($criterion_key); ?>"><?php _e('Severity:', 'wcag-testing'); ?></label>
                                                <select id="issue_severity_<?php echo esc_attr($criterion_key); ?>" name="issue_severity_<?php echo esc_attr($criterion_key); ?>">
                                                    <option value="low"><?php _e('Low', 'wcag-testing'); ?></option>
                                                    <option value="medium"><?php _e('Medium', 'wcag-testing'); ?></option>
                                                    <option value="high"><?php _e('High', 'wcag-testing'); ?></option>
                                                    <option value="critical"><?php _e('Critical', 'wcag-testing'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="wcag-test-summary">
            <h2><?php _e('Test Summary', 'wcag-testing'); ?></h2>
            <div class="summary-stats">
                <div class="stat">
                    <span class="stat-label"><?php _e('Passed:', 'wcag-testing'); ?></span>
                    <span class="stat-value passed" id="summary-passed">0</span>
                </div>
                <div class="stat">
                    <span class="stat-label"><?php _e('Failed:', 'wcag-testing'); ?></span>
                    <span class="stat-value failed" id="summary-failed">0</span>
                </div>
                <div class="stat">
                    <span class="stat-label"><?php _e('Warnings:', 'wcag-testing'); ?></span>
                    <span class="stat-value warning" id="summary-warnings">0</span>
                </div>
                <div class="stat">
                    <span class="stat-label"><?php _e('Not Applicable:', 'wcag-testing'); ?></span>
                    <span class="stat-value na" id="summary-na">0</span>
                </div>
            </div>
            
            <div class="test-actions">
                <button type="submit" name="save_draft" class="button"><?php _e('Save as Draft', 'wcag-testing'); ?></button>
                <button type="submit" name="save_final" class="button button-primary"><?php _e('Save Final Report', 'wcag-testing'); ?></button>
            </div>
        </div>
        
        <?php wp_nonce_field('wcag_test_nonce', 'wcag_test_nonce'); ?>
    </form>
</div>