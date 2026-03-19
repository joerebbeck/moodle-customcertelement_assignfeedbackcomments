<?php
// This file is part of the customcert module for Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Base class with implementation logic for the Assignment feedback element.
 *
 * This class contains all the logic and methods for both legacy and Element System v2 AC Is
 * but does NOT implement any interfaces directly to avoid Fatal Errors on older mod_customcert
 * versions.
 *
 * @package customcertelement_assignfeedbackcomments
 * @copyright 2026 Joe Rebbeck <tjr@the-ela.com>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_assignfeedbackcomments;

defined('MOODLE_INTERNAL') || die();

/**
 * Assignment feedback element base class.
 */
abstract class element_base extends \mod_customcert\element {

    // =========================================================================
    // Element System v2 methods (mod_customcert 5.2+)
    // =========================================================================

    /**
     * Adds the element-specific fields to the form (v2 API).
     *
     * @param \MoodleQuickForm $mform The Moodle form.
     */
    public function build_form(\MoodleQuickForm $mform): void {
        global $COURSE;

        $assignments = $this->get_course_assignments($COURSE->id);
        $options = [0 => get_string('chooseassignment', 'customcertelement_assignfeedbackcomments')] + $assignments;

        $mform->addElement('select', 'assignid', get_string('assignment', 'customcertelement_assignfeedbackcomments'), $options);
        $mform->setType('assignid', PARAM_INT);
        $mform->addHelpButton('assignid', 'assignid', 'customcertelement_assignfeedbackcomments');

        // Character limit setting
        $mform->addElement('text', 'char_limit', get_string('charlimit', 'customcertelement_assignfeedbackcomments'));
        $mform->setType('char_limit', PARAM_INT);
        $mform->setDefault('char_limit', 1000);
        $mform->addHelpButton('char_limit', 'charlimit', 'customcertelement_assignfeedbackcomments');

        // Coupling notice: inform instructors of the feedback sub-plugin limitation
        $mform->addElement('static', 'assignfeedback_notice', '',
            get_string('feedbackcommentsonly', 'customcertelement_assignfeedbackcomments'));

        // Guard for v2: add common form elements if the required v2 standard is fully active
        if (interface_exists('\mod_customcert\element\form_buildable_interface') && 
            ($this instanceof \mod_customcert\element\form_buildable_interface)) {
            \mod_customcert\element_helper::render_common_form_elements($mform);
        }
    }

    /**
     * Validates the submitted form data (v2 API).
     *
     * @param array $data Submitted form data.
     * @return array Associative array of field => error string.
     */
    public function validate(array $data): array {
        $errors = [];
        if (empty($data['assignid'])) {
            $errors['assignid'] = get_string('required');
        }
        return $errors;
    }

    /**
     * Normalises the form data for JSON persistence (v2 API).
     *
     * @param \stdClass $formdata The form data.
     * @return array The normalised array data to be merged or saved.
     */
    public function normalise_data(\stdClass $formdata): array {
        return [
            'assignid' => (int) ($formdata->assignid ?? 0),
            'char_limit' => !empty($formdata->char_limit) ? (int) $formdata->char_limit : 0,
        ];
    }

    /**
     * Prepares the form with element data (v2 API).
     *
     * @param \MoodleQuickForm $mform The Moodle form.
     */
    public function prepare_form(\MoodleQuickForm $mform): void {
        // Use v2 get_payload() if available, otherwise fallback handles decoding
        if (method_exists($this, 'get_payload')) {
            $payload = $this->get_payload();
            if (is_array($payload)) {
                if (array_key_exists('assignid', $payload)) {
                    $mform->setDefault('assignid', $payload['assignid']);
                }
                if (array_key_exists('char_limit', $payload)) {
                    $mform->setDefault('char_limit', $payload['char_limit']);
                }
            }
        }
    }

    // =========================================================================
    // Legacy API methods (mod_customcert 4.1 - 5.1)
    // =========================================================================

