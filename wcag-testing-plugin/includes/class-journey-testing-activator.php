<?php
/**
 * Fired during plugin activation
 *
 * @package    Journey_Testing
 * @subpackage Journey_Testing/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class Journey_Testing_Activator {

    /**
     * Activate the plugin.
     *
     * Create database tables and set up initial data.
     */
    public static function activate() {
        self::create_tables();
        self::create_default_data();
        self::set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Platforms table
        $table_platforms = $wpdb->prefix . 'journey_platforms';
        $sql_platforms = "CREATE TABLE $table_platforms (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            slug varchar(100) NOT NULL,
            description text,
            icon varchar(50),
            display_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        
        // Journey definitions table
        $table_journeys = $wpdb->prefix . 'journey_definitions';
        $sql_journeys = "CREATE TABLE $table_journeys (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            platform_id mediumint(9) NOT NULL,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            description text,
            version varchar(20) DEFAULT '1.0.0',
            status varchar(20) DEFAULT 'active',
            display_order int(11) DEFAULT 0,
            created_by bigint(20) unsigned,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY platform_id (platform_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Test steps table
        $table_steps = $wpdb->prefix . 'journey_test_steps';
        $sql_steps = "CREATE TABLE $table_steps (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            journey_id mediumint(9) NOT NULL,
            title varchar(255) NOT NULL,
            description text,
            expected_result text,
            step_order int(11) DEFAULT 0,
            is_required tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY journey_id (journey_id)
        ) $charset_collate;";
        
        // Test runs table
        $table_runs = $wpdb->prefix . 'journey_test_runs';
        $sql_runs = "CREATE TABLE $table_runs (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            journey_id mediumint(9) NOT NULL,
            tester_id bigint(20) unsigned NOT NULL,
            status varchar(20) DEFAULT 'in_progress',
            started_at datetime DEFAULT CURRENT_TIMESTAMP,
            completed_at datetime,
            notes text,
            PRIMARY KEY (id),
            KEY journey_id (journey_id),
            KEY tester_id (tester_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Test results table
        $table_results = $wpdb->prefix . 'journey_test_results';
        $sql_results = "CREATE TABLE $table_results (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            test_run_id mediumint(9) NOT NULL,
            test_step_id mediumint(9) NOT NULL,
            result varchar(20) NOT NULL,
            notes text,
            tested_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY test_run_id (test_run_id),
            KEY test_step_id (test_step_id),
            KEY result (result)
        ) $charset_collate;";
        
        // Issues table
        $table_issues = $wpdb->prefix . 'journey_issues';
        $sql_issues = "CREATE TABLE $table_issues (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            test_result_id mediumint(9) NOT NULL,
            severity varchar(20) NOT NULL,
            description text NOT NULL,
            steps_to_reproduce text,
            expected_behavior text,
            actual_behavior text,
            recommendation text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY test_result_id (test_result_id),
            KEY severity (severity)
        ) $charset_collate;";
        
        // Attachments table
        $table_attachments = $wpdb->prefix . 'journey_attachments';
        $sql_attachments = "CREATE TABLE $table_attachments (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            object_type varchar(50) NOT NULL,
            object_id mediumint(9) NOT NULL,
            file_name varchar(255) NOT NULL,
            file_path varchar(500) NOT NULL,
            file_type varchar(100),
            file_size bigint(20),
            uploaded_by bigint(20) unsigned,
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY object_lookup (object_type, object_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_platforms);
        dbDelta($sql_journeys);
        dbDelta($sql_steps);
        dbDelta($sql_runs);
        dbDelta($sql_results);
        dbDelta($sql_issues);
        dbDelta($sql_attachments);
    }
    
    /**
     * Create default data
     */
    private static function create_default_data() {
        global $wpdb;
        
        // Insert default platforms
        $platforms_table = $wpdb->prefix . 'journey_platforms';
        
        $wpdb->insert($platforms_table, array(
            'name' => 'Web Platform',
            'slug' => 'web',
            'description' => 'Festool web platform testing',
            'icon' => 'dashicons-laptop',
            'display_order' => 1
        ));
        $web_platform_id = $wpdb->insert_id;
        
        $wpdb->insert($platforms_table, array(
            'name' => 'App Platform',
            'slug' => 'app',
            'description' => 'Festool mobile app testing',
            'icon' => 'dashicons-smartphone',
            'display_order' => 2
        ));
        $app_platform_id = $wpdb->insert_id;
        
        // Insert default journeys for Web platform
        $journeys_table = $wpdb->prefix . 'journey_definitions';
        
        $web_journeys = array(
            array(
                'name' => 'Complete Purchase Flow',
                'slug' => 'web-complete-purchase',
                'description' => 'Full purchase journey from login to checkout',
                'display_order' => 1
            ),
            array(
                'name' => 'Catalog Navigation Flow',
                'slug' => 'web-catalog-navigation',
                'description' => 'Product discovery through catalog navigation',
                'display_order' => 2
            ),
            array(
                'name' => 'Dust Extractor Recommendation',
                'slug' => 'web-dust-extractor',
                'description' => 'Anwendungsberater Saugen recommendation flow',
                'display_order' => 3
            ),
            array(
                'name' => 'Product Information Flow',
                'slug' => 'web-product-info',
                'description' => 'Product detail page and tutorial viewing',
                'display_order' => 4
            )
        );
        
        foreach ($web_journeys as $journey) {
            $journey['platform_id'] = $web_platform_id;
            $journey['created_by'] = get_current_user_id();
            $wpdb->insert($journeys_table, $journey);
            $journey_id = $wpdb->insert_id;
            
            // Add default test steps based on journey
            self::create_default_test_steps($journey_id, $journey['slug']);
        }
        
        // Insert default journeys for App platform
        $app_journeys = array(
            array(
                'name' => 'Complete Purchase Flow',
                'slug' => 'app-complete-purchase',
                'description' => 'Full purchase journey from login to checkout',
                'display_order' => 1
            ),
            array(
                'name' => 'Catalog Navigation Flow',
                'slug' => 'app-catalog-navigation',
                'description' => 'Product discovery through catalog navigation',
                'display_order' => 2
            ),
            array(
                'name' => 'MyTools Flow',
                'slug' => 'app-mytools',
                'description' => 'MyTools area navigation and tutorial viewing',
                'display_order' => 3
            )
        );
        
        foreach ($app_journeys as $journey) {
            $journey['platform_id'] = $app_platform_id;
            $journey['created_by'] = get_current_user_id();
            $wpdb->insert($journeys_table, $journey);
            $journey_id = $wpdb->insert_id;
            
            // Add default test steps based on journey
            self::create_default_test_steps($journey_id, $journey['slug']);
        }
    }
    
    /**
     * Create default test steps for a journey
     */
    private static function create_default_test_steps($journey_id, $journey_slug) {
        global $wpdb;
        $steps_table = $wpdb->prefix . 'journey_test_steps';
        
        $test_steps = array();
        
        switch ($journey_slug) {
            case 'web-complete-purchase':
            case 'app-complete-purchase':
                $test_steps = array(
                    array(
                        'title' => 'Navigate to MyFestool Login',
                        'description' => 'Go to the MyFestool login page',
                        'expected_result' => 'Login page loads successfully with username and password fields'
                    ),
                    array(
                        'title' => 'Login to MyFestool',
                        'description' => 'Enter valid credentials and submit login form',
                        'expected_result' => 'Successfully logged in and redirected to account dashboard'
                    ),
                    array(
                        'title' => 'Search for Product',
                        'description' => 'Use the search bar to search for a specific product',
                        'expected_result' => 'Search results display relevant products'
                    ),
                    array(
                        'title' => 'Add Product to Cart',
                        'description' => 'Click "Add to Cart" button on a product',
                        'expected_result' => 'Product is added to cart with confirmation message'
                    ),
                    array(
                        'title' => 'Add Product to Wishlist',
                        'description' => 'Click "Add to Wishlist" button on the same or different product',
                        'expected_result' => 'Product is added to wishlist with confirmation'
                    ),
                    array(
                        'title' => 'Open Shopping Cart',
                        'description' => 'Navigate to the shopping cart page',
                        'expected_result' => 'Cart page displays with added products'
                    ),
                    array(
                        'title' => 'Add Products from Slider',
                        'description' => 'Use Product Cards Slider to add additional products',
                        'expected_result' => 'Additional products are successfully added to cart'
                    ),
                    array(
                        'title' => 'Enter Promo Code',
                        'description' => 'Enter a valid promotional code in the cart',
                        'expected_result' => 'Promo code is applied and discount is reflected'
                    ),
                    array(
                        'title' => 'Proceed to Checkout',
                        'description' => 'Click checkout button and fill in required information',
                        'expected_result' => 'Checkout process completes up to payment selection'
                    )
                );
                break;
                
            case 'web-catalog-navigation':
            case 'app-catalog-navigation':
                $test_steps = array(
                    array(
                        'title' => 'Navigate to MyFestool Login',
                        'description' => 'Go to the MyFestool login page',
                        'expected_result' => 'Login page loads successfully'
                    ),
                    array(
                        'title' => 'Login to MyFestool',
                        'description' => 'Enter valid credentials and submit login form',
                        'expected_result' => 'Successfully logged in'
                    ),
                    array(
                        'title' => 'Open Catalog Navigation',
                        'description' => 'Access the main product catalog menu',
                        'expected_result' => 'Catalog categories are displayed'
                    ),
                    array(
                        'title' => 'Browse Category',
                        'description' => 'Select a product category to browse',
                        'expected_result' => 'Products in selected category are displayed'
                    ),
                    array(
                        'title' => 'Apply Filters',
                        'description' => 'Use filters to narrow down product selection',
                        'expected_result' => 'Product list updates based on applied filters'
                    ),
                    array(
                        'title' => 'Select Product',
                        'description' => 'Click on a product from the filtered results',
                        'expected_result' => 'Product detail page opens'
                    ),
                    array(
                        'title' => 'Add to Cart from Catalog',
                        'description' => 'Add the selected product to cart',
                        'expected_result' => 'Product is added to cart successfully'
                    )
                );
                break;
                
            case 'web-dust-extractor':
                $test_steps = array(
                    array(
                        'title' => 'Navigate to Dust Extractor Tool',
                        'description' => 'Go to Anwendungsberater Saugen page',
                        'expected_result' => 'Recommendation tool loads successfully'
                    ),
                    array(
                        'title' => 'Select Application Type',
                        'description' => 'Choose the type of application/work',
                        'expected_result' => 'Next step or options are displayed'
                    ),
                    array(
                        'title' => 'Specify Requirements',
                        'description' => 'Enter specific requirements for dust extraction',
                        'expected_result' => 'Form accepts input and shows relevant options'
                    ),
                    array(
                        'title' => 'Review Recommendations',
                        'description' => 'View the recommended dust extractors',
                        'expected_result' => 'Appropriate products are recommended based on inputs'
                    ),
                    array(
                        'title' => 'Compare Products',
                        'description' => 'Use comparison feature for recommended products',
                        'expected_result' => 'Comparison table displays with key features'
                    ),
                    array(
                        'title' => 'Select Recommended Product',
                        'description' => 'Choose one of the recommended products',
                        'expected_result' => 'Product detail page opens for selected item'
                    )
                );
                break;
                
            case 'web-product-info':
                $test_steps = array(
                    array(
                        'title' => 'Navigate to Product',
                        'description' => 'Go to a specific product detail page',
                        'expected_result' => 'Product page loads with all information'
                    ),
                    array(
                        'title' => 'Review Product Information',
                        'description' => 'Check product specifications, features, and details',
                        'expected_result' => 'All product information is displayed correctly'
                    ),
                    array(
                        'title' => 'View Product Images',
                        'description' => 'Browse through product image gallery',
                        'expected_result' => 'Images load and can be viewed in detail'
                    ),
                    array(
                        'title' => 'Check Technical Data',
                        'description' => 'Review technical specifications tab',
                        'expected_result' => 'Technical data is complete and accurate'
                    ),
                    array(
                        'title' => 'Find Tutorial Section',
                        'description' => 'Locate the tutorial/video section on the page',
                        'expected_result' => 'Tutorial section is visible and accessible'
                    ),
                    array(
                        'title' => 'Watch Tutorial Video',
                        'description' => 'Play a tutorial video for the product',
                        'expected_result' => 'Video plays successfully with good quality'
                    )
                );
                break;
                
            case 'app-mytools':
                $test_steps = array(
                    array(
                        'title' => 'Navigate to MyFestool Login',
                        'description' => 'Go to the MyFestool login page in app',
                        'expected_result' => 'Login page loads successfully'
                    ),
                    array(
                        'title' => 'Login to MyFestool',
                        'description' => 'Enter valid credentials and submit',
                        'expected_result' => 'Successfully logged in to app'
                    ),
                    array(
                        'title' => 'Navigate to MyTools',
                        'description' => 'Access MyTools section from menu',
                        'expected_result' => 'MyTools area opens with registered products'
                    ),
                    array(
                        'title' => 'Select a Tool',
                        'description' => 'Choose a registered tool from MyTools list',
                        'expected_result' => 'Tool details page opens'
                    ),
                    array(
                        'title' => 'View Tool Information',
                        'description' => 'Review tool details and warranty information',
                        'expected_result' => 'All tool information is displayed correctly'
                    ),
                    array(
                        'title' => 'Access Tutorials',
                        'description' => 'Find and open tutorial section for the tool',
                        'expected_result' => 'Tutorial list is displayed'
                    ),
                    array(
                        'title' => 'Watch Tutorial',
                        'description' => 'Play a tutorial video from the list',
                        'expected_result' => 'Video plays successfully in app'
                    )
                );
                break;
        }
        
        // Insert test steps
        foreach ($test_steps as $order => $step) {
            $wpdb->insert($steps_table, array(
                'journey_id' => $journey_id,
                'title' => $step['title'],
                'description' => $step['description'],
                'expected_result' => $step['expected_result'],
                'step_order' => $order,
                'is_required' => 1
            ));
        }
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        add_option('journey_testing_version', JOURNEY_TESTING_VERSION);
        add_option('journey_testing_upload_dir', 'journey-testing-uploads');
        add_option('journey_testing_max_file_size', 10485760); // 10MB
        add_option('journey_testing_allowed_file_types', array('jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'pdf', 'doc', 'docx'));
    }
}