<?php

include('../../../inc/includes.php');

global $DB;

if (isset($_POST['update_config'])) {
 

    $DB->update(
        'glpi_plugin_guardiankeyauth_configs',
        [
            'orgid'       => $_POST['orgid'],
            'authgroupid' => $_POST['authgroupid'],
            'key'      => $_POST['key'],
            'iv'          => $_POST['iv'],
        ],
        ['id' => 1]
    );
    Html::redirect($CFG_GLPI['root_doc'] . '/front/plugin.php');
    exit;
}
$csrf_token = '';
if (!empty($_SESSION['glpicsrftokens']) && is_array($_SESSION['glpicsrftokens'])) {
    foreach ($_SESSION['glpicsrftokens'] as $token => $expire) {
        // Só usa tokens que ainda não expiraram
        if ($expire > time()) {
            $csrf_token = $token;
            break;
        }
    }
}

$config = $DB->query("SELECT * FROM `glpi_plugin_guardiankeyauth_configs` LIMIT 1");
$values = mysqli_fetch_assoc($config);

// Formulário
echo "<form method='post' action='config.php' id='form' class='form-class'>";
echo "<table class='tab_cadre' style='margin:auto; width:50%;'>";
echo "<tr><th colspan='2'>GuardianKey Configuration</th></tr>";

echo "<tr><td>Organization ID:</td><td><input type='text' name='orgid' value='" . htmlentities($values['orgid']) . "' style='width:100%'></td></tr>";
echo "<tr><td>AuthGroup ID:</td><td><input type='text' name='authgroupid' value='" . htmlentities($values['authgroupid']) . "' style='width:100%'></td></tr>";
echo "<tr><td>Key:</td><td><input type='text' name='key' value='" . htmlentities($values['key']) . "' style='width:100%'></td></tr>";
echo "<tr><td>IV:</td><td><input type='text' name='iv' value='" . htmlentities($values['iv']) . "' style='width:100%'></td></tr>";

echo "<tr><td colspan='2' class='center'>";
echo Html::submit(__('Save'), ['name' => 'update_config', 'class' => 'submit']);
echo "</td></tr>";
echo "</table>";
echo '        <input type="hidden" name="_glpi_csrf_token" value="'.$csrf_token.'"; ?>';

echo "</form>";
