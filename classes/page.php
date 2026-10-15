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

/**
 * Handles the Metrovalencia page.
 *
 * @package    local_metrovalencia
 * @copyright  2026 RaZaC-WP
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page {
    /**
     * Prepare and render the Metrovalencia page.
     *
     * @return void
     */
    public static function render(): void {
        global $OUTPUT, $PAGE;

        try {
            $stations = metrovalencia_api::get_stations();
            $stationoptions = self::build_station_options($stations);
            
            if (empty($stationoptions)) {
                throw new \RuntimeException('No stations available.');
            }

            $defaultstationid = (int) array_key_first($stationoptions);
            
            $stationid = optional_param('stationid', $defaultstationid, PARAM_INT);

            if (!array_key_exists($stationid, $stationoptions)) {
                $stationid = $defaultstationid;
            }

            $data = metrovalencia_api::get_forecasts($stationid);

            $pagedata = new \local_metrovalencia\output\metrovalencia_page(
                $stationoptions,
                $stationid,
                $data['previsiones']
            );

            $renderer = $PAGE->get_renderer('local_metrovalencia');
            echo $renderer->render_metrovalencia_page($pagedata);
        } catch (\Throwable $e) {
            debugging('MetroAPI error: ' . $e->getMessage(), DEBUG_DEVELOPER);

            echo $OUTPUT->notification(
                get_string('error_load', 'local_metrovalencia'),
                \core\output\notification::NOTIFY_ERROR
            );
        }
    }

    /**
     * Build a list of valid station IDs and names, sorted alphabetically.
     *
     * @param array $stations Stations returned by the API.
     * @return array Station names indexed by ID.
     */
    private static function build_station_options(array $stations): array {
        $options = [];

        foreach ($stations as $station) {
            if (!isset($station['id'], $station['nombre'])) {
                continue;
            }

            $options[(int) $station['id']] = $station['nombre'];
        }

        // Locale-aware sort that keeps the IDs as keys and handles accents.
        \core_collator::asort($options);

        return $options;
    }
}
