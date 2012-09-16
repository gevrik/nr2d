<?php

class WSCronCommand extends CConsoleCommand
{
    public function actionIndex()
    {

        set_time_limit(0);
        $Server = WebSocket::getInstance();

        $now = time();

        while ($now) {
        	if (time() >= $now + 10) {
        		$Server->log('tick! ' . count($Server->wsClients));
        		$now = time();
        	}
        }

    }
}
