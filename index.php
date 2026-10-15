<?php
require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();

$PAGE->set_url(new moodle_url('/local/metrovalencia/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_metrovalencia'));
$PAGE->set_heading(get_string('pluginname', 'local_metrovalencia'));

echo $OUTPUT->header();

\local_metrovalencia\page::render();

echo $OUTPUT->footer();
