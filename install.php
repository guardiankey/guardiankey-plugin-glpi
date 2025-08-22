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
        $content = file($file); // lê arquivo em array de linhas
        $newcontent = [];
        $inserted = false;

        foreach ($content as $line) {
            $newcontent[] = $line;

            if (strpos($line, 'http_response_code(401);') !== false && !$inserted) {
                $newcontent[] = "        require_once('../plugins/guardiankeyauth/guardiankeyauth.class.php');\n";
                $newcontent[] = "        \$GK = new PluginGuardianKeyAuth();\n";
                $newcontent[] = "        \$GK->sendEvent();\n";
                $inserted = true;
            }
        }

        if ($inserted) {
            file_put_contents($file, implode('', $newcontent));
        }
    }



    return true;
}

function plugin_guardiankeyauth_uninstall() {
    global $DB;
    $DB->query("DROP TABLE IF EXISTS `glpi_plugin_guardiankeyauth_configs`");

   $file = GLPI_ROOT . "/front/login.php";

   if (is_writable($file) && file_exists($file)) {
      $lines = file($file);
      $newlines = [];

      foreach ($lines as $line) {
         $trimmed = ltrim($line);

         if (strpos($trimmed, 'require_once') === 0 && strpos($trimmed, '../plugins/guardiankeyauth') !== false) {
            if (strpos($trimmed, '//') !== 0) {
               $line = "//" . $line;
            }
         }

         if (strpos($trimmed, '$GK =') === 0) {
            if (strpos($trimmed, '//') !== 0) {
               $line = "//" . $line;
            }
         }

         if (strpos($trimmed, '$GK->sendEvent') === 0) {
            if (strpos($trimmed, '//') !== 0) {
               $line = "//" . $line;
            }
         }

         $newlines[] = $line;
      }

      // salva de volta
      file_put_contents($file, implode('', $newlines));
   }

    return true;
}

