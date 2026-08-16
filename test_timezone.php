<?php
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";
echo "Timezone: " . date_default_timezone_get() . "<br>";
echo "UTC Time: " . gmdate('Y-m-d H:i:s') . "<br>";
echo "Offset from UTC: " . date('Z') . " seconds (" . (date('Z')/3600) . " hours)<br>";
echo "Server Timezone Setting: " . ini_get('date.timezone') . "<br>";
?>
