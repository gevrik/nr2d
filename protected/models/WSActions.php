<?php
class WSActions
{
	public function chatsubo($userObject)
	{
		$Server = WebSocket::getInstance();

		//$userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
        $userObject->profile->location = 1;
        $userObject->profile->save(false);
        $Server->wsUsers[$clientID]['roomId'] = 1;
        $Server->wsSend($clientID, "RECALL");
        $Server->wsSend($clientID, "SYSMSG You have connected to The Chatsubo.");

        return;

    }
}