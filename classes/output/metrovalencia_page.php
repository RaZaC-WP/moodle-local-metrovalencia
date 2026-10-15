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
 * Data for the Metrovalencia page.
 *
 * @package    local_metrovalencia
 * @copyright  2026 RaZaC-WP
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metrovalencia_page implements \renderable, \templatable {
    /** @var array Available stations, names indexed by ID. */
    private array $stations;

    /** @var int Selected station ID. */
    private int $stationid;

    /** @var array Train forecasts. */
    private array $forecasts;

    /**
     * Constructor.
     *
     * @param array $stations Available stations, names indexed by ID.
     * @param int $stationid Selected station ID.
     * @param array $forecasts Train forecasts.
     */
    public function __construct(array $stations, int $stationid, array $forecasts) {
        $this->stations = $stations;
        $this->stationid = $stationid;
        $this->forecasts = $forecasts;
    }

    /**
     * Export data for the Mustache template.
     *
     * @param \renderer_base $output Renderer instance.
     * @return array Template context.
     */
    public function export_for_template(\renderer_base $output): array {
        $stationoptions = [];

        foreach ($this->stations as $id => $name) {
            $stationoptions[] = [
                'id' => (int) $id,
                'name' => $name,
                'selected' => (int) $id === $this->stationid,
            ];
        }

        $forecasts = [];

        foreach ($this->forecasts as $forecast) {
            $seconds = max(0, (int) ($forecast['seconds'] ?? 0));

            $forecasts[] = [
                'time' => $forecast['hora'] ?? '—',
                'destination' => $forecast['destino'] ?? '—',
                'line' => (string) ($forecast['line'] ?? '—'),
                'remaining' => get_string('remaining_format', 'local_metrovalencia', [
                    'minutes' => intdiv($seconds, 60),
                    'seconds' => $seconds % 60,
                ]),
            ];
        }

        return [
            'stations' => $stationoptions,
            'hasstations' => !empty($stationoptions),
            'forecasts' => $forecasts,
            'hasforecasts' => !empty($forecasts),
            'selectedstation' => $this->get_selected_station_name(),
        ];
    }

    /**
     * Returns the name of the selected station.
     *
     * @return string Station name, or an empty string if it is not found.
     */
    private function get_selected_station_name(): string {
        return $this->stations[$this->stationid] ?? '';
    }
}
