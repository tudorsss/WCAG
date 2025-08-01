<?php
/**
 * Autoloader for Journey Testing Plugin
 */
class Journey_Testing_Autoloader {
    
    /**
     * Register the autoloader
     */
    public static function register() {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }
    
    /**
     * Autoload classes
     *
     * @param string $class_name The class name to load
     */
    public static function autoload($class_name) {
        // Only autoload classes from this plugin
        if (strpos($class_name, 'Journey_Testing') !== 0) {
            return;
        }
        
        // Convert class name to file name
        $file_name = 'class-' . str_replace('_', '-', strtolower($class_name)) . '.php';
        
        // Check in different directories
        $paths = array(
            JOURNEY_TESTING_PLUGIN_DIR . 'includes/',
            JOURNEY_TESTING_PLUGIN_DIR . 'includes/models/',
            JOURNEY_TESTING_PLUGIN_DIR . 'admin/',
            JOURNEY_TESTING_PLUGIN_DIR . 'public/',
        );
        
        foreach ($paths as $path) {
            $file = $path . $file_name;
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
}