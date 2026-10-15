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

namespace local_metrovalencia\output;

/**
 * Renderer for the Metrovalencia page.
 *
 * @package    local_metrovalencia
 * @copyright  2026 RaZaC-WP
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {
    /**
     * Render the Metrovalencia page.
     *
     * @param metrovalencia_page $page Page data.
     * @return string HTML output.
     */
    public function render_metrovalencia_page(metrovalencia_page $page): string {
        return $this->render_from_template(
            'local_metrovalencia/metrovalencia_page',
            $page->export_for_template($this)
        );
    }
}
