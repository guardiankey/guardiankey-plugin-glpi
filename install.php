<?php

function plugin_guardiankeyauth_install() {
    global $DB;

    $query = "CREATE TABLE IF NOT EXISTS `glpi_plugin_guardiankeyauth_configs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `orgid` VARCHAR(64),
        `authgroupid` VARCHAR(64),
        `keyval` TEXT,
        `iv` TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $DB->query($query);

    // Insere config default (linha única)
    $DB->query("INSERT INTO `glpi_plugin_guardiankeyauth_configs` (`orgid`, `authgroupid`, `keyval`, `iv`)
                VALUES ('', '', '', '')");

    return true;
}

function plugin_guardiankeyauth_uninstall() {
    global $DB;
    $DB->query("DROP TABLE IF EXISTS `glpi_plugin_guardiankeyauth_configs`");
    return true;
}

