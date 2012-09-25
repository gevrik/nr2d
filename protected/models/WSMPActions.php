<?php
class WSMPActions
{

    public function addbulletCommand($parsed)
    {
        $Server = WebSocket::getInstance();
        $currentBullets = count($Server->wsBullets);
        $Server->wsBullets[$currentBullets] = array(
            'bulletId' => $currentBullets,
            'currentX' => (int)$parsed->xvalue->currentX,
            'currentY' => (int)$parsed->xvalue->currentY,
            'targetX' => (int)$parsed->xvalue->targetX,
            'targetY' => (int)$parsed->xvalue->targetY,
            'userId' => (int)$parsed->xvalue->userId,
            'trajX' => (int)$parsed->xvalue->trajX,
            'trajY' => (int)$parsed->xvalue->trajY,
            'roomId' => (int)$parsed->xvalue->roomId,
            'damage' => (int)$parsed->xvalue->damage,
            'hadImpact' => 0
        );

        $returnCommand = array(
            'xcommand' => 'ADDBULLET',
            'xvalue' => $Server->wsBullets[$currentBullets]
        );

        foreach ( $Server->wsClients as $id => $client ) {
            if (isset($Server->wsUsers[$id]) && $Server->wsUsers[$id]['roomId'] == $Server->wsBullets[$currentBullets]['roomId']) {
                $Server->wsSend($id, json_encode($returnCommand));
            }
        }
    }

    public function damageentityCommand($parsed)
    {
        $Server = WebSocket::getInstance();
        $damagedEntityId = $parsed->xvalue->targetId;
        $damageAmount = (int)$parsed->xvalue->damage;
        $damageDealer = (int)$parsed->xvalue->shooter;
        $damageAmount -= (int)$Server->wsEntities[$damagedEntityId]['defend'];
        if ($damageAmount < 1) {
            $damageAmount = 1;
        }

        if (isset($Server->wsEntities[$damagedEntityId])) {

            $Server->wsEntities[$damagedEntityId]['eeg'] -= $damageAmount;

            if ($Server->wsEntities[$damagedEntityId]['eeg'] <= 0) {
                foreach($Server->wsClients as $id => $client) {
                    if ($Server->wsUsers[$id]['userId'] == $damageDealer) {
                        $Server->wsUsers[$id]['credits'] += $Server->wsEntities[$damagedEntityId]['credits'];
                    }
                }
            }

            $returnCommand = array(
                'xcommand' => 'REDUCEENTEEG',
                'xvalue' => array(
                    'entityId' => $damagedEntityId,
                    'amount' => (int)$damageAmount
                )
            );

            foreach ($Server->wsClients as $id => $client) {
                if (isset($Server->wsUsers[$id]) && $Server->wsUsers[$id]['roomId'] == $Server->wsEntities[$damagedEntityId]['roomId']) {
                    $Server->wsSend($id, json_encode($returnCommand));
                }
            }
        }
    }

    public function deletebulletCommand($parsed)
    {
        $Server = WebSocket::getInstance();
        $returnCommand = array(
            'xcommand' => 'DELETEBULLET',
            'xvalue' => $parsed->xvalue
        );
        foreach ( $Server->wsClients as $id => $client ) {
            $Server->wsSend($id, json_encode($returnCommand));
        }
        unset($Server->wsBullets[$parsed->xvalue]);
    }

    public function removeentityCommand($parsed)
    {
        $Server = WebSocket::getInstance();
        $entityId = $parsed->xvalue;
        //$Server->log($entityId);

        if (isset($Server->wsEntities[$entityId])) {

            $entityObject = Entity::model()->findByPk($entityId);
            $currentRoom = $entityObject->roomId;
            $entityObject->roomId = 0;
            $entityObject->save(false);

            if (isset($Server->wsRooms[$currentRoom])) {
                $Server->wsRooms[$currentRoom]['entityAmount'] -= 1;

                $returnCommand = array(
                    'xcommand' => 'REMOVEENTITY',
                    'xvalue' => $entityId
                );

                foreach ($Server->wsClients as $id => $client) {
                    if ($Server->wsUsers[$id]['roomId'] == $currentRoom)
                    $Server->wsSend($id, json_encode($returnCommand));
                }

                $Server->wsEntities[$entityId]['roomId'] = 0;
            }
        }
    }
}