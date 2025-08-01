# WCAG 2.2 Testing Suite for WordPress

A comprehensive WordPress plugin for testing websites against WCAG 2.2 accessibility standards. This plugin provides a complete testing framework with all success criteria, including the 9 new criteria introduced in WCAG 2.2.

## Features

### 🎯 Complete WCAG 2.2 Coverage
- All success criteria from Level A, AA, and AAA
- Special highlighting for the 9 new WCAG 2.2 criteria
- Organized by the four WCAG principles: Perceivable, Operable, Understandable, and Robust

### 🆕 New WCAG 2.2 Criteria Included
1. **2.4.11 Focus Not Obscured (Minimum)** - Level AA
2. **2.4.12 Focus Not Obscured (Enhanced)** - Level AAA
3. **2.4.13 Focus Appearance** - Level AAA
4. **2.5.7 Dragging Movements** - Level AA
5. **2.5.8 Target Size (Minimum)** - Level AA
6. **3.2.6 Consistent Help** - Level A
7. **3.3.7 Redundant Entry** - Level A
8. **3.3.8 Accessible Authentication (Minimum)** - Level AA
9. **3.3.9 Accessible Authentication (Enhanced)** - Level AAA

### 📊 Testing Features
- **Manual Testing Checklist**: Work through each criterion with detailed test steps
- **Automated Test Integration**: Run basic automated checks (extensible)
- **Issue Tracking**: Document failures with severity levels, descriptions, and recommendations
- **Progress Tracking**: Real-time summary of passed, failed, and warning states

### 📈 Reporting & Export
- Save draft and final reports
- Export reports in multiple formats (CSV, JSON, PDF-ready)
- Historical report tracking
- Issue management system

### 🛠️ Admin Features
- Comprehensive dashboard with statistics
- Configurable default conformance level
- Optional front-end testing toolbar for admins
- Email notifications for critical issues

## Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/wcag-testing-plugin/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to the 'WCAG Testing' menu in your WordPress admin

## Usage

### Starting a New Test

1. Go to **WCAG Testing → New Test**
2. Enter the URL you want to test
3. Select your target conformance level (A, AA, or AAA)
4. Work through each guideline and criterion:
   - Mark each as Pass, Fail, Warning, or N/A
   - For failures, document the issue details
   - Add notes for future reference

### Using the Testing Checklist

Each criterion includes:
- **Description**: What the criterion requires
- **Test Steps**: Specific things to check
- **Result Options**: Pass, Fail, Warning, or Not Applicable
- **Issue Documentation**: For failures, record:
  - Element/location
  - Description of the issue
  - Recommended fix
  - Severity level

### Automated Testing

Click "Run Automated Tests" to perform basic automated checks. This currently includes:
- Basic HTML validation
- Color contrast checking (planned)
- Alt text detection (planned)
- Heading structure analysis (planned)

Note: Automated testing can only catch about 30% of accessibility issues. Manual testing is essential.

### Viewing Reports

1. Go to **WCAG Testing → Reports**
2. View summary statistics for each report
3. Click on a report to see detailed results
4. Export reports in various formats

## Testing Methodology

### Recommended Testing Process

1. **Automated Scan First**: Run automated tests to catch obvious issues
2. **Keyboard Navigation**: Test all functionality using only keyboard
3. **Screen Reader Testing**: Use NVDA, JAWS, or VoiceOver
4. **Visual Inspection**: Check color contrast, text sizing, focus indicators
5. **Interactive Testing**: Test forms, modals, and dynamic content

### Essential Testing Tools

- **Keyboard**: Tab, Shift+Tab, Enter, Space, Arrow keys
- **Screen Readers**: NVDA (free), JAWS, VoiceOver
- **Browser Tools**: Chrome DevTools, Firefox Accessibility Inspector
- **Color Contrast**: Colour Contrast Analyser
- **Mobile Testing**: Real devices for touch interaction testing

## Configuration

### Settings

Navigate to **WCAG Testing → Settings** to configure:

- **Default Conformance Level**: Set default to A, AA, or AAA
- **Enable Toolbar**: Show testing toolbar on front-end (admin only)
- **Auto-save**: Automatically save progress during testing
- **Email Notifications**: Get notified of critical issues

## Development

### Extending the Plugin

The plugin is built with extensibility in mind:

```php
// Add custom automated tests
add_filter('wcag_testing_automated_tests', function($tests) {
    $tests['custom_test'] = array(
        'name' => 'My Custom Test',
        'callback' => 'my_custom_test_function'
    );
    return $tests;
});
```

### Database Structure

The plugin creates two tables:
- `wp_wcag_reports`: Stores test reports
- `wp_wcag_issues`: Stores individual issues

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Modern browser for testing interface

## Support

For issues, feature requests, or contributions, please visit the [GitHub repository](https://github.com/yourdomain/wcag-testing-plugin).

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed for Festool GmbH accessibility testing needs by the BetterQA team.

## Changelog

### Version 1.0.0
- Initial release
- Complete WCAG 2.2 criteria implementation
- Manual testing interface
- Basic automated testing framework
- Report generation and export
- Issue tracking system