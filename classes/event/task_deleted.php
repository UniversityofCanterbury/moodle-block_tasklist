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

namespace block_tasklist\event;

/**
 * Event for user deleting a task.
 *
 * @package     block_tasklist
 * @copyright   2022 University of Canterbury
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_deleted extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public function get_description() {
        return "The user with id '{$this->userid}' deleted a task '{$this->other['taskname']}' id '{$this->other['taskid']}'";
    }

    public static function get_name() {
        return get_string('event:taskdeleted', 'block_tasklist');
    }

}
