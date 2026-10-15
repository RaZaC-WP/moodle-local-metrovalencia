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

namespace local_metrovalencia;

use core\hook\navigation\primary_extend;
use core_user\hook\extend_user_menu;
use core_user\output\user_action_menu\link;

/**
 * Hook callbacks for the Metrovalencia local plugin.
 *
 * @package    local_metrovalencia
 * @copyright  2026 RaZaC-WP
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Adds Metrovalencia to the user menu.
     *
     * @param extend_user_menu $hook The user menu hook.
     */
    public static function extend_user_menu(extend_user_menu $hook): void {
        if (!self::can_view()) {
            return;
        }

        $hook->add_menu_item(
            new link(
                new \moodle_url('/local/metrovalencia/index.php'),
                get_string('pluginname', 'local_metrovalencia')
            )
        );
    }

    /**
     * Adds Metrovalencia to the primary navigation.
     *
     * @param primary_extend $hook The primary navigation hook.
     */
    public static function extend_primary_navigation(primary_extend $hook): void {
        if (!self::can_view()) {
            return;
        }

        $hook->get_primaryview()->add(
            get_string('pluginname', 'local_metrovalencia'),
            new \moodle_url('/local/metrovalencia/index.php'),
            \navigation_node::TYPE_CUSTOM,
            null,
            'local_metrovalencia'
        );
    }

    /**
     * Checks whether the current user may see the Metrovalencia entries.
     *
     * @return bool
     */
    private static function can_view(): bool {
        return isloggedin()
            && has_capability('local/metrovalencia:view', \context_system::instance());
    }
}
