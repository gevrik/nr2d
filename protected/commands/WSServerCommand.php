<?php

class WSServerCommand extends CConsoleCommand
{
    public function actionIndex()
    {

        set_time_limit(0);
        $Server = WebSocket::getInstance();

        $Server->bind('message', array('WSChatAction', 'wsOnMessage'));
        $Server->bind('open', array('WSChatAction', 'wsOnOpen'));
        $Server->bind('close', array('WSChatAction', 'wsOnClose'));
        $Server->wsStartServer('totalmadownage.de', 9300);

    }
}
