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
 * The task that provides all the steps to perform a complete backup is defined here.
 *
 * @package     block_tasklist
 * @category    backup
 * @copyright   2022 University of Canterbury
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'//blocks/tasklist/backup/moodle2/backup_tasklist_stepslib.php');

/**
 * Provides all the settings and steps to perform a complete backup of block_tasklist.
 */
class backup_tasklist_block_task extends backup_block_task {

    /**
     * Defines particular settings for the plugin.
     */
    protected function define_my_settings() {
    }

    /**
     * Defines particular steps for the backup process.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_tasklist_block_structure_step('tasklist', 'tasklist.xml'));
    }

    /**
     * Returns the array of file area names within the block context.
     *
     * @return string[] File area names.
     */
    public function get_fileareas() {
        return array();
    }

    /**
     * Returns the config elements that must be processed before they are stored for backup.
     *
     * @return string[] Config elements.
     */
    public function get_configdata_encoded_attributes() {
        return array();
    }

    /**
     * Codes the transformations to perform in the block in order to get transportable (encoded) links.
     *
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {
        return $content;
    }
}
