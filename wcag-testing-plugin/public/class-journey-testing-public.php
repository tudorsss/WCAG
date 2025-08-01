<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/public
 */

class Journey_Testing_Public {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        // Currently no public styles needed
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        // Currently no public scripts needed
    }
}