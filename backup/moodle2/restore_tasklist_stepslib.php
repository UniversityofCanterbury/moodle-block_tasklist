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
 * All the steps to restore block_tasklist are defined here.
 *
 * @package     block_tasklist
 * @category    backup
 * @copyright   2022 University of Canterbury
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_tasklist_block_structure_step extends restore_structure_step {

    /**
     * Defines the structure to be restored.
     */
    protected function define_structure() {
        $paths = array();

        $paths[] = new restore_path_element('block', '/block', true);
        $paths[] = new restore_path_element('items', '/block/items');
        $paths[] = new restore_path_element('item', '/block/items/item');

        return $paths;
    }

    public function process_block($data) {
        global $DB;

        $data = (object) $data;

        if (!$this->task->get_blockid()) {
            return;
        }

        foreach ($data->items[0]['item'] as $item) {
            $item = (object) $item;
            $item->instanceid = $this->task->get_blockid();
            $DB->insert_record('block_tasklist_items', $item);
        }
    }
}
