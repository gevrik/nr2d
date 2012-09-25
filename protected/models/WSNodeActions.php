<?php
class WSNodeActions
{

    public function createnodeCommand($parsed, $clientID)
    {
        $Server = WebSocket::getInstance();
        $direction = $parsed->xvalue->direction;
        $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
        $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

        if ($room->userId == $Server->wsUsers[$clientID]['userId'] && $userObject->profile->credits >= 100) {

            if ($room->getExit($direction) == 0) {

                $userObject->profile->credits -= 100;
                $userObject->profile->save(false);
                $Server->wsUsers[$clientID]['credits'] -= 100;

                // $returnCommand = array(
                //     'xcommand' => 'CREDITSCHANGE',
                //     'xvalue' => 100
                // );
                // $Server->wsSend($clientID, json_encode($returnCommand));

                $targetX = $room->x;
                $targetY = $room->y;

                if ($direction == 'north') {
                    $targetY += 1;
                }
                if ($direction == 'east') {
                    $targetX += 1;
                }
                if ($direction == 'south') {
                    $targetY -= 1;
                }
                if ($direction == 'west') {
                    $targetX -= 1;
                }

                $newRoom = new Room;
                $newRoom->userId = $Server->wsUsers[$clientID]['userId'];
                $newRoom->areaId = $room->areaId;
                $newRoom->created = date( 'Y-m-d H:i:s', time());
                $newRoom->type = 'default';
                $newRoom->level = 1;
                $newRoom->x = $targetX;
                $newRoom->y = $targetY;
                $newRoom->name = 'DEFAULT NODE';
                $newRoom->description = 'A newly created node.';
                $newRoom->save();

                $Server->wsRooms[$newRoom->id] = array('roomId' => $newRoom->id, 'areaId' => $newRoom->areaId, 'userId' => $newRoom->userId, 'type' => $newRoom->type, 'level' => $newRoom->level, 'x' => $newRoom->x, 'y' => $newRoom->y, 'name' => $newRoom->name, 'entityAmount' => $newRoom->entityAmount);

                $returnCommand = array(
                    'xcommand' => 'RECALL',
                    'xvalue' => 0
                );
                $Server->wsSend($clientID, json_encode($returnCommand));

                foreach ( $Server->wsClients as $id => $client ) {
                    if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                        $returnCommand = array(
                            'xcommand' => 'RECALL',
                            'xvalue' => 0
                        );
                        $Server->wsSend($id, json_encode($returnCommand));
                    }
                }

            }
        }
    }

	public function upgradenodeCommand($clientID)
    {
        $Server = WebSocket::getInstance();
        $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
        $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

        $cost = ($room->level * 1000) * $room->level;

        if ($room->type != 'io' && $userObject->profile->credits >= $cost) {

            $userObject->profile->credits -= $cost;
            $userObject->profile->save(false);

            $Server->wsUsers[$clientID]['credits'] -= $cost;

            // $returnCommand = array(
            //     'xcommand' => 'CREDITSCHANGE',
            //     'xvalue' => $cost
            // );
            // $Server->wsSend($clientID, json_encode($returnCommand));

            if ($room->type == 'database') {

                $bonusMB = (($room->level + 1) * ($room->level + 1)) - ($room->level * $room->level);

                $returnCommand = array(
                    'xcommand' => 'RAISEMAXSTORAGE',
                    'xvalue' => $bonusMB
                );
                $Server->wsSend($clientID, json_encode($returnCommand));
                $Server->wsUsers[$clientID]['maxStorage'] += $bonusMB;
            }

            if ($room->type == 'coproc') {
                $bonusMB = (($room->level + 1) * ($room->level + 1)) - ($room->level * $room->level);

                $returnCommand = array(
                    'xcommand' => 'RAISEMAXMEMORY',
                    'xvalue' => $bonusMB
                );
                $Server->wsSend($clientID, json_encode($returnCommand));
                $Server->wsUsers[$clientID]['maxMemory'] += $bonusMB;
            }

            $room->level += 1;
            $room->save(false);

            $returnCommand = array(
                    'xcommand' => 'RECALL',
                    'xvalue' => 0
                );
            $Server->wsSend($clientID, json_encode($returnCommand));

            foreach ( $Server->wsClients as $id => $client ) {
                if ( isset($Server->wsUsers[$id]) && $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                    $Server->wsSend($id, json_encode($returnCommand));
                }
            }

        }
    }

    public function chatsuboCommand($clientID)
    {
        $Server = WebSocket::getInstance();
        $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
        $userObject->profile->location = 1;
        $userObject->profile->save(false);
        $Server->wsUsers[$clientID]['roomId'] = 1;
        $returnCommand = array(
            'xcommand' => 'RECALL',
            'xvalue' => 0
        );
        $Server->wsSend($clientID, json_encode($returnCommand));
        $returnCommand = array(
            'xcommand' => 'SYSMSG',
            'xvalue' => '> Chatsubo connection established'
        );
        $Server->wsSend($clientID, json_encode($returnCommand));
    }

    public function modifynodeCommand($parsed, $clientID)
    {
        $Server = WebSocket::getInstance();
        $newType = $parsed->xvalue->type;
        $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
        $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

        switch ($newType) {

            default:
                $cost = 0;
            break;

            case 'firewall':
                $cost = 750;
            break;

            case 'database':
            case 'terminal':
            case 'coproc':
            case 'coding':
            case 'hacking':
                $cost = 250;
            break;

        }

        if ($room->type != 'io' && $userObject->profile->credits >= $cost) {

            $userObject->profile->credits -= $cost;
            $userObject->profile->save(false);

            $Server->wsUsers[$clientID]['credits'] -= $cost;

            // $returnCommand = array(
            //     'xcommand' => 'CREDITSCHANGE',
            //     'xvalue' => $cost
            // );
            // $Server->wsSend($clientID, json_encode($returnCommand));

            if ($room->type == 'database') {

                $returnCommand = array(
                    'xcommand' => 'REDUCEMAXSTORAGE',
                    'xvalue' => $room->level * $room->level
                );
                $Server->wsSend($clientID, json_encode($returnCommand));

                $Server->wsUsers[$clientID]['maxStorage'] -= ($room->level * $room->level);
                if ($Server->wsUsers[$clientID]['maxStorage'] < 0) {
                    $Server->wsUsers[$clientID]['maxStorage'] = 0;
                }
            }

            if ($room->type == 'coproc') {
                $returnCommand = array(
                    'xcommand' => 'REDUCEMAXMEMORY',
                    'xvalue' => $room->level * $room->level
                );
                $Server->wsSend($clientID, json_encode($returnCommand));
                $Server->wsUsers[$clientID]['maxMemory'] -= $room->level * $room->level;
                if ($Server->wsUsers[$clientID]['maxMemory'] < 0) {
                    $Server->wsUsers[$clientID]['maxMemory'] = 0;
                }
            }

            $room->type = $newType;
            $room->name = $newType . ' node';
            $room->save(false);

            if ($room->type == 'database') {
                $returnCommand = array(
                    'xcommand' => 'RAISEMAXSTORAGE',
                    'xvalue' => $room->level * $room->level
                );
                $Server->wsSend($clientID, json_encode($returnCommand));
                $Server->wsUsers[$clientID]['maxStorage'] += $room->level * $room->level;
            }

            if ($room->type == 'coproc') {
                $returnCommand = array(
                    'xcommand' => 'RAISEMAXMEMORY',
                    'xvalue' => $room->level * $room->level
                );
                $Server->wsSend($clientID, json_encode($returnCommand));
                $Server->wsUsers[$clientID]['maxMemory'] += $room->level * $room->level;
            }

            $Server->wsRooms[$room->id]['type'] = $room->type;
            $Server->wsRooms[$room->id]['name'] = $room->name;

            $returnCommand = array(
                'xcommand' => 'RECALL',
                'xvalue' => 0
            );
            $Server->wsSend($clientID, json_encode($returnCommand));

            foreach ( $Server->wsClients as $id => $client ) {
                if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                    $Server->wsSend($clientID, json_encode($returnCommand));
                }
            }

        }
    }

    public function movetoCommand($parsed, $clientID)
    {
        $Server = WebSocket::getInstance();
        $targetNode = $parsed->xvalue;
        $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
        $profileObject = $userObject->profile;
        $profileObject->location = $targetNode;
        $profileObject->save(false);
        $Server->wsUsers[$clientID]['roomId'] = $targetNode;

        $returnCommand = array(
            'xcommand' => 'RESETPROGRESS',
            'xvalue' => 0
        );
        $Server->wsSend($clientID, json_encode($returnCommand));

        $returnCommand = array(
            'xcommand' => 'RECALL',
            'xvalue' => 0
        );
        $Server->wsSend($clientID, json_encode($returnCommand));
    }

    public function recallCommand($clientID)
    {
        $Server = WebSocket::getInstance();

        $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
        $userObject->profile->location = $Server->wsUsers[$clientID]['homeNode'];
        $userObject->profile->save(false);
        $Server->wsUsers[$clientID]['roomId'] = $Server->wsUsers[$clientID]['homeNode'];
        $returnCommand = array(
            'xcommand' => 'RECALL',
            'xvalue' => 0
        );
        $Server->wsSend($clientID, json_encode($returnCommand));
        $returnCommand = array(
            'xcommand' => 'SYSMSG',
            'xvalue' => '> home system connection established'
        );
        $Server->wsSend($clientID, json_encode($returnCommand));
    }

    public function roomupdateCommand($clientID)
    {
        $Server = WebSocket::getInstance();
        $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
        //$Server->log($room->name);
        $roomOwnerName = ($room->userId == 0) ? 'System' : CHtml::encode($room->user->username);

        $northExit = $room->getExit('north');
        $eastExit = $room->getExit('east');
        $southExit = $room->getExit('south');
        $westExit = $room->getExit('west');

        $returnCommand = array(
            'xcommand' => 'ROOMUPDATE',
            'xvalue' => array(
                'name' => CHtml::encode($room->name),
                'type' => CHtml::encode($room->type),
                'owner' => $roomOwnerName,
                'level' => $room->level,
                'northExit' => $northExit,
                'eastExit' => $eastExit,
                'southExit' => $southExit,
                'westExit' => $westExit,
                'roomId' => $room->id
            )
        );

        $Server->wsSend($clientID, json_encode($returnCommand));
    }
}