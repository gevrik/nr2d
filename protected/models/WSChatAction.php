<?php
class WSChatAction
{

    // when a client sends data to the server
    static function wsOnMessage($clientID, $message, $messageLength, $binary) {
        date_default_timezone_set('Europe/Berlin');
        $Server = WebSocket::getInstance();
        $ip = long2ip( $Server->wsClients[$clientID][6] );

        // check if message length is 0
        if ($messageLength == 0) {
            $Server->wsClose($clientID);
            return;
        }

        //$Server->log($message);

        $message = explode(' ', $message);
        $command = array_shift($message);

        if ($command == 'INITP') {

            $userObject = User::model()->findByPk($message[0]);
            $stealthBonus = 0;
            $attackBonus = 0;
            $detectBonus = 0;
            $defendBonus = 0;

            $maxMemory = 0;
            $maxStorage = 0;

            foreach ($userObject->rooms as $room) {
                if ($room->type == 'database') {
                    $maxStorage += $room->level;
                }
                if ($room->type == 'coproc') {
                    $maxMemory += $room->level;
                }
            }

            $Server->wsUsers[$clientID] = array('userId' => $userObject->id, 'roomId' => $userObject->profile->location, 'x' => 256, 'y' => 256, 'homeNode' => $userObject->profile->homenode, 'credits' => $userObject->profile->credits, 'secrating' => $userObject->profile->secrating, 'stealth' => $userObject->profile->stealth, 'detect' => $userObject->profile->detect, 'attack' => $userObject->profile->attack, 'defend' => $userObject->profile->defend, 'coding' => $userObject->profile->coding, 'snippets' => $userObject->profile->snippets, 'eeg' => $userObject->profile->eeg, 'willpower' => $userObject->profile->willpower, 'stealthBonus' => $stealthBonus, 'attackBonus' => $attackBonus, 'maxStorage' => $maxStorage, 'maxMemory' => $maxMemory, 'detectBonus' => $detectBonus, 'defendBonus' => $defendBonus, 'decking' => $userObject->profile->decking);

            $Server->wsSend($clientID, "INITP " . $userObject->profile->location . " " . $userObject->profile->homenode . " " . $userObject->profile->speed . " " . $clientID . " " . $userObject->profile->credits . " " . $userObject->profile->secrating . " " . $userObject->profile->stealth . " " . $userObject->profile->detect . " " . $userObject->profile->attack . " ". $userObject->profile->defend . " " . $userObject->profile->coding . " " . $userObject->profile->snippets . " " . $userObject->profile->eeg . " " . $userObject->profile->willpower . " " . $stealthBonus . " " . $attackBonus . " " . $maxStorage . " " . $maxMemory . " " . $detectBonus . " " . $defendBonus . " " . $userObject->profile->decking);

            foreach ( $Server->wsClients as $id => $client ) {
                if (isset($Server->wsUsers[$id])) {
                    if ( $id != $clientID ) {
                        $Server->wsSend($clientID, "OTHERUSER " . $id . " " . $Server->wsUsers[$id]['x'] . " " . $Server->wsUsers[$id]['y'] . " " . $Server->wsUsers[$id]['roomId']);
                        $Server->wsSend($id, "OTHERUSER " . $clientID . " " . $Server->wsUsers[$clientID]['x'] . " " . $Server->wsUsers[$clientID]['y'] . " " . $Server->wsUsers[$clientID]['roomId']);
                    }
                }
            }

            foreach ( $Server->wsPrograms as $programId => $programObject ) {
                if ($Server->wsPrograms[$programId]['userId'] == $Server->wsUsers[$clientID]['userId']) {
                    $Server->wsSend($clientID, 'ADDTOSTORAGE ' . $Server->wsPrograms[$programId]['programId'] . ' ' . $Server->wsPrograms[$programId]['userId'] . ' ' . $Server->wsPrograms[$programId]['type'] . ' ' . $Server->wsPrograms[$programId]['rating'] . ' ' . $Server->wsPrograms[$programId]['condition'] . ' ' . $Server->wsPrograms[$programId]['maxUpgrades'] . ' ' . $Server->wsPrograms[$programId]['upgrades'] . ' ' . $Server->wsPrograms[$programId]['name']);
                }
            }

           

        }

        else if ($command == 'ATTACKVIRUS') {
            $targetVirusId = array_shift($message);
            $virusDamage = array_shift($message);
            $pAttack = $Server->wsUsers[$clientID]['attack'] + $Server->wsUsers[$clientID]['attackBonus'];
            $vDefend = $Server->wsEntities[$targetVirusId]['defend'];
            srand();
            $skillRoll = rand(2, 20) + $pAttack - $vDefend;
            if ($skillRoll >= 11) {

                foreach ($Server->wsClients as $recipId => $recipClient) {
                    if ($Server->wsUsers[$recipId]['roomId'] ==  $Server->wsEntities[$targetVirusId]['roomId']) {
                        $Server->wsSend($recipId, 'CREATEBULLET ' . round($Server->wsUsers[$clientID]['x']) . ' ' . round($Server->wsUsers[$clientID]['y']) . ' ' . round($Server->wsEntities[$targetVirusId]['x']) . ' ' . round($Server->wsEntities[$targetVirusId]['y']));
                    }
                }

                $bonusDamage = 0;

                if ($skillRoll > 20) {
                    $bonusDamage = $skillRoll - 20;
                }

                $Server->wsEntities[$targetVirusId]['eeg'] -= ($virusDamage + $bonusDamage);

                if ($Server->wsEntities[$targetVirusId]['eeg'] < 1) {
                    //virus flatlined
                    $Server->wsSend($clientID, 'SYSMSG > virus flatlined');
                    $Server->wsSend($clientID, 'FLATLINEVIRUS ' . $targetVirusId);
                    $Server->wsSend($clientID, "RECALL");
                    unset($Server->wsEntities[$targetVirusId]);
                    foreach ( $Server->wsClients as $id => $client ) {
                        if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                            $Server->wsSend($id, 'FLATLINEVIRUS ' . $targetVirusId);
                            $Server->wsSend($id, "RECALL");
                        }
                    }
                    $entityObject = Entity::model()->findByPk($targetVirusId);
                    $entityObject->delete(false);
                }
                else {

                    $Server->wsSend($clientID, 'SYSMSG > virus hit');
                    $Server->wsSend($clientID, 'DAMAGEVIRUS ' . $targetVirusId . ' ' . ($virusDamage + $bonusDamage));

                    foreach ( $Server->wsClients as $id => $client ) {
                        if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                            $Server->wsSend($id, "RECALL");
                        }
                    }
                }

            }
            else {
                $Server->wsSend($clientID, 'SYSMSG > virus missed');   
            }
        }

        else if ($command == 'CHAT') {
            $chatterName = array_shift($message);
            $chatMessage = implode(' ', $message);

            foreach ( $Server->wsClients as $id => $client ) {
                $Server->wsSend($id, "CHAT " . $chatterName . " " . $chatMessage);
            }
        }

        else if ($command == 'CHATSUBO') {
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
            $userObject->profile->location = 1;
            $userObject->profile->save(false);
            $Server->wsUsers[$clientID]['roomId'] = 1;
            $Server->wsSend($clientID, "RECALL");
            $Server->wsSend($clientID, "SYSMSG You have connected to The Chatsubo.");
            //call_user_func_array(array("WSActions", "chatsubo"), array($userObject, $Server));
            //WSActions::chatsubo($userObject);
        }

        else if ($command == 'CREATENODE') {
            $direction = array_shift($message);
            $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

            if ($room->userId == $Server->wsUsers[$clientID]['userId'] && $userObject->profile->credits >= 100) {

                if ($room->getExit($direction) == 0) {

                    $userObject->profile->credits -= 100;
                    $userObject->profile->save(false);
                    $Server->wsSend($clientID, "CREDITSCHANGE " . 100);

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

                    $Server->wsSend($clientID, "RECALL");

                    foreach ( $Server->wsClients as $id => $client ) {
                        if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                            $Server->wsSend($id, "RECALL");
                        }
                    }

                }
            }

        }

        else if ($command == 'CREATEPROGRAM') {
            $programType = array_shift($message);
            $Server->log($programType);
            $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

            switch($programType) {

                case 'stealth':
                case 'detect':
                case 'defend':
                case 'attack':
                case 'eegbooster':
                case 'scanner':
                    $creditCost = 100;
                    $snippetCost = 10;
                break;

                case 'antivirus':
                    $creditCost = 250;
                    $snippetCost = 25;
                break;

                default:
                    $creditCost = 100;
                    $snippetCost = 10;
                break;

            }

            if ($room->type == 'coding' && $userObject->profile->credits >= $creditCost && $userObject->profile->snippets >= $snippetCost) {

                    $userObject->profile->credits -= $creditCost;
                    $userObject->profile->snippets -= $snippetCost;
                    $userObject->profile->save(false);
                    $Server->wsSend($clientID, "CREDITSCHANGE " . $creditCost);
                    $Server->wsSend($clientID, "SNIPPETSCHANGE " . $snippetCost);
                    $Server->wsUsers[$clientID]['credits'] -= $creditCost;
                    $Server->wsUsers[$clientID]['snippets'] -= $snippetCost;

                    $newModel = new Program;
                    $newModel->coderId = $Server->wsUsers[$clientID]['userId'];
                    $newModel->userId = $Server->wsUsers[$clientID]['userId'];
                    $newModel->type = $programType;
                    $newModel->created = date( 'Y-m-d H:i:s', time());
                    $newModel->rating = 1;
                    $newModel->condition = 100;
                    $newModel->maxUpgrades = $Server->wsUsers[$clientID]['coding'];
                    $newModel->upgrades = 0;

                    if ($programType == 'stealth') {
                        $newModel->name = 'stealth v1.0';
                        $newModel->description = 'This program boosts your stealth rating.';
                    }

                    if ($programType == 'attack') {
                        $newModel->name = 'attack v1.0';
                        $newModel->description = 'This program boosts your attack rating.';
                    }

                    if ($programType == 'antivirus') {
                        $newModel->name = 'antivirus v1.0';
                        $newModel->description = 'This program can attack virii.';
                    }

                    if ($programType == 'detect') {
                        $newModel->name = 'detect v1.0';
                        $newModel->description = 'This program boosts your detect rating.';
                    }

                    if ($programType == 'defend') {
                        $newModel->name = 'defend v1.0';
                        $newModel->description = 'This program boosts your defend rating.';
                    }

                    if ($programType == 'eegbooster') {
                        $newModel->condition = 10;
                        $newModel->name = 'eeg booster';
                        $newModel->description = 'This program boosts your EEG value.';
                    }

                    if ($programType == 'scanner') {
                        $newModel->name = 'scanner v1.0';
                        $newModel->description = 'This program allows you to scan the node.';
                    }

                    $newModel->save();

                    $Server->wsPrograms[$newModel->id] = array('programId' => $newModel->id, 'userId' => $newModel->userId, 'type' => $newModel->type, 'rating' => $newModel->rating, 'condition' => $newModel->condition, 'maxUpgrades' => $newModel->maxUpgrades, 'upgrades' => $newModel->upgrades, 'name' => CHtml::encode($newModel->name));

                     $Server->wsSend($clientID, 'ADDTOSTORAGE ' . $Server->wsPrograms[$newModel->id]['programId'] . ' ' . $Server->wsPrograms[$newModel->id]['userId'] . ' ' . $Server->wsPrograms[$newModel->id]['type'] . ' ' . $Server->wsPrograms[$newModel->id]['rating'] . ' ' . $Server->wsPrograms[$newModel->id]['condition'] . ' ' . $Server->wsPrograms[$newModel->id]['maxUpgrades'] . ' ' . $Server->wsPrograms[$newModel->id]['upgrades'] . ' ' . $Server->wsPrograms[$newModel->id]['name']);

                    $Server->wsSend($clientID, 'SYSMSG > coding complete');

            }

        }

        else if ($command == 'EXECUTEPROGRAM') {
            $executeProgId = array_shift($message);

            if ($Server->wsPrograms[$executeProgId]) {

                if ($Server->wsPrograms[$executeProgId]['condition'] > 0) {

                    $Server->wsPrograms[$executeProgId]['condition'] -= 1;
                    if ($Server->wsPrograms[$executeProgId]['condition'] < 0) {
                        $Server->wsPrograms[$executeProgId]['condition'] = 0;
                    }
                    $Server->wsSend($clientID, 'REDUCEPROGCOND ' . $executeProgId);

                    $programObject = Program::model()->findByPk($executeProgId);
                    $programObject->condition -= 1;
                    $programObject->save(false);

                    $skillMod = $Server->wsUsers[$clientID]['decking'];
                    srand();

                    $skillRoll = rand(2, 20) + $skillMod;
                    $critSuccessMargin = 0;

                    if ($skillRoll > 20) {
                        $critSuccessMargin = $skillRoll - 20;
                    }

                    if ($skillRoll >= 11) {

                        if ($Server->wsPrograms[$executeProgId]['type'] == 'eegbooster') {

                            $exeProgRating = $Server->wsPrograms[$executeProgId]['rating'];
                            $healRating = ($exeProgRating * rand(2, 20)) + $critSuccessMargin;

                            $Server->wsSend($clientID, 'RAISEEEG ' . $healRating);
                            $Server->wsSend($clientID, 'SYSMSG > booster restores EEG by: ' . $healRating);

                            $Server->wsUsers[$clientID]['eeg'] += $healRating;
                            if ($Server->wsUsers[$clientID]['eeg'] > 100) {
                                $Server->wsUsers[$clientID]['eeg'] = 100;
                            }

                            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
                            $userObject->profile->eeg += $healRating;
                            if ($userObject->profile->eeg > 100) {
                                $userObject->profile->eeg = 100;
                            }
                            $userObject->profile->save(false);

                        }

                        if ($Server->wsPrograms[$executeProgId]['type'] == 'scanner') {

                            $exeProgRating = $Server->wsPrograms[$executeProgId]['rating'];
                            $currentRoom = $Server->wsRooms[$Server->wsUsers[$clientID]['roomId']];

                            $Server->wsSend($clientID, 'SYSMSG >>> SCANNER RESULT:');

                            $northRoom = 0;
                            $eastRoom = 0;
                            $southRoom = 0;
                            $westRoom = 0;

                            foreach ($Server->wsRooms as $cRoomId => $cRoomObject) {
                                if ($Server->wsRooms[$cRoomId]['y'] == $currentRoom['y'] + 1 &&
                                    $Server->wsRooms[$cRoomId]['x'] == $currentRoom['x']
                                    ) 
                                {
                                    $northRoom = $Server->wsRooms[$cRoomId]['roomId'];
                                }
                                if ($Server->wsRooms[$cRoomId]['y'] == $currentRoom['y'] - 1 &&
                                    $Server->wsRooms[$cRoomId]['x'] == $currentRoom['x']
                                    ) 
                                {
                                    $southRoom = $Server->wsRooms[$cRoomId]['roomId'];
                                }
                                if ($Server->wsRooms[$cRoomId]['y'] == $currentRoom['y'] &&
                                    $Server->wsRooms[$cRoomId]['x'] == $currentRoom['x'] + 1
                                    ) 
                                {
                                    $eastRoom = $Server->wsRooms[$cRoomId]['roomId'];
                                }
                                if ($Server->wsRooms[$cRoomId]['y'] == $currentRoom['y'] &&
                                    $Server->wsRooms[$cRoomId]['x'] == $currentRoom['x'] - 1
                                    ) 
                                {
                                    $westRoom = $Server->wsRooms[$cRoomId]['roomId'];
                                }
                            }

                            if ($northRoom != 0) {
                                $Server->wsSend($clientID, 'SYSMSG >>> north: ' . CHtml::encode($Server->wsRooms[$northRoom]['name']));
                            }
                            if ($eastRoom != 0) {
                                $Server->wsSend($clientID, 'SYSMSG >>> east: ' . CHtml::encode($Server->wsRooms[$eastRoom]['name']));
                            }
                            if ($southRoom != 0) {
                                $Server->wsSend($clientID, 'SYSMSG >>> south: ' . CHtml::encode($Server->wsRooms[$southRoom]['name']));
                            }
                            if ($westRoom != 0) {
                                $Server->wsSend($clientID, 'SYSMSG >>> west: ' . CHtml::encode($Server->wsRooms[$westRoom]['name']));
                            }

                        }

                    }
                    else {
                        $Server->wsSend($clientID, 'SYSMSG > program execution failed');
                    }
                    

                }
                else {
                    $Server->wsSend($clientID, 'SYSMSG > program condition critical - unable to execute');
                }

            }

        }        

        else if ($command == 'LOADPROGRAM') {
            $loadProgId = array_shift($message);
            $Server->wsSend($clientID, "LOADPROGRAM " . $loadProgId);
            $Server->wsPrograms[$loadProgId]['loaded'] = 1;

            if ($Server->wsPrograms[$loadProgId]['type'] == 'stealth') {
                $Server->wsUsers[$clientID]['stealthBonus'] += $Server->wsPrograms[$loadProgId]['rating'];
                $Server->wsSend($clientID, 'CHANGESTEALTHBONUS ' . $Server->wsPrograms[$loadProgId]['rating']);
            }

            if ($Server->wsPrograms[$loadProgId]['type'] == 'attack') {
                $Server->wsUsers[$clientID]['attackBonus'] += $Server->wsPrograms[$loadProgId]['rating'];
                $Server->wsSend($clientID, 'CHANGEATTACKBONUS ' . $Server->wsPrograms[$loadProgId]['rating']);
            }

            if ($Server->wsPrograms[$loadProgId]['type'] == 'detect') {
                $Server->wsUsers[$clientID]['detectBonus'] += $Server->wsPrograms[$loadProgId]['rating'];
                $Server->wsSend($clientID, 'CHANGEDETECTBONUS ' . $Server->wsPrograms[$loadProgId]['rating']);
            }

            if ($Server->wsPrograms[$loadProgId]['type'] == 'defend') {
                $Server->wsUsers[$clientID]['defendBonus'] += $Server->wsPrograms[$loadProgId]['rating'];
                $Server->wsSend($clientID, 'CHANGEDEFENDBONUS ' . $Server->wsPrograms[$loadProgId]['rating']);
            }

            $Server->wsSend($clientID, "SYSMSG > program loaded");
        } 

        else if ($command == 'MODIFYNODE') {

            $newType = array_shift($message);
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
                    $cost = 250;
                break;

            }

            if ($room->type != 'io' && $userObject->profile->credits >= $cost) {

                $userObject->profile->credits -= $cost;
                $userObject->profile->save(false);
                $Server->wsSend($clientID, "CREDITSCHANGE " . $cost);

                if ($room->type == 'database') {
                    $Server->wsSend($clientID, "REDUCEMAXSTORAGE " . $room->level);
                    $Server->wsUsers[$clientID]['maxStorage'] -= $room->level;
                    if ($Server->wsUsers[$clientID]['maxStorage'] < 0) {
                        $Server->wsUsers[$clientID]['maxStorage'] = 0;
                    }
                }

                if ($room->type == 'coproc') {
                    $Server->wsSend($clientID, "REDUCEMAXMEMORY " . $room->level);
                    $Server->wsUsers[$clientID]['maxMemory'] -= $room->level;
                    if ($Server->wsUsers[$clientID]['maxMemory'] < 0) {
                        $Server->wsUsers[$clientID]['maxMemory'] = 0;
                    }
                }

                $room->type = $newType;
                $room->name = $newType . ' node';
                $room->save(false);

                if ($room->type == 'database') {
                    $Server->wsSend($clientID, "RAISEMAXSTORAGE " . $room->level);
                    $Server->wsUsers[$clientID]['maxStorage'] += $room->level;
                }

                if ($room->type == 'coproc') {
                    $Server->wsSend($clientID, "RAISEMAXMEMORY " . $room->level);
                    $Server->wsUsers[$clientID]['maxMemory'] += $room->level;
                }

                $Server->wsRooms[$room->id]['type'] = $room->type;
                $Server->wsRooms[$room->id]['name'] = $room->name;

                $Server->wsSend($clientID, "RECALL");

                foreach ( $Server->wsClients as $id => $client ) {
                    if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                        $Server->wsSend($id, "RECALL");
                    }
                }

            }

        }

        else if ($command == 'MOVETO') {
            $targetNode = array_shift($message);
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
            $userObject->profile->location = $targetNode;
            $userObject->profile->save(false);
            $Server->wsUsers[$clientID]['roomId'] = $targetNode;
            $Server->wsSend($clientID, "RESETPROGRESS");
            $Server->wsSend($clientID, "RECALL");
        }

        else if ($command == 'RECALL') {
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
            $userObject->profile->location = $Server->wsUsers[$clientID]['homeNode'];
            $userObject->profile->save(false);
            $Server->wsUsers[$clientID]['roomId'] = $Server->wsUsers[$clientID]['homeNode'];
            $Server->wsSend($clientID, "RECALL");
            $Server->wsSend($clientID, "SYSMSG You have connected to your home system.");
        }

        else if ($command == 'ROOMUPDATE') {

            $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
            $roomOwnerName = ($room->userId == 0) ? 'System' : CHtml::encode($room->user->username);

            $northExit = $room->getExit('north');
            $eastExit = $room->getExit('east');
            $southExit = $room->getExit('south');
            $westExit = $room->getExit('west');

            //$Server->log($northExit);

            $Server->wsSend($clientID, "ROOMNAME " . CHtml::encode($room->name));
            $Server->wsSend($clientID, "ROOMTYPE " . CHtml::encode($room->type));
            $Server->wsSend($clientID, "ROOMOWNER " . $roomOwnerName);
            $Server->wsSend($clientID, "ROOMLEVEL " . $room->level);
            $Server->wsSend($clientID, "NORTHEXIT " . $northExit);
            $Server->wsSend($clientID, "EASTEXIT " . $eastExit);
            $Server->wsSend($clientID, "SOUTHEXIT " . $southExit);
            $Server->wsSend($clientID, "WESTEXIT " . $westExit);
            $Server->wsSend($clientID, "ROOMID " . $room->id);

        }

         else if ($command == 'UNLOADALL') {
            foreach ($Server->wsPrograms as $programId => $programObject) {
                if ($Server->wsPrograms[$programId]['userId'] == $Server->wsUsers[$clientID]['userId'] && $Server->wsPrograms[$programId]['loaded'] == 1) {

                    $Server->wsPrograms[$programId]['loaded'] = 0;
                    $Server->wsSend($clientID, 'UNLOADPROGRAM ' . $programId);

                    $Server->wsPrograms[$programId]['loaded'] = 0;

                    if ($Server->wsPrograms[$programId]['type'] == 'attack') {
                        $Server->wsSend($clientID, 'REDUCEATTACKBONUS ' . $Server->wsPrograms[$programId]['rating']);
                    }

                    if ($Server->wsPrograms[$programId]['type'] == 'defend') {
                        $Server->wsSend($clientID, 'REDUCEDEFENDBONUS ' . $Server->wsPrograms[$programId]['rating']);
                    }

                    if ($Server->wsPrograms[$programId]['type'] == 'detect') {
                        $Server->wsSend($clientID, 'REDUCEDETECTBONUS ' . $Server->wsPrograms[$programId]['rating']);
                    }

                    if ($Server->wsPrograms[$programId]['type'] == 'stealth') {
                        $Server->wsSend($clientID, 'REDUCESTEALTHBONUS ' . $Server->wsPrograms[$programId]['rating']);
                    }

                }
            }

            $Server->wsSend($clientID, 'SYSMSG > active memory cleared');
        }

        else if ($command == 'UNLOADPROGRAM') {

            $programId = array_shift($message);

            if ($Server->wsPrograms[$programId]['userId'] == $Server->wsUsers[$clientID]['userId'] && $Server->wsPrograms[$programId]['loaded'] == 1) {

                $Server->wsPrograms[$programId]['loaded'] = 0;
                $Server->wsSend($clientID, 'UNLOADPROGRAM ' . $programId);

                $Server->wsPrograms[$programId]['loaded'] = 0;

                if ($Server->wsPrograms[$programId]['type'] == 'attack') {
                    $Server->wsSend($clientID, 'REDUCEATTACKBONUS ' . $Server->wsPrograms[$programId]['rating']);
                }

                if ($Server->wsPrograms[$programId]['type'] == 'defend') {
                    $Server->wsSend($clientID, 'REDUCEDEFENDBONUS ' . $Server->wsPrograms[$programId]['rating']);
                }

                if ($Server->wsPrograms[$programId]['type'] == 'detect') {
                    $Server->wsSend($clientID, 'REDUCEDETECTBONUS ' . $Server->wsPrograms[$programId]['rating']);
                }

                if ($Server->wsPrograms[$programId]['type'] == 'stealth') {
                    $Server->wsSend($clientID, 'REDUCESTEALTHBONUS ' . $Server->wsPrograms[$programId]['rating']);
                }

            }

            $Server->wsSend($clientID, 'SYSMSG > program unloaded');
        }

        else if ($command == 'UPDATEME') {

            $newX = array_shift($message);
            $newY = array_shift($message);

            $Server->wsUsers[$clientID]['x'] = $newX;
            $Server->wsUsers[$clientID]['y'] = $newY;

            foreach ( $Server->wsClients as $id => $client ) {
                if (isset($Server->wsUsers[$id])) {
                    if ( $id != $clientID ) {
                        $Server->wsSend($clientID, "OTHERUSER " . $id . " " . $Server->wsUsers[$id]['x'] . " " . $Server->wsUsers[$id]['y']. " " . $Server->wsUsers[$id]['roomId']);
                    }
                }
            }

            foreach ( $Server->wsEntities as $ide => $cliente ) {
                if ($Server->wsEntities[$ide]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                    $Server->wsSend($clientID, "OTHERENTITY " . $ide . " " . $Server->wsEntities[$ide]['x'] . " " . $Server->wsEntities[$ide]['y']. " " . $Server->wsEntities[$ide]['roomId'] . " " . $Server->wsEntities[$ide]['eeg'] . " " . $Server->wsEntities[$ide]['type']);
                }
            }

        }
       
        else if ($command == 'UPGRADENODE') {

            $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

            $cost = ($room->level * 1000) * $room->level;

            if ($room->type != 'io' && $userObject->profile->credits >= $cost) {

                $userObject->profile->credits -= $cost;
                $userObject->profile->save(false);
                $Server->wsSend($clientID, "CREDITSCHANGE " . $cost);

                if ($room->type == 'database') {
                    $Server->wsSend($clientID, "RAISEMAXSTORAGE 1");
                    $Server->wsUsers[$clientID]['maxStorage'] += 1;
                }

                if ($room->type == 'coproc') {
                    $Server->wsSend($clientID, "RAISEMAXMEMORY 1");
                    $Server->wsUsers[$clientID]['maxMemory'] += 1;
                }

                $room->level += 1;
                $room->save(false);

                $Server->wsSend($clientID, "RECALL");

                foreach ( $Server->wsClients as $id => $client ) {
                    if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                        $Server->wsSend($id, "RECALL");
                    }
                }

            }

        }
        
        else {
            $Server->wsSend($clientID, implode(' ', $message));
        }

    }

    // when a client connects
    static function wsOnOpen($clientID)
    {
        $Server = WebSocket::getInstance();;
        $ip = long2ip( $Server->wsClients[$clientID][6] );

        $Server->log( "$ip ($clientID) has connected." );

    }

    // when a client closes or lost connection
    static function wsOnClose($clientID, $status) {
        $Server = WebSocket::getInstance();
        $ip = long2ip( $Server->wsClients[$clientID][6] );

        foreach ( $Server->wsClients as $id => $client ) {
            $Server->wsSend($id, "REMOVEUSER " . $clientID);
        }

        $Server->log( "$ip ($clientID) has disconnected." );

    }
}