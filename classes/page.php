<?php
namespace local_metrovalencia;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles the Metrovalencia page.
 *
 * @package    local_metrovalencia
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page
{

    /**
     * Renders the station selector and train forecasts.
     *
     * @return void
     */
    public static function render(): void
    {
        global $OUTPUT, $PAGE;

        try {
            $stations = metrovalencia_api::get_stations();
            $options = self::build_station_options($stations);

            if (empty($options)) {
                throw new \RuntimeException('No stations available.');
            }

            $defaultstationid = (int) array_key_first($options);
            $stationid = optional_param(
                'stationid',
                $defaultstationid,
                PARAM_INT
            );

            // Only accept station IDs returned by the API.
            if (!array_key_exists($stationid, $options)) {
                $stationid = $defaultstationid;
            }

            self::render_station_selector($options, $stationid);

            echo $OUTPUT->heading($options[$stationid]);
            echo $OUTPUT->heading('Próximos trenes', 3);

            $data = metrovalencia_api::get_forecasts($stationid);
            $trains = $data['previsiones'] ?? [];

            self::render_forecasts($trains);

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
     * Builds the station selector options.
     *
     * @param array $stations Stations returned by MetroAPI.
     * @return array Station IDs mapped to station names.
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

        return $options;
    }

    /**
     * Renders the station selection form.
     *
     * @param array $options Available stations.
     * @param int $stationid Selected station ID.
     * @return void
     */
    private static function render_station_selector(
        array $options,
        int $stationid
    ): void {
        global $PAGE;

        echo \html_writer::start_tag('form', [
            'method' => 'get',
            'action' => $PAGE->url->out(false),
        ]);

        echo \html_writer::label(
            'Selecciona una estación:',
            'stationid'
        );

        echo \html_writer::select(
            $options,
            'stationid',
            $stationid,
            false,
            ['id' => 'stationid']
        );

        echo \html_writer::empty_tag('input', [
            'type' => 'submit',
            'value' => 'Consultar',
            'class' => 'btn btn-primary',
        ]);

        echo \html_writer::end_tag('form');
    }

    /**
     * Renders the train forecasts table.
     *
     * @param array $trains Forecasts returned by MetroAPI.
     * @return void
     */
    private static function render_forecasts(array $trains): void
    {
        global $OUTPUT;

        if (empty($trains)) {
            echo $OUTPUT->notification(
                'No hay previsiones disponibles en este momento.',
                \core\output\notification::NOTIFY_INFO
            );
            return;
        }

        $table = new \html_table();
        $table->head = [
            'Hora',
            'Destino',
            'Línea',
            'Tiempo restante',
        ];
        $table->attributes['class'] = 'generaltable';

        foreach ($trains as $train) {
            $seconds = max(0, (int) ($train['seconds'] ?? 0));

            $minutes = intdiv($seconds, 60);
            $remainingseconds = $seconds % 60;

            $table->data[] = [
                s($train['hora'] ?? '—'),
                s($train['destino'] ?? '—'),
                s((string) ($train['line'] ?? '—')),
                $minutes . ' min ' . $remainingseconds . ' s',
            ];
        }

        echo \html_writer::table($table);
    }
}