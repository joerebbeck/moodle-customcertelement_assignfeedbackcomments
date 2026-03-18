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
 * Assignment feedback element for mod_customcert.
 *
 * Compatible with:
 * - Moodle 4.1 - 5.1 : legacy element API (render_form_elements etc.)
 * - Moodle 5.2+ : Element System v2 interfaces
 *
 * This file acts as a dispatcher. It splits the implementation to a base class
 * to avoid Fatal Errors when implementing interfaces that do not exist on older
 * mod_customcert versions.
 *
 * @package customcertelement_assignfeedback
 * @copyright 2026 Joe Rebbeck <tjr@the-ela.com>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_assignfeedback;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/element_base.php');

if (interface_exists('\mod_customcert\element\form_buildable_interface')) {
    /**
     * Assignment feedback element class (Element System v2 implementation).
     */
    class element extends element_base implements
        \mod_customcert\element\form_buildable_interface,
        \mod_customcert\element\validatable_element_interface,
        \mod_customcert\element\persistable_element_interface,
        \mod_customcert\element\preparable_form_interface {
    }
} else {
    /**
     * Assignment feedback element class (Legacy API implementation).
     */
    class element extends element_base {
    }
}
