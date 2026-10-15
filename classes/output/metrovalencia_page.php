<?php
namespace local_metrovalencia\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Data for the Metrovalencia page.
 *
 * @package    local_metrovalencia
 * @copyright  RaZaC-WP - 2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metrovalencia_page implements \renderable, \templatable
{

    /** @var array Available stations. */
    private array $stations;

    /** @var int Selected station ID. */
    private int $stationid;

    /** @var array Train forecasts. */
    private array $forecasts;

    /**
     * Constructor.
     *
     * @param array $stations Available stations.
     * @param int $stationid Selected station ID.
     * @param array $forecasts Train forecasts.
     */
    public function __construct(
        array $stations,
        int $stationid,
        array $forecasts
    ) {
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
    public function export_for_template(\renderer_base $output): array
    {
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
                'minutes' => intdiv($seconds, 60),
                'seconds' => $seconds % 60,
            ];
        }

        return [
            'stations' => $stationoptions,
            'hasstations' => !empty($stationoptions),
            'forecasts' => $forecasts,
            'hasforecasts' => !empty($forecasts),
            'selectedstation' => $this->get_selected_station_name()
        ];
    }

    private function get_selected_station_name(): string
    {
        foreach ($this->stations as $id => $name) {
            if ((int) $id === $this->stationid) {
                return $name;
            }
        }

        return '';
    }
}