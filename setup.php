<?php
include_once __DIR__ . '/install.php';
include_once(__DIR__ . "/guardiankeyauth.class.php");


function plugin_init_guardiankeyauth() {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['guardiankeyauth'] = true;


    if (isset($_SESSION['glpiID'])) {


        $GK = new PluginGuardianKeyAuth();
        $GK->checkAccess();

    }


    $PLUGIN_HOOKS['config_page']['guardiankeyauth'] = 'front/config.php';
}

function plugin_version_guardiankeyauth() {
    return [
        'name'           => 'GuardianKey AuthSecurity',
        'version'        => '1.0.0',
        'author'         => 'GuardianKey Cybersecurity',
        'license'        => 'MIT',
        'homepage'       => 'https://guardiankey.io',
        'minGlpiVersion' => '9.4',
        'maxGlpiVersion' => '10.0.19'
    ];
}

function plugin_guardiankeyauth_check_config($verbose=false) {
    return true;
}

$PLUGIN_HOOKS['install']['guardiankeyauth'] = 'plugin_guardiankeyauth_install';
$PLUGIN_HOOKS['uninstall']['guardiankeyauth'] = 'plugin_guardiankeyauth_uninstall';


