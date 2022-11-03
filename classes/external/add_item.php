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

namespace block_tasklist\external;

use block_tasklist\event\task_created;
use external_api;
use external_description;
use external_function_parameters;
use external_single_structure;
use external_value;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External function 'block_tasklist_add_item' implementation.
 *
 * @package     block_tasklist
 * @category    external
 * @copyright   2022 University of Canterbury
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_item extends external_api {

    /**
     * Describes parameters of the {@see self::execute()} method.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'instanceid' => new external_value(PARAM_INT, 'Block instance ID'),
            'name' => new external_value(PARAM_TEXT, 'Name of item'),
            'complete' => new external_value(PARAM_BOOL, 'Is item complete'),
            'position' => new external_value(PARAM_INT, 'Item position'),
        ]);
    }

    /**
     * Add item to the task list
     *
     * @param int $instanceid
     * @param string $name
     * @param bool $complete
     * @param int $position
     */
    public static function execute(int $instanceid, string $name, bool $complete, int $position) {
        global $USER, $DB;

        // Re-validate parameters in rare case this method was called directly.
        [
            'instanceid' => $instanceid,
            'name' => $name,
            'complete' => $complete,
            'position' => $position,
        ] = self::validate_parameters(self::execute_parameters(), [
            'instanceid' => $instanceid,
            'name' => $name,
            'complete' => $complete,
            'position' => $position,
        ]);

        $context = \context_block::instance($instanceid);
        self::validate_context($context);

        $record = new \stdClass();
        $record->instanceid = $instanceid;
        $record->userid = $USER->id;
        $record->name = $name;
        $record->complete = $complete ? 1 : 0;
        $record->position = $position;

        $record->id = $DB->insert_record('block_tasklist_items', $record);

        task_created::create([
            'context' => $context,
            'other' => [
                'taskid' => $record->id,
                'taskname' => $record->name,
            ]
        ])->trigger();

        return $record;
    }

    /**
     * Describes the return value of the {@see self::execute()} method.
     *
     * @return external_description
     */
    public static function execute_returns(): external_description {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Unique identifier'),
            'userid' => new external_value(PARAM_INT, 'User ID'),
            'instanceid' => new external_value(PARAM_INT, 'Block instance ID'),
            'name' => new external_value(PARAM_TEXT, 'Item name'),
            'complete' => new external_value(PARAM_INT, 'Is item complete'),
            'position' => new external_value(PARAM_INT, 'Item position'),
        ]);
    }
}
