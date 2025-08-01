<?php
/**
 * WCAG 2.2 Criteria Data
 */
class WCAG_Testing_Criteria {
    
    /**
     * Get all WCAG 2.2 criteria organized by principle
     */
    public static function get_all_criteria() {
        return array(
            'perceivable' => self::get_perceivable_criteria(),
            'operable' => self::get_operable_criteria(),
            'understandable' => self::get_understandable_criteria(),
            'robust' => self::get_robust_criteria()
        );
    }
    
    /**
     * Get Perceivable criteria
     */
    private static function get_perceivable_criteria() {
        return array(
            '1.1' => array(
                'name' => __('Text Alternatives', 'wcag-testing'),
                'criteria' => array(
                    '1.1.1' => array(
                        'name' => __('Non-text Content', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('All non-text content has a text alternative that serves the equivalent purpose.', 'wcag-testing'),
                        'tests' => array(
                            'Check all images have appropriate alt text',
                            'Verify decorative images use alt="" or CSS backgrounds',
                            'Test complex images have detailed descriptions',
                            'Ensure form buttons have descriptive text'
                        )
                    )
                )
            ),
            '1.2' => array(
                'name' => __('Time-based Media', 'wcag-testing'),
                'criteria' => array(
                    '1.2.1' => array(
                        'name' => __('Audio-only and Video-only (Prerecorded)', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Alternatives for time-based media.', 'wcag-testing'),
                        'tests' => array(
                            'Audio-only content has transcript',
                            'Video-only content has audio description or text alternative'
                        )
                    ),
                    '1.2.2' => array(
                        'name' => __('Captions (Prerecorded)', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Captions are provided for all prerecorded audio content.', 'wcag-testing'),
                        'tests' => array(
                            'Captions present and synchronized',
                            'Captions include speaker identification',
                            'Sound effects are described'
                        )
                    ),
                    '1.2.3' => array(
                        'name' => __('Audio Description or Media Alternative', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Alternative for time-based media or audio description.', 'wcag-testing'),
                        'tests' => array(
                            'Audio description track available',
                            'Full text transcript describes visual content'
                        )
                    ),
                    '1.2.4' => array(
                        'name' => __('Captions (Live)', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Captions are provided for all live audio content.', 'wcag-testing'),
                        'tests' => array(
                            'Real-time captions available',
                            'Caption quality is understandable'
                        )
                    ),
                    '1.2.5' => array(
                        'name' => __('Audio Description (Prerecorded)', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Audio description is provided for all prerecorded video content.', 'wcag-testing'),
                        'tests' => array(
                            'Audio description track describes visual elements',
                            'Description fits in natural pauses'
                        )
                    )
                )
            ),
            '1.3' => array(
                'name' => __('Adaptable', 'wcag-testing'),
                'criteria' => array(
                    '1.3.1' => array(
                        'name' => __('Info and Relationships', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Information, structure, and relationships can be programmatically determined.', 'wcag-testing'),
                        'tests' => array(
                            'Headings use proper hierarchy',
                            'Lists use ul/ol/dl markup',
                            'Tables have headers marked',
                            'Form labels associated with inputs',
                            'Required fields clearly marked'
                        )
                    ),
                    '1.3.2' => array(
                        'name' => __('Meaningful Sequence', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Correct reading sequence can be programmatically determined.', 'wcag-testing'),
                        'tests' => array(
                            'Content makes sense when CSS disabled',
                            'Screen reader announces logical order',
                            'Tab order follows visual flow'
                        )
                    ),
                    '1.3.3' => array(
                        'name' => __('Sensory Characteristics', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Instructions do not rely solely on sensory characteristics.', 'wcag-testing'),
                        'tests' => array(
                            'Instructions don\'t rely only on shape/size/position',
                            'Multiple ways to identify UI elements'
                        )
                    ),
                    '1.3.4' => array(
                        'name' => __('Orientation', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Content does not restrict its view to a single display orientation.', 'wcag-testing'),
                        'tests' => array(
                            'Content works in portrait and landscape',
                            'No orientation lock unless essential'
                        )
                    ),
                    '1.3.5' => array(
                        'name' => __('Identify Input Purpose', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('The purpose of input fields can be programmatically determined.', 'wcag-testing'),
                        'tests' => array(
                            'Name fields have autocomplete="name"',
                            'Email has autocomplete="email"',
                            'Address fields properly identified'
                        )
                    )
                )
            ),
            '1.4' => array(
                'name' => __('Distinguishable', 'wcag-testing'),
                'criteria' => array(
                    '1.4.1' => array(
                        'name' => __('Use of Color', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Color is not used as the only visual means of conveying information.', 'wcag-testing'),
                        'tests' => array(
                            'Links distinguishable without color',
                            'Error states not just red',
                            'Charts/graphs have patterns or labels'
                        )
                    ),
                    '1.4.2' => array(
                        'name' => __('Audio Control', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('A mechanism is available to pause or stop audio that plays automatically.', 'wcag-testing'),
                        'tests' => array(
                            'Can pause/stop within 3 seconds',
                            'Volume control available',
                            'Mute option present'
                        )
                    ),
                    '1.4.3' => array(
                        'name' => __('Contrast (Minimum)', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Text has a contrast ratio of at least 4.5:1.', 'wcag-testing'),
                        'tests' => array(
                            'Normal text: 4.5:1 minimum',
                            'Large text (18pt+): 3:1 minimum',
                            'Check over actual backgrounds'
                        )
                    ),
                    '1.4.4' => array(
                        'name' => __('Resize Text', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Text can be resized up to 200% without loss of content.', 'wcag-testing'),
                        'tests' => array(
                            'Zoom browser to 200%',
                            'All text remains readable',
                            'No horizontal scrolling',
                            'Functionality preserved'
                        )
                    ),
                    '1.4.5' => array(
                        'name' => __('Images of Text', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Text is used to convey information rather than images of text.', 'wcag-testing'),
                        'tests' => array(
                            'Real text used instead of images',
                            'Only logos/branding exempt'
                        )
                    ),
                    '1.4.10' => array(
                        'name' => __('Reflow', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Content can be presented without horizontal scrolling.', 'wcag-testing'),
                        'tests' => array(
                            'Set viewport to 1280px, zoom to 400%',
                            'No horizontal scroll for reading',
                            'Content reflows to single column'
                        )
                    ),
                    '1.4.11' => array(
                        'name' => __('Non-text Contrast', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('UI components and graphics have a contrast ratio of at least 3:1.', 'wcag-testing'),
                        'tests' => array(
                            'Buttons: 3:1 contrast with background',
                            'Form fields: 3:1 border contrast',
                            'Icons: 3:1 contrast ratio',
                            'Focus indicators: 3:1 contrast'
                        )
                    ),
                    '1.4.12' => array(
                        'name' => __('Text Spacing', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('No loss of content with modified text spacing.', 'wcag-testing'),
                        'tests' => array(
                            'Line height: 1.5× font size',
                            'Paragraph spacing: 2× font size',
                            'Letter spacing: 0.12× font size',
                            'Word spacing: 0.16× font size'
                        )
                    ),
                    '1.4.13' => array(
                        'name' => __('Content on Hover or Focus', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Additional content on hover/focus can be dismissed.', 'wcag-testing'),
                        'tests' => array(
                            'Can dismiss with Escape key',
                            'Can hover over revealed content',
                            'Content stays until trigger removed'
                        )
                    )
                )
            )
        );
    }
    
    /**
     * Get Operable criteria
     */
    private static function get_operable_criteria() {
        return array(
            '2.1' => array(
                'name' => __('Keyboard Accessible', 'wcag-testing'),
                'criteria' => array(
                    '2.1.1' => array(
                        'name' => __('Keyboard', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('All functionality is available from a keyboard.', 'wcag-testing'),
                        'tests' => array(
                            'Tab to all interactive elements',
                            'Activate with Enter/Space',
                            'No mouse-only interactions',
                            'Test dropdowns, sliders, tabs'
                        )
                    ),
                    '2.1.2' => array(
                        'name' => __('No Keyboard Trap', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Keyboard focus can be moved away from any component.', 'wcag-testing'),
                        'tests' => array(
                            'Tab through entire page',
                            'Can exit all components',
                            'Modal dialogs closeable'
                        )
                    ),
                    '2.1.4' => array(
                        'name' => __('Character Key Shortcuts', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Single character shortcuts can be turned off or remapped.', 'wcag-testing'),
                        'tests' => array(
                            'Can turn off shortcuts',
                            'Can remap shortcuts',
                            'Only active when focused'
                        )
                    )
                )
            ),
            '2.2' => array(
                'name' => __('Enough Time', 'wcag-testing'),
                'criteria' => array(
                    '2.2.1' => array(
                        'name' => __('Timing Adjustable', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Users can control time limits.', 'wcag-testing'),
                        'tests' => array(
                            'Can turn off time limits',
                            '20-second warning given',
                            'Can extend 10× minimum'
                        )
                    ),
                    '2.2.2' => array(
                        'name' => __('Pause, Stop, Hide', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Users can control moving, blinking, or scrolling content.', 'wcag-testing'),
                        'tests' => array(
                            'Can pause animations',
                            'Can stop auto-scrolling',
                            'Can hide distractions'
                        )
                    )
                )
            ),
            '2.3' => array(
                'name' => __('Seizures and Physical Reactions', 'wcag-testing'),
                'criteria' => array(
                    '2.3.1' => array(
                        'name' => __('Three Flashes or Below Threshold', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('No content flashes more than three times per second.', 'wcag-testing'),
                        'tests' => array(
                            'Nothing flashes >3×/second',
                            'Flash area is small',
                            'Contrast is low'
                        )
                    )
                )
            ),
            '2.4' => array(
                'name' => __('Navigable', 'wcag-testing'),
                'criteria' => array(
                    '2.4.1' => array(
                        'name' => __('Bypass Blocks', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('A mechanism to bypass blocks of repeated content.', 'wcag-testing'),
                        'tests' => array(
                            'Skip link as first item',
                            'Skip to main content works',
                            'Other skip options available'
                        )
                    ),
                    '2.4.2' => array(
                        'name' => __('Page Titled', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Pages have titles that describe topic or purpose.', 'wcag-testing'),
                        'tests' => array(
                            'Every page has unique title',
                            'Title describes page purpose',
                            'Title includes site name'
                        )
                    ),
                    '2.4.3' => array(
                        'name' => __('Focus Order', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Components receive focus in a meaningful sequence.', 'wcag-testing'),
                        'tests' => array(
                            'Follows logical reading order',
                            'Matches visual layout',
                            'Groups related items'
                        )
                    ),
                    '2.4.4' => array(
                        'name' => __('Link Purpose (In Context)', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('The purpose of each link can be determined from context.', 'wcag-testing'),
                        'tests' => array(
                            'Links describe destination',
                            'No "click here" alone',
                            'Context provides clarity'
                        )
                    ),
                    '2.4.5' => array(
                        'name' => __('Multiple Ways', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('More than one way to locate a page within a set.', 'wcag-testing'),
                        'tests' => array(
                            'Site search available',
                            'Sitemap or menu',
                            'Related links',
                            'Breadcrumbs'
                        )
                    ),
                    '2.4.6' => array(
                        'name' => __('Headings and Labels', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Headings and labels describe topic or purpose.', 'wcag-testing'),
                        'tests' => array(
                            'Headings describe sections',
                            'Labels describe purpose',
                            'No empty headings'
                        )
                    ),
                    '2.4.7' => array(
                        'name' => __('Focus Visible', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Keyboard focus indicator is visible.', 'wcag-testing'),
                        'tests' => array(
                            'All elements show focus',
                            'Focus clearly visible',
                            'Custom indicators meet contrast'
                        )
                    ),
                    '2.4.11' => array(
                        'name' => __('Focus Not Obscured (Minimum)', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Focused element is not entirely hidden.', 'wcag-testing'),
                        'is_new' => true,
                        'tests' => array(
                            'Tab through with sticky headers',
                            'Check modal positioning',
                            'Test chat widgets',
                            'Some part remains visible'
                        )
                    ),
                    '2.4.12' => array(
                        'name' => __('Focus Not Obscured (Enhanced)', 'wcag-testing'),
                        'level' => 'AAA',
                        'description' => __('Focused element is fully visible.', 'wcag-testing'),
                        'is_new' => true,
                        'tests' => array(
                            'No part of focus hidden',
                            'Full element visible',
                            'Test all overlays'
                        )
                    ),
                    '2.4.13' => array(
                        'name' => __('Focus Appearance', 'wcag-testing'),
                        'level' => 'AAA',
                        'description' => __('Focus indicator meets minimum size and contrast.', 'wcag-testing'),
                        'is_new' => true,
                        'tests' => array(
                            '2px minimum thickness',
                            '3:1 contrast ratio',
                            'Sufficient area covered'
                        )
                    )
                )
            ),
            '2.5' => array(
                'name' => __('Input Modalities', 'wcag-testing'),
                'criteria' => array(
                    '2.5.1' => array(
                        'name' => __('Pointer Gestures', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Multipoint or path-based gestures have alternatives.', 'wcag-testing'),
                        'tests' => array(
                            'Pinch/zoom has buttons too',
                            'Swipe has tap alternative',
                            'Path gestures have simple option'
                        )
                    ),
                    '2.5.2' => array(
                        'name' => __('Pointer Cancellation', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Functions can be cancelled to prevent accidental activation.', 'wcag-testing'),
                        'tests' => array(
                            'Actions on mouse-up, not down',
                            'Can abort by moving away',
                            'Drag-drop is exception'
                        )
                    ),
                    '2.5.3' => array(
                        'name' => __('Label in Name', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Visible label is in accessible name.', 'wcag-testing'),
                        'tests' => array(
                            'Visible text in accessible name',
                            '"Click [visible text]" works',
                            'Icons have tooltips'
                        )
                    ),
                    '2.5.4' => array(
                        'name' => __('Motion Actuation', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Functions triggered by motion have alternatives.', 'wcag-testing'),
                        'tests' => array(
                            'Shake to undo has button',
                            'Tilt controls have alternative',
                            'Can disable motion'
                        )
                    ),
                    '2.5.7' => array(
                        'name' => __('Dragging Movements', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Functions that use dragging have alternatives.', 'wcag-testing'),
                        'is_new' => true,
                        'tests' => array(
                            'Reorder lists with buttons',
                            'Sliders have number input',
                            'Drag-drop has cut/paste',
                            'Test on touch devices'
                        )
                    ),
                    '2.5.8' => array(
                        'name' => __('Target Size (Minimum)', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Interactive targets are at least 24×24 CSS pixels.', 'wcag-testing'),
                        'is_new' => true,
                        'tests' => array(
                            'Measure all targets: 24×24px minimum',
                            'OR 24px spacing between small targets',
                            'Text links inline exempt',
                            'Native controls exempt'
                        )
                    )
                )
            )
        );
    }
    
    /**
     * Get Understandable criteria
     */
    private static function get_understandable_criteria() {
        return array(
            '3.1' => array(
                'name' => __('Readable', 'wcag-testing'),
                'criteria' => array(
                    '3.1.1' => array(
                        'name' => __('Language of Page', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Default human language can be programmatically determined.', 'wcag-testing'),
                        'tests' => array(
                            'html lang attribute present',
                            'Correct language code',
                            'Matches page content'
                        )
                    ),
                    '3.1.2' => array(
                        'name' => __('Language of Parts', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Language of phrases or passages can be determined.', 'wcag-testing'),
                        'tests' => array(
                            'Foreign phrases marked',
                            'Quotes in other languages',
                            'Proper lang attributes'
                        )
                    )
                )
            ),
            '3.2' => array(
                'name' => __('Predictable', 'wcag-testing'),
                'criteria' => array(
                    '3.2.1' => array(
                        'name' => __('On Focus', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Components do not initiate change of context on focus.', 'wcag-testing'),
                        'tests' => array(
                            'Tab through all elements',
                            'No automatic redirects',
                            'No form submission',
                            'No context changes'
                        )
                    ),
                    '3.2.2' => array(
                        'name' => __('On Input', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Changing settings does not automatically change context.', 'wcag-testing'),
                        'tests' => array(
                            'Dropdowns don\'t auto-submit',
                            'Radio buttons don\'t navigate',
                            'Changes are user-initiated'
                        )
                    ),
                    '3.2.3' => array(
                        'name' => __('Consistent Navigation', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Navigation mechanisms are consistent.', 'wcag-testing'),
                        'tests' => array(
                            'Same order across pages',
                            'Same locations',
                            'Same labels'
                        )
                    ),
                    '3.2.4' => array(
                        'name' => __('Consistent Identification', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Components with same functionality are identified consistently.', 'wcag-testing'),
                        'tests' => array(
                            'Same icons = same function',
                            'Same labels throughout',
                            'Consistent behavior'
                        )
                    ),
                    '3.2.6' => array(
                        'name' => __('Consistent Help', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Help mechanisms occur in the same order.', 'wcag-testing'),
                        'is_new' => true,
                        'tests' => array(
                            'Help in same place on all pages',
                            'Contact info consistent location',
                            'Chat widget same position',
                            'Support links same order'
                        )
                    )
                )
            ),
            '3.3' => array(
                'name' => __('Input Assistance', 'wcag-testing'),
                'criteria' => array(
                    '3.3.1' => array(
                        'name' => __('Error Identification', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Input errors are identified and described.', 'wcag-testing'),
                        'tests' => array(
                            'Submit form with errors',
                            'Errors clearly identified',
                            'Error text describes issue',
                            'Field highlighting present'
                        )
                    ),
                    '3.3.2' => array(
                        'name' => __('Labels or Instructions', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Labels or instructions are provided for user input.', 'wcag-testing'),
                        'tests' => array(
                            'All fields have labels',
                            'Required fields marked',
                            'Format examples shown',
                            'Help text available'
                        )
                    ),
                    '3.3.3' => array(
                        'name' => __('Error Suggestion', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Suggestions for correcting errors are provided.', 'wcag-testing'),
                        'tests' => array(
                            'Suggestions for fixing errors',
                            'Format examples provided',
                            'Valid options listed'
                        )
                    ),
                    '3.3.4' => array(
                        'name' => __('Error Prevention (Legal, Financial, Data)', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Submissions can be reversed, checked, or confirmed.', 'wcag-testing'),
                        'tests' => array(
                            'Review step before submission',
                            'Can correct information',
                            'Confirmation required',
                            'Reversible actions'
                        )
                    ),
                    '3.3.7' => array(
                        'name' => __('Redundant Entry', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('Information previously entered is auto-populated or available.', 'wcag-testing'),
                        'is_new' => true,
                        'tests' => array(
                            'Shipping copies from billing',
                            'Previous entries remembered',
                            'Auto-fill supported',
                            'Multi-step forms retain data'
                        )
                    ),
                    '3.3.8' => array(
                        'name' => __('Accessible Authentication (Minimum)', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Authentication does not rely on cognitive function test.', 'wcag-testing'),
                        'is_new' => true,
                        'tests' => array(
                            'Password managers work',
                            'Copy/paste allowed',
                            'Biometrics available',
                            'No puzzle CAPTCHAs',
                            'Email/SMS codes allowed'
                        )
                    ),
                    '3.3.9' => array(
                        'name' => __('Accessible Authentication (Enhanced)', 'wcag-testing'),
                        'level' => 'AAA',
                        'description' => __('No cognitive function test required.', 'wcag-testing'),
                        'is_new' => true,
                        'tests' => array(
                            'No object recognition',
                            'No personal content tests',
                            'Alternative methods available'
                        )
                    )
                )
            )
        );
    }
    
    /**
     * Get Robust criteria
     */
    private static function get_robust_criteria() {
        return array(
            '4.1' => array(
                'name' => __('Compatible', 'wcag-testing'),
                'criteria' => array(
                    '4.1.2' => array(
                        'name' => __('Name, Role, Value', 'wcag-testing'),
                        'level' => 'A',
                        'description' => __('User interface components have name, role, and value.', 'wcag-testing'),
                        'tests' => array(
                            'Screen reader announces correctly',
                            'States are communicated',
                            'ARIA used properly',
                            'Updates announced'
                        )
                    ),
                    '4.1.3' => array(
                        'name' => __('Status Messages', 'wcag-testing'),
                        'level' => 'AA',
                        'description' => __('Status messages can be programmatically determined.', 'wcag-testing'),
                        'tests' => array(
                            'Success messages announced',
                            'Error alerts announced',
                            'Progress updates communicated',
                            'Search results announced'
                        )
                    )
                )
            )
        );
    }
    
    /**
     * Get criteria by level
     */
    public static function get_criteria_by_level($level = 'AA') {
        $all_criteria = self::get_all_criteria();
        $filtered = array();
        
        $levels = array('A');
        if ($level === 'AA' || $level === 'AAA') {
            $levels[] = 'AA';
        }
        if ($level === 'AAA') {
            $levels[] = 'AAA';
        }
        
        foreach ($all_criteria as $principle_key => $principle) {
            $filtered[$principle_key] = array();
            
            foreach ($principle as $guideline_key => $guideline) {
                if (!isset($guideline['criteria'])) {
                    continue;
                }
                
                $filtered_criteria = array();
                foreach ($guideline['criteria'] as $criterion_key => $criterion) {
                    if (in_array($criterion['level'], $levels)) {
                        $filtered_criteria[$criterion_key] = $criterion;
                    }
                }
                
                if (!empty($filtered_criteria)) {
                    $filtered[$principle_key][$guideline_key] = array(
                        'name' => $guideline['name'],
                        'criteria' => $filtered_criteria
                    );
                }
            }
        }
        
        return $filtered;
    }
    
    /**
     * Get new criteria in WCAG 2.2
     */
    public static function get_new_criteria() {
        $all_criteria = self::get_all_criteria();
        $new_criteria = array();
        
        foreach ($all_criteria as $principle_key => $principle) {
            foreach ($principle as $guideline_key => $guideline) {
                if (!isset($guideline['criteria'])) {
                    continue;
                }
                
                foreach ($guideline['criteria'] as $criterion_key => $criterion) {
                    if (isset($criterion['is_new']) && $criterion['is_new']) {
                        $new_criteria[$criterion_key] = $criterion;
                    }
                }
            }
        }
        
        return $new_criteria;
    }
}