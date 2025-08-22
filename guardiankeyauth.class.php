<?php

class PluginGuardianKeyAuth {


    static function checkAccess() {


        if((!isset($_SESSION['guardiankey_verified']))) {


                $_SESSION['guardiankey_verified'] = '1';
        
        
                    
                    $username = $_SESSION['glpiname'];
                    $client_ip  = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                    $ua         = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
                    $login_failed = false;

                    require_once __DIR__ . '/guardiankey.class.php';
                    $conf = self::loadGKConfig();
                    

                    if (!$conf || empty($conf['orgid']) || empty($conf['authgroupid']) || empty($conf['key']) || empty($conf['iv'])) {
                        error_log("[GuardianKey] Configuração incompleta. Login não está sendo protegido.");
                        return;
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

                           
                        $login_failed = '0';
                        $response = $gkas->checkaccess($username, '', $login_failed, 'Authentication');
                    } catch (Exception $e) {
                    
                        return;
                    }
                                  

                    if (isset($response['response']) && $response['response'] === 'BLOCK') {
                        Session::addMessageAfterRedirect("Acesso bloqueado por política de risco.", false, ERROR);
                        Html::redirect($CFG_GLPI['root_doc'] . "/index.php"); // força redirecionamento para logout
                    }

                    if (isset($response['response']) && in_array($response['response'], ['NOTIFY', 'HARD-NOTIFY'])) {
                        Session::addMessageAfterRedirect("Aviso de risco: login fora do padrão.", true, WARNING);
                    }
    
        }  

    }
    


    static function sendEvent() {



             
                    foreach ($_POST as $key => $value) {
                        if (strpos($key, 'fielda') === 0) {
                            $username = $value;
                            break;
                        }
                    }
                                            


                    $client_ip  = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                    $ua         = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
                    $login_failed = false;

                    require_once __DIR__ . '/guardiankey.class.php';
                    $conf = self::loadGKConfig();
                    

                    if (!$conf || empty($conf['orgid']) || empty($conf['authgroupid']) || empty($conf['key']) || empty($conf['iv'])) {
                        error_log("[GuardianKey] Configuração incompleta. Login não está sendo protegido.");
                        return;
                    }

                    try {
                        
                                                    file_put_contents(
                        GLPI_ROOT . "/files/_log/guardiankey.log",
                        "[" . date('Y-m-d H:i:s') . "] [GK] debug var conf: " . json_encode($conf) . "\n",
                        FILE_APPEND
                    );
                        $GKconfig = array(
                            'orgid'       => $conf['orgid'],
                            'authgroupid' => $conf['authgroupid'],
                            'key'         => $conf['key'],
                            'iv'          => $conf['iv'],
                            'agentid'     => $conf['authgroupid'], // manter compatibilidade
                            'service'     => 'GLPI',

                        );
                        $gkas = new guardiankey($GKconfig);

                           
                        $login_failed = '1';
                        $response = $gkas->checkaccess($username, '', $login_failed, 'Authentication');
                    } catch (Exception $e) {
                        file_put_contents(
                            GLPI_ROOT . "/files/_log/guardiankey.log",
                            "[" . date('Y-m-d H:i:s') . "] [GK] Exception: " . $e->getMessage() . "\n",
                            FILE_APPEND
                        );
                        return;
                    }
                                    file_put_contents(
                        GLPI_ROOT . "/files/_log/guardiankey.log",
                        "[" . date('Y-m-d H:i:s') . "] [GK] response result: " . json_encode($response) . "\n",
                        FILE_APPEND
                    );
                    // Log no arquivo do plugin
                    file_put_contents(
                        GLPI_ROOT . "/files/_log/guardiankey.log",
                        "[" . date('Y-m-d H:i:s') . "] [GK] Resposta: " . json_encode($response) . "\n",
                        FILE_APPEND
                    );

                    // Mensagens opcionais para o usuário
                    if (isset($response['response']) && $response['response'] === 'BLOCK') {
                        Session::addMessageAfterRedirect("Acesso bloqueado por política de risco.", false, ERROR);
                        Html::redirect($CFG_GLPI['root_doc'] . "/index.php"); // força redirecionamento para logout
                    }

                    if (isset($response['response']) && in_array($response['response'], ['NOTIFY', 'HARD-NOTIFY'])) {
                        Session::addMessageAfterRedirect("Aviso de risco: login fora do padrão.", true, WARNING);
                    }
    
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

        return false;
        
        }
}
