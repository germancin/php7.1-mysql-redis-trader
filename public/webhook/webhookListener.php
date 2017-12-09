<?php
class gitHook {

    var $config_filename = 'config.json';
    var $email = 'elmaildegerman@gmail.com';

    public function run($payload) {

        $tt = var_dump($payload);
        error_log($tt);

        // read config.json
        exec("cd /var/www && git reset --hard HEAD");
        exec("git pull origin master");



    }

}

$gitHook = new gitHook();

try {
    if (isset($_POST['payload'])) {
        $gitHook->run($_POST['payload']);
    }
} catch ( Exception $e ) {
    // script notifications will be send to this email:
    $msg = $e->getMessage();
}

