<?php
define('CLI_SCRIPT', true);
define('MOODLE_INTERNAL', true);
require_once('C:/Users/admin/Downloads/MoodleWindowsInstaller-latest-500/server/moodle/config.php');

// Trigger autoloader for mod_customcert\element
if (class_exists('core\\plugininfo\\mod')) {
    $class = new ReflectionClass('core\\plugininfo\\mod');
    echo "Methods of core\\plugininfo\\mod:\n";
    foreach ($class->getMethods() as $method) {
         echo " - " . $method->getName() . "\n";
    }
} else {
    echo "Class core\\plugininfo\\mod not found\n";
}
