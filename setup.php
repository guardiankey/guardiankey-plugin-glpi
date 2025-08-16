<?php

function plugin_init_guardiankeyauth() {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['guardiankeyauth'] = true;

    // Hook no login do GLPI
    $PLUGIN_HOOKS['pre_user_login']['guardiankeyauth'] = ['PluginGuardianKeyAuth', 'checkAccess'];

    // Página de configuração (menu do plugin)
    $PLUGIN_HOOKS['config_page']['guardiankeyauth'] = 'front/config.php';
}

function plugin_version_guardiankeyauth() {
    return [
        'name'           => 'GuardianKey AuthSecurity',
        'version'        => '1.0.0',
        'author'         => 'Seu Nome ou Empresa',
        'license'        => 'MIT',
        'homepage'       => 'https://guardiankey.io',
        'minGlpiVersion' => '9.4',
        'maxGlpiVersion' => '10.0'  // Ajuste conforme o seu caso
    ];
}

function plugin_guardiankeyauth_check_config($verbose=false) {
    return true;
}

// Instalação do plugin (tabela de configs)
$PLUGIN_HOOKS['install']['guardiankeyauth'] = 'plugin_guardiankeyauth_install';
$PLUGIN_HOOKS['uninstall']['guardiankeyauth'] = 'plugin_guardiankeyauth_uninstall';


