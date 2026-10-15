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
 * Client for the MetroAPI service.
 *
 * @package    local_metrovalencia
 * @copyright  2026 RaZaC-WP
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metrovalencia_api {
    /** @var string MetroAPI base URL. */
    private const BASE_URL = 'https://metroapi.alexbadi.es';

    /** @var int Maximum seconds to wait for the whole request. */
    private const TIMEOUT = 10;

    /** @var int Maximum seconds to wait for the connection to be established. */
    private const CONNECT_TIMEOUT = 3;

    /**
     * Builds the User-Agent sent to MetroAPI.
     *
     * MetroAPI asks clients to identify themselves with a contact e-mail, so
     * the site support address configured in Moodle is used.
     *
     * @return string User agent string.
     */
    private static function get_user_agent(): string {
        $contact = \core_user::get_support_user()->email;

        return 'moodle-local-metrovalencia/0.1 (Moodle plugin; contact=' . $contact . ')';
    }

    /**
     * Sends a GET request and decodes the JSON response.
     *
     * @param string $endpoint API endpoint, relative to the base URL.
     * @return array Decoded JSON response.
     * @throws \RuntimeException If the request fails or the response is invalid.
     */
    private static function get_json(string $endpoint): array {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');

        $client = new \curl();

        $client->setopt([
            'CURLOPT_USERAGENT' => self::get_user_agent(),
        ]);

        $client->setHeader([
            'Accept: application/json',
        ]);

        $response = $client->get(self::BASE_URL . $endpoint, [], [
            'CURLOPT_TIMEOUT' => self::TIMEOUT,
            'CURLOPT_CONNECTTIMEOUT' => self::CONNECT_TIMEOUT,
        ]);

        $errno = $client->get_errno();
        if ($errno !== 0 || !is_string($response)) {
            throw new \RuntimeException(
                'MetroAPI request failed. cURL error: ' . $errno
            );
        }

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
    public static function get_stations(): array {
        $cache = \cache::make('local_metrovalencia', 'stations');

        $stations = $cache->get('list');
        if ($stations !== false) {
            return $stations;
        }

        $data = self::get_json('/metrodata/paradas');

        if (!isset($data['paradas']) || !is_array($data['paradas'])) {
            throw new \RuntimeException(
                'MetroAPI response does not contain a valid station list.'
            );
        }

        $cache->set('list', $data['paradas']);

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
    public static function get_forecasts(int $stationid): array {
        if ($stationid <= 0) {
            throw new \invalid_parameter_exception('Invalid station ID.');
        }

        $cache = \cache::make('local_metrovalencia', 'forecasts');

        $cached = $cache->get($stationid);
        if ($cached !== false) {
            return $cached;
        }

        $data = self::get_json('/prevision/' . $stationid . '/parse');

        if (!isset($data['previsiones']) || !is_array($data['previsiones'])) {
            throw new \RuntimeException(
                'MetroAPI response does not contain valid forecasts.'
            );
        }

        $cache->set($stationid, $data);

        return $data;
    }
}
