<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Web service definitions
 *
 * @package     block_tasklist
 * @copyright   2022 University of Canterbury
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_tasklist_get_items' => [
        'classname' => 'block_tasklist\external\get_items',
        'classpath' => '',
        'description' => 'Get list items',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ],
    'block_tasklist_add_item' => [
        'classname' => 'block_tasklist\external\add_item',
        'classpath' => '',
        'description' => 'Add item',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ],
    'block_tasklist_update_items' => [
        'classname' => 'block_tasklist\external\update_items',
        'classpath' => '',
        'description' => 'Update items',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ],
    'block_tasklist_delete_item' => [
        'classname' => 'block_tasklist\external\delete_item',
        'classpath' => '',
        'description' => 'Delete item',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ],
];
