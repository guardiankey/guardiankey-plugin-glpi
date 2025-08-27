<?php

function plugin_guardiankeyauth_install() {
    global $DB;

    $query = "CREATE TABLE IF NOT EXISTS `glpi_plugin_guardiankeyauth_configs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `orgid` VARCHAR(64),
        `authgroupid` VARCHAR(64),
        `key` TEXT,
        `iv` TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $DB->query($query);

    // Insere config default (linha única)
    $DB->query("INSERT INTO `glpi_plugin_guardiankeyauth_configs` (`orgid`, `authgroupid`, `key`, `iv`)
                VALUES ('', '', '', '')");

    $file = GLPI_ROOT . "/front/login.php";
    if (is_writable($file)) {

      $content = file_get_contents($file);

      $pre_block = '// GK_BEGIN_BLOCK
      if (isset($_SESSION["namfield"]) && isset($_POST[$_SESSION["namfield"]])) {
         $username = $_POST[$_SESSION["namfield"]];
      } elseif (isset($_POST["login_name"]) ) {
         $username = $_POST["login_name"];
      } else {
         $username = "Could not find login name. Check your GLPI version";
      }
      require_once("../plugins/guardiankeyauth/guardiankeyauth.class.php");
      $GK = new PluginGuardianKeyAuth();
// GK_END_BLOCK';

      $ok_block_begin = '// GK_BEGIN_BLOCK
         $gk_return = $GK->checkAccess($username,"0");
         if ($gk_return == "BLOCK") {
';
      $ok_block_end = '
            exit();
         }
// GK_END_BLOCK';

      $nok_block = '// GK_BEGIN_BLOCK
         $GK->checkAccess($username,"1");
// GK_END_BLOCK';

      $content = preg_replace(
      '/([^\r\n]+\$auth->login\(.*\) {\n)(.*?)} else {(.*?)}/si', 
      $pre_block.'$1'.$ok_block_begin.'$3'.$ok_block_end.'$2} else {'.$nok_block.'$3}', 
      $content
      );

      file_put_contents($file, $content);
    }
    return true;
}

function plugin_guardiankeyauth_uninstall() {
   global $DB;
   $DB->query("DROP TABLE IF EXISTS `glpi_plugin_guardiankeyauth_configs`");

   $file = GLPI_ROOT . "/front/login.php";

   if (is_writable($file) && file_exists($file)) {
      $content = file_get_contents($file);
      $content = preg_replace(
      '|// GK_BEGIN_BLOCK(.*?)// GK_END_BLOCK|si', 
      '', 
      $content
      );
      file_put_contents($file, $content);
   }
    return true;
}

