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
 * The task that provides a complete restore of block_tasklist is defined here.
 *
 * @package     block_tasklist
 * @category    backup
 * @copyright   2022 University of Canterbury
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'//blocks/tasklist/backup/moodle2/restore_tasklist_stepslib.php');

/**
 * Restore task for block_tasklist.
 */
class restore_tasklist_block_task extends restore_block_task {

    /**
     * Defines particular settings that the block can have.
     */
    protected function define_my_settings() {
    }

    /**
     * Defines particular steps that the block can have.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_tasklist_block_structure_step('tasklist', 'tasklist.xml'));
    }

    /**
     * Returns the fileareas belonging to the block.
     *
     * @return array.
     */
    public function get_fileareas() {
        return array();
    }

    /**
     * Returns the encoded configuration attributes.
     *
     * @return array;
     */
    public function get_configdata_encoded_attributes() {
        return array();
    }

    /**
     * Defines the contents in the block that must be processed by the link decoder.
     *
     * @return array.
     */
    public static function define_decode_contents() {
        $contents = array();

        // Define the contents.

        return $contents;
    }

    /**
     * Defines the decoding rules for links belonging to the block to be executed by the link decoder.
     *
     * @return array.
     */
    public static function define_decode_rules() {
        $rules = array();

        // Define the rules, if any.

        return $rules;
    }
}