    /**
     * Renders the form elements for this element (legacy API).
     *
     * @param \MoodleQuickForm $mform The form being rendered.
     */
    public function render_form_elements($mform) {
        $this->build_form($mform);
        parent::render_form_elements($mform);
    }

    /**
     * Validates form data for this element (legacy API).
     *
     * @param array $data The form data.
     * @param array $files The uploaded files.
     * @return array Validation errors.
     */
    public function validate_form_elements($data, $files) {
        $errors = parent::validate_form_elements($data, $files);
        return array_merge($errors, $this->validate($data));
    }

    /**
     * Handles saving the unique data for this element into the database.
     *
     * @param \stdClass $data the form data
     * @return string the unique data to save
     */
    public function save_unique_data($data) {
        return json_encode([
            'assignid' => (int) ($data->assignid ?? 0),
            'char_limit' => !empty($data->char_limit) ? (int) $data->char_limit : 0,
        ]);
    }

    /**
     * Sets the data on the form when editing an element (legacy API).
     *
     * @param \mod_customcert\edit_element_form $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        $data = json_decode($this->get_data(), true);
        if (!empty($data['assignid'])) {
            if ($mform->elementExists('assignid')) {
                $mform->getElement('assignid')->setValue($data['assignid']);
            }
        }
        if (!empty($data['char_limit'])) {
            if ($mform->elementExists('char_limit')) {
                $mform->getElement('char_limit')->setValue($data['char_limit']);
            }
        }
        parent::definition_after_data($mform);
    }

    // =========================================================================
    // Render / preview
    // =========================================================================

    /**
     * Renders this element as an HTML string for preview.
     *
     * @return string
     */
    public function render_html() {
        return $this->preview_text();
    }

    /**
     * Returns a sample string for the PDF preview.
     *
     * @return string
     */
    public function preview_text() {
        return get_string('pluginname', 'customcertelement_assignfeedbackcomments');
    }

    /**
     * Renders this element on the PDF certificate.
     *
     * Security: verifies mod/customcert:view capability before any data access.
     *
     * @param \pdf      $pdf     The PDF object.
     * @param bool      $preview Whether this is a preview render.
     * @param \stdClass $user    The user the certificate is being generated for.
     */
    public function render($pdf, $preview, $user) {
        $context = \mod_customcert\element_helper::get_context($this->get_id());
        require_capability('mod/customcert:view', $context);

        if ($preview) {
            \mod_customcert\element_helper::render_content($pdf, $this, $this->preview_text());
            return;
        }

        $elementdata = json_decode($this->get_data(), true);
        $assignid    = (int) ($elementdata['assignid'] ?? 0);
        $charlimit   = !empty($elementdata['char_limit']) ? (int) $elementdata['char_limit'] : 0;

        $feedback = $this->get_feedback_for_user($assignid, $user->id, $charlimit);

        \mod_customcert\element_helper::render_content($pdf, $this, $feedback);
    }

    // =========================================================================
    // Internal helpers
    // =========================================================================

    /**
     * Guards against running when mod_assign is not installed or the
     * specific assignment record no longer exists.
     *
     * @param int $assignid The assignment ID to validate.
     * @return bool True if the assignment is accessible, false otherwise.
     */
    protected function assignment_is_available(int $assignid): bool {
        $plugininfo = \core_plugin_manager::instance()->get_plugin_info('mod_assign');
        if ($plugininfo === null || !$plugininfo->is_enabled()) {
            return false;
        }

        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        return $DB->record_exists('assign', ['id' => $assignid]);
    }

