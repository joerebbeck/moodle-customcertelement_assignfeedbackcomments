<?php
define('CLI_SCRIPT', true);
define('MOODLE_INTERNAL', true);
require_once('C:/Users/admin/Downloads/MoodleWindowsInstaller-latest-500/server/moodle/config.php');

global $DB;

$records = $DB->get_records('customcert_elements', ['element' => 'assignfeedback']);

echo "Total feedback elements: " . count($records) . "\n";
foreach ($records as $record) {
    echo "ID: " . $record->id . " | Data: " . $record->data . "\n";
}
