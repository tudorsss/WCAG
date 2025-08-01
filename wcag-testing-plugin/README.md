# User Journey Testing Suite for Festool

A comprehensive WordPress plugin for managing and executing user journey tests across Festool's web and app platforms. This plugin provides a complete testing framework for tracking test execution, documenting issues, and managing test versions across multiple testers.

## Features

### 🎯 Platform-Based Organization
- Separate test suites for Web and App platforms
- Organized user journeys for each platform
- Reusable test steps across journeys

### 📊 User Journey Coverage

#### Web Platform Journeys
1. **Complete Purchase Flow**
   - MyFestool Login
   - Search product (via search bar)
   - Add to cart & wishlist
   - Cart management with Product Cards Slider
   - Promo code application
   - Checkout process

2. **Catalog Navigation Flow**
   - MyFestool Login
   - Browse via catalog navigation
   - Product selection and cart management

3. **Dust Extractor Recommendation**
   - Navigate to Anwendungsberater Saugen
   - Complete recommendation flow

4. **Product Information Flow**
   - Product detail page exploration
   - Tutorial viewing

#### App Platform Journeys
1. **Complete Purchase Flow**
   - MyFestool Login
   - Search product (via search bar)
   - Add to cart & wishlist
   - Cart management
   - Checkout process

2. **Catalog Navigation Flow**
   - MyFestool Login
   - Browse via catalog navigation
   - Product selection

3. **MyTools Flow**
   - Navigate to MyTools area
   - Open product information
   - Watch tutorials

### 📈 Testing Features
- **Manual Testing Checklists**: Step-by-step test execution
- **Execution Status Tracking**: Pass/Fail/Blocked/Skipped for each step
- **Issue Documentation**: Detailed failure documentation with severity levels
- **File Attachments**: Screenshots, videos, and documents for test evidence
- **Progress Tracking**: Real-time overview of testing progress per journey

### 👥 Team Features
- **Multiple Testers**: Assign different journeys to team members
- **Test Versioning**: Track changes to test cases over time
- **Test History**: View and compare previous test runs

### 📊 Reporting & Analytics
- **Journey-Level Reports**: Detailed results for each user journey
- **Platform Overview**: Aggregate results by platform
- **Export Capabilities**: Export test results in multiple formats
- **Historical Tracking**: Compare test runs over time

## Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/user-journey-testing/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to the 'Journey Testing' menu in your WordPress admin

## Usage

### Starting a New Test Run

1. Go to **Journey Testing → New Test Run**
2. Select the platform (Web or App)
3. Choose the journey to test
4. Assign a tester
5. Begin executing test steps

### Executing Tests

For each test step:
- Review the step description and expected outcome
- Execute the step manually
- Record the result (Pass/Fail/Blocked/Skipped)
- For failures:
  - Document the issue
  - Add screenshots or videos
  - Specify severity
  - Add recommendations

### Managing Test Journeys

1. Go to **Journey Testing → Manage Journeys**
2. Create or edit journeys for each platform
3. Define test steps (reusable across journeys)
4. Set version numbers for tracking changes

### Viewing Reports

1. Go to **Journey Testing → Reports**
2. Filter by platform, journey, date range, or tester
3. View detailed results for each test run
4. Export reports for stakeholder review

## Test Organization Structure

```
Platform (Web/App)
└── Journey (e.g., Complete Purchase Flow)
    └── Test Steps (e.g., MyFestool Login)
        └── Test Execution
            ├── Result (Pass/Fail/Blocked/Skipped)
            ├── Issues (if failed)
            └── Attachments
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Modern browser for testing interface
- Sufficient storage for file attachments

## Development

### Database Structure

The plugin creates the following tables:
- `wp_journey_platforms`: Stores platform definitions
- `wp_journey_definitions`: Stores user journey templates
- `wp_journey_test_steps`: Stores reusable test step definitions
- `wp_journey_test_runs`: Stores test execution instances
- `wp_journey_test_results`: Stores individual step results
- `wp_journey_issues`: Stores documented issues
- `wp_journey_attachments`: Stores file attachment metadata

### Extending the Plugin

```php
// Add custom test step types
add_filter('journey_testing_step_types', function($types) {
    $types['custom_type'] = array(
        'name' => 'Custom Step Type',
        'icon' => 'dashicons-admin-generic'
    );
    return $types;
});
```

## Support

For issues, feature requests, or contributions, please contact the BetterQA team.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed specifically for Festool GmbH by the BetterQA team to streamline user journey testing across web and app platforms.

## Changelog

### Version 2.0.0
- Complete rewrite from WCAG testing to User Journey testing
- Platform-based test organization
- Support for multiple testers and test versioning
- File attachment capabilities
- Comprehensive reporting system

### Version 1.0.0
- Initial release as WCAG testing tool