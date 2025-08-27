<?php

class PluginGuardianKeyAuth {

    static function checkAccess($username,$login_failed) {

        if(!self::enabled_plugin()) {
            return "DISABLED";
        }

        if((!isset($_SESSION['guardiankey_verified']))) {

            $_SESSION['guardiankey_verified'] = '1';

            require_once __DIR__ . '/guardiankey.class.php';
            $conf = self::loadGKConfig();

            if (!$conf || empty($conf['orgid']) || empty($conf['authgroupid']) || empty($conf['key']) || empty($conf['iv'])) {
                error_log("[GuardianKey] Configuração incompleta. Login não está sendo protegido.");
                return "ERROR";
            }

            try {
                $GKconfig = array(
                    'orgid'       => $conf['orgid'],
                    'authgroupid' => $conf['authgroupid'],
                    'key'         => $conf['key'],
                    'iv'          => $conf['iv'],
                    'agentid'     => $conf['authgroupid'], // manter compatibilidade
                    'service'     => 'GLPI',

                );
                $gkas = new guardiankey($GKconfig);
                $response = $gkas->checkaccess($username, '', $login_failed);
            } catch (Exception $e) {
                return "ERROR";
            }
            
            if (isset($response['response']) && $response['response'] === 'BLOCK') {
                return "BLOCK";
            }else{
                return "ACCEPT";
            }
        }  

    }

    static function enabled_plugin() {
        global $DB;
        $plugin = $DB->request([
            'FROM' => 'glpi_plugins',
            'WHERE' => [
                'directory' => 'guardiankeyauth'
            ],
            'LIMIT' => 1
        ]);

        foreach ($plugin as $row) {
            if ($row['state'] == 1) {
                return true;
            }
        }
        return false;
    }

    static function loadGKConfig() {
        global $DB;
        $res = $DB->request([
            'FROM' => 'glpi_plugin_guardiankeyauth_configs',
            'LIMIT' => 1
        ]);
        $data = [];
        foreach ($res as $linha) {
            $data[] = $linha;
        }
        if (!empty($data)) {
            return $data[0];
        }
        return null;
    }
}
