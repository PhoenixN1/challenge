<?php

echo "<h2>$_SERVER</h2>";
$server_keys = ['PHP_SELF','SERVER_NAME','HTTP_HOST','HTTP_REFERER','HTTP_USER_AGENT','SERVER_ADDR','SERVER_PORT','DOCUMENT_ROOT','REQUEST_METHOD','REQUEST_TIME','QUERY_STRING','REQUEST_URI','SCRIPT_FILENAME','SERVER_PROTOCOL'];
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Key</th><th>Value</th></tr>";
foreach ($server_keys as $key) {
    $value = isset($_SERVER[$key]) ? htmlspecialchars($_SERVER[$key]) : '<em>not set</em>';
    echo "<tr><td><code>$key</code></td><td>$value</td></tr>";
}
echo "</table>";

echo "<h2>$_REQUEST</h2>";
echo "<form method='post' action='?name=Ali&city=Casablanca'>";
echo "Name: <input type='text' name='name' value='Ali'> ";
echo "Age: <input type='text' name='age' value='25'> ";
echo "<input type='submit' value='Send'>";
echo "</form>";

echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Key</th><th>Value</th><th>Source</th></tr>";
foreach ($_REQUEST as $key => $value) {
    $source = isset($_GET[$key]) ? 'GET' : (isset($_POST[$key]) ? 'POST' : 'COOKIE');
    echo "<tr><td><code>" . htmlspecialchars($key) . "</code></td>";
    echo "<td>" . htmlspecialchars($value) . "</td>";
    echo "<td>$source</td></tr>";
}
if (empty($_REQUEST)) {
    echo "<tr><td colspan='3'><em>Submit the form to see data</em></td></tr>";
}
echo "</table>";
?>