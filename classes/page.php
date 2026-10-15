<?php
namespace local_metrovalencia;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles the Metrovalencia page.
 *
 * @package    local_metrovalencia
 * @copyright  RaZaC-WP - 2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page
{

    /**
     * Prepare and render the Metrovalencia page.
     *
     * @return void
     */
    public static function render(): void
    {
        global $OUTPUT, $PAGE;

        try {
            $stations = metrovalencia_api::get_stations();
            $stationoptions = self::build_station_options($stations);

            if (empty($stationoptions)) {
                throw new \RuntimeException('No stations available.');
            }

            $defaultstationid = (int) array_key_first($stationoptions);
            $stationid = optional_param(
                'stationid',
                $defaultstationid,
                PARAM_INT
            );

            if (!array_key_exists($stationid, $stationoptions)) {
                $stationid = $defaultstationid;
            }

            $data = metrovalencia_api::get_forecasts($stationid);
            $forecasts = $data['previsiones'] ?? [];

            $pagedata = new \local_metrovalencia\output\metrovalencia_page(
                $stationoptions,
                $stationid,
                $forecasts
            );

            $renderer = $PAGE->get_renderer('local_metrovalencia');
            echo $renderer->render_metrovalencia_page($pagedata);

        } catch (\Throwable $e) {

            debugging(
                'MetroAPI error: ' . $e->getMessage(),
                DEBUG_DEVELOPER
            );

            echo $OUTPUT->notification(
                'No se han podido cargar las estaciones o los horarios. '
                . 'Inténtalo de nuevo más tarde.',
                \core\output\notification::NOTIFY_ERROR
            );
        }
    }

    /**
     * Build a list of valid station IDs and names.
     *
     * @param array $stations Stations returned by the API.
     * @return array Station options indexed by ID.
     */
    private static function build_station_options(array $stations): array
    {
        $options = [];

        foreach ($stations as $station) {
            if (!isset($station['id'], $station['nombre'])) {
                continue;
            }

            $options[(int) $station['id']] = $station['nombre'];
        }

        asort($options, SORT_NATURAL | SORT_FLAG_CASE);

        return $options;
    }
}