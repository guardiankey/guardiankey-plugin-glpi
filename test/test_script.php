<?php
    $file = "login.php";


   // Verify if exists GK_BEGIN_BLOCK in file, to avoid duplicate insertions
   $content_check = file_get_contents($file);
   if (strpos($content_check, '// GK_BEGIN_BLOCK') !== false) {
      return true;
   }

    if (is_writable($file)) {

      // Create a backup of the original file, with date and time and extension .php to avoid execution
      copy($file, $file . '.bak_' . date('Ymd_His') . '.php');

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
// GK_END_BLOCK
';

      $ok_block_begin = '// GK_BEGIN_BLOCK
         $gk_return = $GK->checkAccess($username,"0");
         if ($gk_return == "BLOCK") {
';
      $ok_block_end = '
            exit();
         }
// GK_END_BLOCK
';

      $nok_block = '
// GK_BEGIN_BLOCK
         $GK->checkAccess($username,"1");
// GK_END_BLOCK';

      $content = preg_replace(
      '/([^\r\n]+\$auth->login\(.*\) {\n)(.*?)} else {(.*?)}/si', 
      $pre_block.'$1'.$ok_block_begin.'$3'.$ok_block_end.'$2} else {'.$nok_block.'$3}', 
      $content
      );

      file_put_contents($file, $content);
    }

