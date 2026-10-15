
<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Main page for the Metrovalencia local plugin.
 *
 * @package    local_metrovalencia
 * @copyright  2026 RaZaC-WP
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();

$PAGE->set_url(new moodle_url('/local/metrovalencia/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_metrovalencia'));
$PAGE->set_heading(get_string('pluginname', 'local_metrovalencia'));

echo $OUTPUT->header();

echo $OUTPUT->heading('Consulta de horarios');

echo $OUTPUT->notification(
    'La aplicación de horarios de Metrovalencia está en desarrollo.',
    \core\output\notification::NOTIFY_INFO
);

echo $OUTPUT->footer();
