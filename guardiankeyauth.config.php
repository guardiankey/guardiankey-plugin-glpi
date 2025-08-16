<?php

include ('../../../inc/includes.php');

$config = $DB->query("SELECT * FROM `glpi_plugin_guardiankeyauth_configs` LIMIT 1");
$values = $DB->fetch_assoc($config);

echo "<form method='post' action=''>";
echo "<table class='tab_cadre'>";

echo "<tr><th colspan='2'>Configuração do GuardianKey</th></tr>";

echo "<tr><td>Organization ID:</td><td><input type='text' name='orgid' value='".htmlentities($values['orgid'])."'></td></tr>";
echo "<tr><td>Auth Group ID:</td><td><input type='text' name='authgroupid' value='".htmlentities($values['authgroupid'])."'></td></tr>";
echo "<tr><td>Key:</td><td><input type='text' name='keyval' value='".htmlentities($values['keyval'])."'></td></tr>";
echo "<tr><td>IV:</td><td><input type='text' name='iv' value='".htmlentities($values['iv'])."'></td></tr>";

echo "<tr><td colspan='2' class='center'><input type='submit' name='update_config' value='Salvar'></td></tr>";
echo "</table>";
echo "</form>";

if (isset($_POST['update_config'])) {
    $stmt = $DB->prepare("UPDATE `glpi_plugin_guardiankeyauth_configs` SET
        `orgid` = ?, `authgroupid` = ?, `keyval` = ?, `iv` = ?
        WHERE `id` = 1");

    $stmt->execute([
        $_POST['orgid'], $_POST['authgroupid'], $_POST['keyval'], $_POST['iv']
    ]);

    Html::back();
}
