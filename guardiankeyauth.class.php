<?php

class PluginGuardianKeyAuth {

    /**
     * Hook chamado antes do login do usuário no GLPI
     */
    static function checkAccess($params) {
        global $DB;

        $username = $params['username'];
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $login_failed = false;

        require_once __DIR__ . '/guardiankey.class.php';

        $conf = self::loadGKConfig();

        if (!$conf || empty($conf['orgid']) || empty($conf['authgroupid']) || empty($conf['keyval']) || empty($conf['iv'])) {
            Session::addMessageAfterRedirect("GuardianKey: Configuração incompleta. Login não está sendo protegido.", false, WARNING);
            return true; // continua mesmo sem config
        }

        $gkas = new GuardianKey(
            $conf['orgid'],
            $conf['authgroupid'],
            $conf['keyval'],
            $conf['iv']
        );

        $response = $gkas->checkAccess([
            'username'      => $username,
            'ip'            => $client_ip,
            'useragent'     => $ua,
            'login_failed'  => $login_failed
        ]);

        if (isset($response['response']) && $response['response'] === 'BLOCK') {
            Session::addMessageAfterRedirect("Acesso bloqueado por política de risco.", false, ERROR);
            return false;
        }

        if (isset($response['response']) && in_array($response['response'], ['NOTIFY', 'HARD-NOTIFY'])) {
            Session::addMessageAfterRedirect("Aviso de risco: login fora do padrão.", true, WARNING);
        }

        // Se for ACCEPT ou ausência de resposta, continua
        return true;
    }

    /**
     * Lê as configurações do plugin salvas no banco de dados
     */
    private static function loadGKConfig() {
        global $DB;

        $res = $DB->query("SELECT * FROM `glpi_plugin_guardiankeyauth_configs` LIMIT 1");
        if ($res && $DB->numrows($res) > 0) {
            return $DB->fetch_assoc($res);
        }
        return false;
    }
}

