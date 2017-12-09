<?php
class gitHook {

    var $config_filename = 'config.json';
    var $email = 'elmaildegerman@gmail.com';

    public function run($payload) {


        // read config.json
        
        if (!file_exists($this->config_filename)) {
            throw new Exception("Can't find ".$this->config_filename);
        }

        $config = json_decode(file_get_contents($this->config_filename), true);

        $jsonIterator = new RecursiveIteratorIterator( new RecursiveArrayIterator(json_decode($payload, TRUE)), 
        RecursiveIteratorIterator::SELF_FIRST);

        $pusher_email = '';
        $html_url = '';
        $ref = '';
        $commits = array();

        foreach ($jsonIterator as $key => $val) {
            //fwrite($myfile, "$key => $val\n" );

            if($key === 'pusher' && is_array($val)){
                $pusher_email = $val['email'];
                $pusher_name = $val['name'];
            }
            if($key === 'repository' && is_array($val)){
                $html_url = $val["html_url"];
            }
            if($key === 'commits' && is_array($val)){
                //$pusher_email = $val['email'];
                //$pusher_name = $val['name'];
                $commits[] = $val;
            }
            if($key === 'ref'){
                $ref = $val;
            }

        }

        if (isset($config['email'])) {
            $headers = 'From: '.$config['email']['from']."\r\n";
            $headers .= 'CC: ' . $pusher_email . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        }

        foreach ($config['endpoints'] as $endpoint) {
            // check if the push came from the right repository and branch
            if ($html_url == 'https://github.com/' . $endpoint['repo'] && $ref == 'refs/heads/' . $endpoint['branch']) {

                //github commands to run.
                exec("cd /var/www && git reset --hard HEAD");
                exec("cd /var/www && git pull origin master");

                // prepare and send the notification email
                if (isset($config['email'])) {
                    // send mail to someone, and the github user who pushed the commit
                    $body = '<p>The Github user <a href="https://github.com/'
                    . $pusher_name .'">@' . $pusher_name . '</a>'
                    . ' has pushed to ' . $html_url
                    . ' and consequently, ' . $endpoint['action']
                    . '.</p>';
                    $body .= '<p>Here\'s a brief list of what has been changed:</p>';
                    $body .= '<ul>';

                    foreach ($commits as $commit) {

                        $body .= '<li>'.$commit[0]['message'].'<br />';
                        $body .= '<small style="color:#999">added: <b>'.count($commit[0]['added'])
                            .'</b> &nbsp; modified: <b>'.count($commit[0]['modified'])
                            .'</b> &nbsp; removed: <b>'.count($commit[0]['removed'])
                            .'</b> &nbsp; <a href="' . $commit[0]['url']
                            . '">read more</a></small></li>';
                    }
                    $body .= '</ul>';
                    $body .= '<p>Deploy date: ' . $commit[0]['timestamp'] . '</p>';
                    $body .= '<p>Cheers, <br/>Github Webhook Endpoint</p>';
                    $msg = 'tbot has been deployed ' . $commit[0]['id'];
                    //mail($config['email']['to'], $msg, $body, $headers);
                }

                return true;
            }
        }


    }


    public function getEmail() {
        return $this->email;
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
    mail($gitHook->getEmail(), $msg, ''.$e);
}

