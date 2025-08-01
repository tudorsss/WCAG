<?php
/**
 * Settings Page Template
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wcag-testing-settings">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php settings_errors('wcag_settings'); ?>
    
    <form method="post" action="">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="default_level"><?php _e('Default Conformance Level', 'wcag-testing'); ?></label>
                </th>
                <td>
                    <select name="default_level" id="default_level">
                        <option value="A" <?php selected($settings['default_level'], 'A'); ?>>Level A</option>
                        <option value="AA" <?php selected($settings['default_level'], 'AA'); ?>>Level AA (Recommended)</option>
                        <option value="AAA" <?php selected($settings['default_level'], 'AAA'); ?>>Level AAA</option>
                    </select>
                    <p class="description"><?php _e('The default conformance level for new tests. Most organizations target Level AA.', 'wcag-testing'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="enable_toolbar"><?php _e('Enable Testing Toolbar', 'wcag-testing'); ?></label>
                </th>
                <td>
                    <select name="enable_toolbar" id="enable_toolbar">
                        <option value="yes" <?php selected($settings['enable_toolbar'], 'yes'); ?>><?php _e('Yes', 'wcag-testing'); ?></option>
                        <option value="no" <?php selected($settings['enable_toolbar'], 'no'); ?>><?php _e('No', 'wcag-testing'); ?></option>
                    </select>
                    <p class="description"><?php _e('Show the testing toolbar on the front-end of your site (admin users only).', 'wcag-testing'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="auto_save"><?php _e('Auto-save Progress', 'wcag-testing'); ?></label>
                </th>
                <td>
                    <select name="auto_save" id="auto_save">
                        <option value="yes" <?php selected($settings['auto_save'], 'yes'); ?>><?php _e('Yes', 'wcag-testing'); ?></option>
                        <option value="no" <?php selected($settings['auto_save'], 'no'); ?>><?php _e('No', 'wcag-testing'); ?></option>
                    </select>
                    <p class="description"><?php _e('Automatically save test progress as you work.', 'wcag-testing'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="email_notifications"><?php _e('Email Notifications', 'wcag-testing'); ?></label>
                </th>
                <td>
                    <select name="email_notifications" id="email_notifications">
                        <option value="yes" <?php selected($settings['email_notifications'], 'yes'); ?>><?php _e('Yes', 'wcag-testing'); ?></option>
                        <option value="no" <?php selected($settings['email_notifications'], 'no'); ?>><?php _e('No', 'wcag-testing'); ?></option>
                    </select>
                    <p class="description"><?php _e('Send email notifications when critical accessibility issues are found.', 'wcag-testing'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="notification_email"><?php _e('Notification Email', 'wcag-testing'); ?></label>
                </th>
                <td>
                    <input type="email" name="notification_email" id="notification_email" value="<?php echo esc_attr($settings['notification_email']); ?>" class="regular-text" />
                    <p class="description"><?php _e('Email address to receive notifications.', 'wcag-testing'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php wp_nonce_field('wcag_settings', 'wcag_settings_nonce'); ?>
        <?php submit_button(); ?>
    </form>
    
    <div class="wcag-testing-info">
        <h2><?php _e('About WCAG 2.2', 'wcag-testing'); ?></h2>
        <p><?php _e('WCAG 2.2 was published in October 2023 and introduces 9 new success criteria. These new criteria focus on:', 'wcag-testing'); ?></p>
        <ul>
            <li><?php _e('Improved support for users with cognitive and learning disabilities', 'wcag-testing'); ?></li>
            <li><?php _e('Better mobile accessibility', 'wcag-testing'); ?></li>
            <li><?php _e('Enhanced support for users with low vision', 'wcag-testing'); ?></li>
        </ul>
        
        <h3><?php _e('Key Changes from WCAG 2.1', 'wcag-testing'); ?></h3>
        <ul>
            <li><?php _e('Success Criterion 4.1.1 Parsing has been removed (obsolete)', 'wcag-testing'); ?></li>
            <li><?php _e('Focus appearance requirements are now more specific', 'wcag-testing'); ?></li>
            <li><?php _e('Authentication processes must be more accessible', 'wcag-testing'); ?></li>
            <li><?php _e('Dragging actions must have single-pointer alternatives', 'wcag-testing'); ?></li>
        </ul>
        
        <p><a href="https://www.w3.org/TR/WCAG22/" target="_blank"><?php _e('Read the full WCAG 2.2 specification', 'wcag-testing'); ?> <span class="dashicons dashicons-external"></span></a></p>
    </div>
</div>