    /**
     * Retrieves and PDF-safe-formats the grader's feedback for a user.
     *
     * @param int $assignid The assignment ID.
     * @param int $userid The target user ID.
     * @param int $charlimit Optional character limit for feedback truncation.
     * @return string PDF-safe feedback string, or an appropriate placeholder.
     */
    protected function get_feedback_for_user(int $assignid, int $userid, int $charlimit = 0): string {
        global $DB;

        if (empty($assignid)) {
            return '';
        }

        $cache    = \cache::make('customcertelement_assignfeedbackcomments', 'feedbackcache');
        $cachekey = "feedback_{$assignid}_{$userid}";
        $cached   = $cache->get($cachekey);

        if ($cached !== false) {
            return $cached;
        }

        if (!$this->assignment_is_available($assignid)) {
            $result = get_string('feedbacknotavailable', 'customcertelement_assignfeedbackcomments');
            $cache->set($cachekey, $result);
            return $result;
        }

        $sql = "SELECT ag.id      AS gradeid,
                       fc.commenttext,
                       fc.commentformat
                  FROM {assign_grades}                ag
              LEFT JOIN {assignfeedback_comments}      fc ON fc.grade = ag.id
                 WHERE ag.assignment = :assignid
                   AND ag.userid     = :userid";

        $row = $DB->get_record_sql($sql, [
            'assignid' => $assignid,
            'userid'   => $userid,
        ]);

        if (!$row) {
            $result = get_string('feedbacknotavailable', 'customcertelement_assignfeedbackcomments');
            $cache->set($cachekey, $result);
            return $result;
        }

        if (empty($row->commenttext)) {
            $result = get_string('nofeedbackprovided', 'customcertelement_assignfeedbackcomments');
            $cache->set($cachekey, $result);
            return $result;
        }

        $context   = \mod_customcert\element_helper::get_context($this->get_id());
        $formatted = format_text($row->commenttext, $row->commentformat, [
            'context' => $context,
            'noclean' => false,
            'filter'  => true,
        ]);

        $result = $this->clean_for_pdf($formatted);

        if ($charlimit > 0) {
            $ending = '...';
            if ($assignid > 0) {
                $cm = get_coursemodule_from_instance('assign', $assignid);
                if ($cm) {
                    global $CFG;
                    $url = $CFG->wwwroot . '/mod/assign/view.php?id=' . $cm->id;
                    $readmore = get_string('readmoreonline', 'customcertelement_assignfeedbackcomments');
                    $ending = '... <a href="' . $url . '">' . $readmore . '</a>';
                }
            }
            $result = shorten_text($result, $charlimit, false, $ending);
        }

        $cache->set($cachekey, $result);

        return $result;
    }

    /**
     * Strips HTML tags that are unsafe or unsupported by TCPDF.
     *
     * @param string $html HTML produced by format_text().
     * @return string PDF-safe string suitable for TCPDF writeHTMLCell().
     */
    protected function clean_for_pdf(string $html): string {
        $mediapattern = '/<(img|iframe|video|audio|object|embed|canvas|svg)'
            . '(\s[^>]*)?>.*?<\/\1>|<(img|iframe|video|audio|object|embed)'
            . '(\s[^>]*)?\/?>/is';
        $html = preg_replace($mediapattern, '', $html);

        $html = preg_replace('/<script(\s[^>]*)?>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style(\s[^>]*)?>.*?<\/style>/is', '', $html);

        $html = preg_replace('/<\/?(form|input|button|select|textarea)(\s[^>]*)?\/?>/i', '', $html);

        $html = clean_text($html, FORMAT_HTML);

        $html = preg_replace('/(<br\s*\/?>\s*){3,}/i', '<br /><br />', $html);

        return trim($html);
    }

    /**
     * Returns an array of assignment names keyed by assignment ID for a course.
     *
     * @param int $courseid The course ID.
     * @return array Associative array of assignment ID => assignment name.
     */
    protected function get_course_assignments(int $courseid): array {
        global $DB;

        $records = $DB->get_records('assign', ['course' => $courseid], 'name ASC', 'id, name');
        $result  = [];
        foreach ($records as $record) {
            $result[$record->id] = $record->name;
        }

        return $result;
    }
}
