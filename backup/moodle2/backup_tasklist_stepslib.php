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
 * Backup steps for block_tasklist are defined here.
 *
 * @package     block_tasklist
 * @category    backup
 * @copyright   2022 University of Canterbury
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_tasklist_block_structure_step extends backup_block_structure_step {

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped in the block tag.
     */
    protected function define_structure() {
        global $DB;

        $items = new backup_nested_element('items', ['instanceid'], null);
        $item = new backup_nested_element('item', array('instanceid'), array('userid', 'instanceid', 'name', 'complete', 'position'));

        $items->add_child($item);
        $items->set_source_array(array((object)array('instanceid' => backup::VAR_BLOCKID)));

        $item->set_source_table('block_tasklist_items', ['instanceid' => backup::VAR_BLOCKID]);

        return $this->prepare_block_structure($items);
    }
}
