<?php
namespace local_metrovalencia;

defined('MOODLE_INTERNAL') || die();

/**
 * Client for the MetroAPI service.
 *
 * @package    local_metrovalencia
 * @copyright  2026 RaZaC-WP
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metrovalencia_api
{

    /** MetroAPI base URL. */
    private const BASE_URL = 'https://metroapi.alexbadi.es';

    /** HTTP user agent sent to MetroAPI. */
    private const USER_AGENT =
        'moodle-local-metrovalencia/0.1 (Moodle plugin; contact=test@test.com)';

    /**
     * Sends a GET request and decodes the JSON response.
     *
     * @param string $endpoint API endpoint, relative to the base URL.
     * @return array Decoded JSON response.
     * @throws \RuntimeException If the request fails or the response is invalid.
     */
    private static function get_json(string $endpoint): array
    {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');

        $client = new \curl();

        $client->setopt([
            'CURLOPT_USERAGENT' => self::USER_AGENT,
        ]);

        $client->setHeader([
            'Accept: application/json',
        ]);

        $response = $client->get(self::BASE_URL . $endpoint);
        $httpcode = $client->get_info()['http_code'] ?? 0;

        if ($httpcode !== 200) {
            throw new \RuntimeException(
                'MetroAPI request failed. HTTP status: ' . $httpcode
            );
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw new \RuntimeException(
                'MetroAPI returned an invalid JSON response.'
            );
        }

        return $data;
    }

    /**
     * Fetches the list of Metrovalencia stations.
     *
     * @return array Available stations.
     * @throws \RuntimeException If the response does not contain stations.
     */
    public static function get_stations(): array
    {
        $data = self::get_json('/metrodata/paradas');

        if (!isset($data['paradas']) || !is_array($data['paradas'])) {
            throw new \RuntimeException(
                'MetroAPI response does not contain a valid station list.'
            );
        }

        return $data['paradas'];
    }

    /**
     * Fetches parsed forecasts for a Metrovalencia station.
     *
     * @param int $stationid Forecast station ID.
     * @return array Decoded forecast data.
     * @throws \invalid_parameter_exception If the station ID is invalid.
     * @throws \RuntimeException If the response does not contain forecasts.
     */
    public static function get_forecasts(int $stationid): array
    {
        if ($stationid <= 0) {
            throw new \invalid_parameter_exception('Invalid station ID.');
        }

        $data = self::get_json('/prevision/' . $stationid . '/parse');

        if (!isset($data['previsiones']) || !is_array($data['previsiones'])) {
            throw new \RuntimeException(
                'MetroAPI response does not contain valid forecasts.'
            );
        }

        return $data;
    }

    /**
     * Searches for a station using its name or search ID.
     *
     * @param string $query Station name or search ID.
     * @return array Station search result.
     * @throws \invalid_parameter_exception If the query is empty.
     * @throws \RuntimeException If the response does not contain a valid result.
     */
    public static function search_station(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            throw new \invalid_parameter_exception(
                'Station query cannot be empty.'
            );
        }

        $data = self::get_json(
            '/paradas/buscar?q=' . rawurlencode($query)
        );

        if (
            !isset($data['res']) ||
            !is_array($data['res']) ||
            !isset($data['res']['id_prevision'])
        ) {
            throw new \RuntimeException(
                'MetroAPI response does not contain a valid station search result.'
            );
        }

        return $data['res'];
    }
}