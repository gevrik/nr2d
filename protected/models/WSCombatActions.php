<?php
class WSCombatActions
{
	public function damageuserFunction($parsed)
	{
		$Server = WebSocket::getInstance();
		$damagedUserSocketId = $parsed->xvalue->targetId;
        $damageAmount = $parsed->xvalue->damage;
        $damageAmount -= $Server->wsUsers[$damagedUserSocketId]['defend'];

        if ($damageAmount < 1) {
            $damageAmount = 1;
        }

        $returnCommand = array(
            'xcommand' => 'REDUCEEEG',
            'xvalue' => (int)$damageAmount
        );
        $Server->wsUsers[$damagedUserSocketId]['eeg'] -= $damageAmount;
        $userObject = User::model()->findByPk($Server->wsUsers[$damagedUserSocketId]['userId']);
        $userObject->profile->eeg -= $damageAmount;
        $userObject->profile->save(false);

        $Server->wsSend($damagedUserSocketId, json_encode($returnCommand));
	}
}