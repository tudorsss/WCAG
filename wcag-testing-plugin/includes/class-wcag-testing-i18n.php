<?php
/**
 * Define the internationalization functionality
 */
class WCAG_Testing_I18n {
    
    /**
     * Load the plugin text domain for translation
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'wcag-testing',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}