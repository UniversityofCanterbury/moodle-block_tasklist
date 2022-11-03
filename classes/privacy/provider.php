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

namespace block_tasklist\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

/**
 * Privacy API implementation for the Task List plugin.
 *
 * @package     block_tasklist
 * @category    privacy
 * @copyright   2022 University of Canterbury
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Describe all the places where the Task List plugin stores some personal data.
     *
     * @param collection $collection Collection of items to add metadata to.
     * @return collection Collection with our added items.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('block_tasklist_items', [
            'userid' => 'privacy:metadata:items:userid',
            'name' => 'privacy:metadata:items:name',
        ], 'privacy:metadata:items');

        return $collection;
    }

    /**
     * Add users who have task list items to provided user list.
     *
     * @param userlist $userlist
     * @return void
     */
    public static function get_users_in_context(userlist $userlist) {
        foreach ($userlist->get_userids() as $userid) {
            $sql = "SELECT userid FROM {block_tasklist_items} WHERE userid = ?";
            $params = [$userid];
            $userlist->add_from_sql('userid', $sql, $params);
        }
    }

    /**
     * Delete user tasklist items.
     *
     * @param approved_userlist $userlist
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        foreach ($userlist->get_userids() as $userid) {
            $DB->delete_records('block_tasklist_items', ['userid' => $userid]);
        }
    }

    /**
     * Get all contexts containing user information for a given user.
     *
     * @param int $userid the id of the user.
     * @return contextlist the list of contexts containing user information.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT ctx.id
                FROM {block_tasklist_items} tli
                JOIN {user} u
                    ON tli.userid = u.id
                JOIN {context} ctx
                    ON ctx.instanceid = u.id
                        AND ctx.contextlevel = :contextlevel
                WHERE tli.userid = :userid";

        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $items = [];
        $results = $DB->get_records('block_tasklist_items', ['userid' => $contextlist->get_user()->id]);
        foreach ($results as $result) {
            $items[] = (object) [
                'name' => $result->name,
                'complete' => \core_privacy\local\request\transform::yesno($result->complete),
            ];
        }
        if (!empty($items)) {
            $data = (object) [
                'items' => $items,
            ];
            \core_privacy\local\request\writer::with_context($contextlist->current())->export_data([
                get_string('pluginname', 'block_tasklist')], $data);
        }
    }

    /**
     * Delete all user data within the given context.
     *
     * @param context $context A context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context instanceof \context_user) {
            $DB->delete_records('block_tasklist_items', ['instanceid' => $context->instanceid]);
        }
    }

    /**
     * Delete all user data for a given user.
     *
     * @param approved_contextlist $contextlist The approved contexts to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $DB->delete_records('block_tasklist_items', ['userid' => $contextlist->get_user()->id]);
    }
}
