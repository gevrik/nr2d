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

        $parsed = json_decode($message);
        $xcommand = $parsed->xcommand;

        //$message = explode(' ', $message);
        //$command = array_shift($message);

        if ($xcommand == 'INITP') {

            $hash = $parsed->xvalue;
            $Server->log($hash);

            $users = User::model()->findAll();
            $foundUser = false;

            foreach ($users as $user) {
                if (hash('sha256', $user->id . 'netrunners') == $hash) {
                    $foundUser = true;
                    $userObject = $user;
                }
            }

            if ($foundUser == false) {
                $Server->wsClose($clientID);
                return;
            }

            //$userObject = User::model()->findByPk($parsed->xvalue);

            $profileObject = $userObject->profile;
            $stealthBonus = 0;
            $attackBonus = 0;
            $detectBonus = 0;
            $defendBonus = 0;

            $maxMemory = 0;
            $maxStorage = 0;

            foreach ($userObject->rooms as $room) {
                if ($room->type == 'database') {
                    $maxStorage += ($room->level * $room->level);
                }
                if ($room->type == 'coproc') {
                    $maxMemory += ($room->level * $room->level);
                }
            }

            $Server->wsUsers[$clientID] = array('userId' => $userObject->id, 'speed' => $profileObject->speed ,'roomId' => $profileObject->location, 'x' => 256, 'y' => 256, 'homeNode' => $profileObject->homenode, 'socketId' => $clientID ,'credits' => $profileObject->credits, 'secrating' => $profileObject->secrating, 'stealth' => $profileObject->stealth, 'detect' => $profileObject->detect, 'attack' => $profileObject->attack, 'defend' => $profileObject->defend, 'coding' => $profileObject->coding, 'snippets' => $profileObject->snippets, 'eeg' => $profileObject->eeg, 'willpower' => $profileObject->willpower, 'stealthBonus' => $stealthBonus, 'attackBonus' => $attackBonus, 'maxStorage' => $maxStorage, 'maxMemory' => $maxMemory, 'detectBonus' => $detectBonus, 'defendBonus' => $defendBonus, 'decking' => $profileObject->decking, 'slots' => $profileObject->slots);

            $returnCommand = array(
                'xcommand' => 'INITP',
                'xvalue' => array(
                    'userId' => (int)$userObject->id,
                    'speed' => (int)$profileObject->speed,
                    'roomId' => (int)$profileObject->location,
                    'x' => 256,
                    'y' => 256,
                    'homeId' => (int)$profileObject->homenode,
                    'socketId' => (int)$clientID,
                    'credits' => (int)$profileObject->credits,
                    'secrating' => (int)$profileObject->secrating,
                    'stealth' => (int)$profileObject->stealth,
                    'detect' => (int)$profileObject->detect,
                    'attack' => (int)$profileObject->attack,
                    'defend' => (int)$profileObject->defend,
                    'coding' => (int)$profileObject->coding,
                    'snippets' => (int)$profileObject->snippets,
                    'eeg' => (int)$profileObject->eeg,
                    'willpower' => (int)$profileObject->willpower,
                    'stealthBonus' => $stealthBonus,
                    'detectBonus' => $detectBonus,
                    'attackBonus' => $attackBonus,
                    'defendBonus' => $defendBonus,
                    'maxStorage' => $maxStorage,
                    'maxMemory' => $maxMemory,
                    'decking' => (int)$profileObject->decking,
                    'slots' => (int)$profileObject->slots,
                    'name' => $userObject->username,
                    'attackspeed' => $userObject->profile->attackspeed
                ),
            );
            $Server->wsSend($clientID, json_encode($returnCommand));

            foreach ( $Server->wsClients as $id => $client ) {
                if (isset($Server->wsUsers[$id])) {
                    if ( $id != $clientID ) {
                        $returnCommand['xcommand'] = 'OTHERUSER';
                        $Server->wsSend($id, json_encode($returnCommand));

                        $otherUser = $Server->wsUsers[$id];

                        $returnCommand = array(
                            'xcommand' => 'OTHERUSER',
                            'xvalue' => array(
                                'userId' => (int)$otherUser['userId'],
                                'speed' => (int)$otherUser['speed'],
                                'roomId' => (int)$otherUser['roomId'],
                                'x' => (int)$otherUser['x'],
                                'y' => (int)$otherUser['x'],
                                'homeId' => (int)$otherUser['homeNode'],
                                'socketId' => (int)$otherUser['socketId'],
                                'credits' => (int)$otherUser['credits'],
                                'secrating' => (int)$otherUser['secrating'],
                                'stealth' => (int)$otherUser['stealth'],
                                'detect' => (int)$otherUser['detect'],
                                'attack' => (int)$otherUser['attack'],
                                'defend' => (int)$otherUser['defend'],
                                'coding' => (int)$otherUser['coding'],
                                'snippets' => (int)$otherUser['snippets'],
                                'eeg' => (int)$otherUser['eeg'],
                                'willpower' => (int)$otherUser['willpower'],
                                'stealthBonus' => (int)$otherUser['stealthBonus'],
                                'detectBonus' => (int)$otherUser['detectBonus'],
                                'attackBonus' => (int)$otherUser['attackBonus'],
                                'defendBonus' => (int)$otherUser['defendBonus'],
                                'maxStorage' => (int)$otherUser['maxStorage'],
                                'maxMemory' => (int)$otherUser['maxMemory'],
                                'decking' => (int)$otherUser['decking'],
                                'slots' => (int)$otherUser['slots']
                            ),
                        );
                        
                        $Server->wsSend($clientID, json_encode($returnCommand));
                        
                    }
                }
            }

            foreach ( $Server->wsPrograms as $programId => $programObject ) {
                if ($Server->wsPrograms[$programId]['userId'] == $Server->wsUsers[$clientID]['userId']) {

                    $storageProgram = $Server->wsPrograms[$programId];

                    $returnCommand = array(
                        'xcommand' => 'ADDTOSTORAGE',
                        'xvalue' => array(
                            'programId' => (int)$storageProgram['programId'],
                            'userId' => (int)$storageProgram['userId'],
                            'type' => $storageProgram['type'],
                            'rating' => (int)$storageProgram['rating'],
                            'condition' => (int)$storageProgram['condition'],
                            'maxUpgrades' => (int)$storageProgram['maxUpgrades'],
                            'upgrades' => (int)$storageProgram['upgrades'],
                            'name' => $storageProgram['name']
                        )
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));
                }
            }

            foreach ($Server->wsBullets as $bulletId => $bulletObject) {
                if ($Server->wsBullets[$bulletId]['roomId'] == $Server->wsUsers[$clientID]['roomId'] ) {
                    $returnCommand = array(
                        'xcommand' => 'ADDBULLET',
                        'xvalue' => $Server->wsBullets[$bulletId]
                    );
                }
            }

        }

        else if ($xcommand == 'ADDBULLET') {
            
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
                'hadImpact' => 0
            );


            $returnCommand = array(
                'xcommand' => 'ADDBULLET',
                'xvalue' => $Server->wsBullets[$currentBullets]
            );

            foreach ( $Server->wsClients as $id => $client ) {
                //$Server->log('socket found');
                if ($Server->wsUsers[$id]['roomId'] == $Server->wsBullets[$currentBullets]['roomId']) {
                    $Server->wsSend($id, json_encode($returnCommand));
                    $Server->log('bullet sent');
                }
            }
        }

        else if ($xcommand == 'DELETEBULLET') {
            $returnCommand = array(
                'xcommand' => 'DELETEBULLET',
                'xvalue' => $parsed->xvalue
            );
            foreach ( $Server->wsClients as $id => $client ) {
                $Server->wsSend($id, json_encode($returnCommand));
            }
            unset($Server->wsBullets[$parsed->xvalue]);
        }

        else if ($xcommand == 'CHAT') {
            
            $returnCommand = array(
                'xcommand' => 'CHAT',
                'xvalue' => $parsed->xvalue
            );

            foreach ( $Server->wsClients as $id => $client ) {
                $Server->wsSend($id, json_encode($returnCommand));
            }
        }

        else if ($xcommand == 'CHATSUBO') {
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

        if ($xcommand == 'CREATENODE') {
            $direction = $parsed->xvalue;
            $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

            if ($room->userId == $Server->wsUsers[$clientID]['userId'] && $userObject->profile->credits >= 100) {

                if ($room->getExit($direction) == 0) {

                    $userObject->profile->credits -= 100;
                    $userObject->profile->save(false);

                    $returnCommand = array(
                        'xcommand' => 'CREDITSCHANGE',
                        'xvalue' => 100
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));

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

        else if ($xcommand == 'CREATEPROGRAM') {
            $programType = $parsed->xvalue;
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

                    $returnCommand = array(
                        'xcommand' => 'CREDITSCHANGE',
                        'xvalue' => $creditCost
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));

                    $returnCommand = array(
                        'xcommand' => 'SNIPPETSCHANGE',
                        'xvalue' => $snippetCost
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));

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

                    $storageProgram = $Server->wsPrograms[$newModel->id];

                    $returnCommand = array(
                        'xcommand' => 'ADDTOSTORAGE',
                        'xvalue' => array(
                            'programId' => (int)$storageProgram['programId'],
                            'userId' => (int)$storageProgram['userId'],
                            'type' => $storageProgram['type'],
                            'rating' => (int)$storageProgram['rating'],
                            'condition' => (int)$storageProgram['condition'],
                            'maxUpgrades' => (int)$storageProgram['maxUpgrades'],
                            'upgrades' => (int)$storageProgram['upgrades'],
                            'name' => $storageProgram['name']
                        )
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));

                    $returnCommand = array(
                        'xcommand' => 'SYSMSG',
                        'xvalue' => '> coding complete'
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));

            }

        }

        else if ($xcommand == 'DAMAGEUSER') {
            $damagedUserSocketId = $parsed->xvalue;

            $returnCommand = array(
                'xcommand' => 'REDUCEEEG',
                'xvalue' => 1
            );
            $Server->wsSend($damagedUserSocketId, json_encode($returnCommand));

        }

        else if ($xcommand == 'EXECUTEPROGRAM') {
            $executeProgId = $parsed->xvalue;

            if ($Server->wsPrograms[$executeProgId]) {

                if ($Server->wsPrograms[$executeProgId]['condition'] > 0) {

                    $Server->wsPrograms[$executeProgId]['condition'] -= 1;
                    if ($Server->wsPrograms[$executeProgId]['condition'] < 0) {
                        $Server->wsPrograms[$executeProgId]['condition'] = 0;
                    }

                    $returnCommand = array(
                        'xcommand' => 'REDUCEPROGCOND',
                        'xvalue' => $executeProgId
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));

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

                            $returnCommand = array(
                                'xcommand' => 'RAISEEEG',
                                'xvalue' => $executeProgId
                            );
                            $Server->wsSend($clientID, json_encode($returnCommand));

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

                            $returnCommand = array(
                                'xcommand' => 'SYSMSG',
                                'xvalue' => '>>> SCANNER RESULT'
                            );
                            $Server->wsSend($clientID, json_encode($returnCommand));

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
                                $returnCommand = array(
                                    'xcommand' => 'SYSMSG',
                                    'xvalue' => '>>> north: ' . CHtml::encode($Server->wsRooms[$northRoom]['name'])
                                );
                                $Server->wsSend($clientID, json_encode($returnCommand));
                            }
                            if ($eastRoom != 0) {
                                $returnCommand = array(
                                    'xcommand' => 'SYSMSG',
                                    'xvalue' => '>>> east: ' . CHtml::encode($Server->wsRooms[$eastRoom]['name'])
                                );
                                $Server->wsSend($clientID, json_encode($returnCommand));
                            }
                            if ($southRoom != 0) {
                                $returnCommand = array(
                                    'xcommand' => 'SYSMSG',
                                    'xvalue' => '>>> south: ' . CHtml::encode($Server->wsRooms[$southRoom]['name'])
                                );
                                $Server->wsSend($clientID, json_encode($returnCommand));
                            }
                            if ($westRoom != 0) {
                                $returnCommand = array(
                                    'xcommand' => 'SYSMSG',
                                    'xvalue' => '>>> west: ' . CHtml::encode($Server->wsRooms[$westRoom]['name'])
                                );
                                $Server->wsSend($clientID, json_encode($returnCommand));
                            }

                        }

                    }
                    else {
                        $returnCommand = array(
                                    'xcommand' => 'SYSMSG',
                                    'xvalue' => '> program execution failed'
                                );
                                $Server->wsSend($clientID, json_encode($returnCommand));
                    }
                    

                }
                else {
                    $returnCommand = array(
                        'xcommand' => 'SYSMSG',
                        'xvalue' => '> program condition critical - execution failed'
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));
                }

            }

        } 

        else if ($xcommand == 'LOADPROGRAM') {
            $loadProgId = $parsed->xvalue;

            $returnCommand = array(
                'xcommand' => 'LOADPROGRAM',
                'xvalue' => $loadProgId
            );
            $Server->wsSend($clientID, json_encode($returnCommand));
            
            $Server->wsPrograms[$loadProgId]['loaded'] = 1;

            if ($Server->wsPrograms[$loadProgId]['type'] == 'stealth') {
                $Server->wsUsers[$clientID]['stealthBonus'] += $Server->wsPrograms[$loadProgId]['rating'];

                $returnCommand = array(
                    'xcommand' => 'RAISEBONUS',
                    'xvalue' => array(
                        'type' => 'stealth',
                        'amount' => (int)$Server->wsPrograms[$loadProgId]['rating']
                    )
                );
                $Server->wsSend($clientID, json_encode($returnCommand));

            }

            if ($Server->wsPrograms[$loadProgId]['type'] == 'attack') {
                $Server->wsUsers[$clientID]['attackBonus'] += $Server->wsPrograms[$loadProgId]['rating'];
                $returnCommand = array(
                    'xcommand' => 'RAISEBONUS',
                    'xvalue' => array(
                        'type' => 'attack',
                        'amount' => (int)$Server->wsPrograms[$loadProgId]['rating']
                    )
                );
                $Server->wsSend($clientID, json_encode($returnCommand));
            }

            if ($Server->wsPrograms[$loadProgId]['type'] == 'detect') {
                $Server->wsUsers[$clientID]['detectBonus'] += $Server->wsPrograms[$loadProgId]['rating'];
                $returnCommand = array(
                    'xcommand' => 'RAISEBONUS',
                    'xvalue' => array(
                        'type' => 'detect',
                        'amount' => (int)$Server->wsPrograms[$loadProgId]['rating']
                    )
                );
                $Server->wsSend($clientID, json_encode($returnCommand));
            }

            if ($Server->wsPrograms[$loadProgId]['type'] == 'defend') {
                $Server->wsUsers[$clientID]['defendBonus'] += $Server->wsPrograms[$loadProgId]['rating'];
                $returnCommand = array(
                    'xcommand' => 'RAISEBONUS',
                    'xvalue' => array(
                        'type' => 'defend',
                        'amount' => (int)$Server->wsPrograms[$loadProgId]['rating']
                    )
                );
                $Server->wsSend($clientID, json_encode($returnCommand));
            }

            $returnCommand = array(
                'xcommand' => 'SYSMSG',
                'xvalue' => '> program loaded'
            );
            $Server->wsSend($clientID, json_encode($returnCommand));
        }

        else if ($xcommand == 'MODIFYNODE') {

            $newType = $parsed->xvalue;
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

                $returnCommand = array(
                    'xcommand' => 'CREDITSCHANGE',
                    'xvalue' => $cost
                );
                $Server->wsSend($clientID, json_encode($returnCommand));

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

        else if ($xcommand == 'MOVETO') {
            $targetNode = $parsed->xvalue;
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
            $userObject->profile->location = $targetNode;
            $userObject->profile->save(false);
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

        else if ($xcommand == 'RECALL') {
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
            $userObject->profile->location = $Server->wsUsers[$clientID]['homeNode'];
            $userObject->profile->save(false);
            $Server->wsUsers[$clientID]['roomId'] = $Server->wsUsers[$clientID]['homeNode'];
            $returnCommand = array(
                'xcommand' => 'RECALL',
                'xvalue' => 0
            );
            $Server->wsSend($clientID, json_encode($returnCommand));
            $Server->wsSend($clientID, json_encode($returnCommand));
            $returnCommand = array(
                'xcommand' => 'SYSMSG',
                'xvalue' => '> home system connection established'
            );
            $Server->wsSend($clientID, json_encode($returnCommand));
        }

        else if ($xcommand == 'ROOMUPDATE') {

            $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
            $Server->log($room->name);
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

        else if ($xcommand == 'UNLOADALL') {
            foreach ($Server->wsPrograms as $programId => $programObject) {
                if ($Server->wsPrograms[$programId]['userId'] == $Server->wsUsers[$clientID]['userId'] && $Server->wsPrograms[$programId]['loaded'] == 1) {

                    $Server->wsPrograms[$programId]['loaded'] = 0;

                    $returnCommand = array(
                        'xcommand' => 'UNLOADPROGRAM',
                        'xvalue' => $programId
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));

                    if ($Server->wsPrograms[$programId]['type'] == 'attack') {
                        $returnCommand = array(
                            'xcommand' => 'REDUCEBONUS',
                            'xvalue' => array(
                                'type' => 'attack',
                                'amount' => (int)$Server->wsPrograms[$programId]['rating']
                            )
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));
                    }

                    if ($Server->wsPrograms[$programId]['type'] == 'defend') {
                        $returnCommand = array(
                            'xcommand' => 'REDUCEBONUS',
                            'xvalue' => array(
                                'type' => 'defend',
                                'amount' => (int)$Server->wsPrograms[$programId]['rating']
                            )
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));
                    }

                    if ($Server->wsPrograms[$programId]['type'] == 'detect') {
                        $returnCommand = array(
                            'xcommand' => 'REDUCEBONUS',
                            'xvalue' => array(
                                'type' => 'detect',
                                'amount' => (int)$Server->wsPrograms[$programId]['rating']
                            )
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));
                    }

                    if ($Server->wsPrograms[$programId]['type'] == 'stealth') {
                        $returnCommand = array(
                            'xcommand' => 'REDUCEBONUS',
                            'xvalue' => array(
                                'type' => 'stealth',
                                'amount' => (int)$Server->wsPrograms[$programId]['rating']
                            )
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));
                    }

                }
            }

            $returnCommand = array(
                'xcommand' => 'SYSMSG',
                'xvalue' => '> active memory cleared'
            );
            $Server->wsSend($clientID, json_encode($returnCommand));
        }

        else if ($xcommand == 'UNLOADPROGRAM') {

            $programId = $parsed->xvalue;

            if ($Server->wsPrograms[$programId]['userId'] == $Server->wsUsers[$clientID]['userId'] && $Server->wsPrograms[$programId]['loaded'] == 1) {

                $Server->wsPrograms[$programId]['loaded'] = 0;

                $returnCommand = array(
                    'xcommand' => 'UNLOADPROGRAM',
                    'xvalue' => $programId
                );
                $Server->wsSend($clientID, json_encode($returnCommand));

                if ($Server->wsPrograms[$programId]['type'] == 'attack') {
                    $returnCommand = array(
                            'xcommand' => 'REDUCEBONUS',
                            'xvalue' => array(
                                'type' => 'attack',
                                'amount' => (int)$Server->wsPrograms[$programId]['rating']
                            )
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));
                }

                if ($Server->wsPrograms[$programId]['type'] == 'defend') {
                    $returnCommand = array(
                            'xcommand' => 'REDUCEBONUS',
                            'xvalue' => array(
                                'type' => 'defend',
                                'amount' => (int)$Server->wsPrograms[$programId]['rating']
                            )
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));
                }

                if ($Server->wsPrograms[$programId]['type'] == 'detect') {
                    $returnCommand = array(
                            'xcommand' => 'REDUCEBONUS',
                            'xvalue' => array(
                                'type' => 'detect',
                                'amount' => (int)$Server->wsPrograms[$programId]['rating']
                            )
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));
                }

                if ($Server->wsPrograms[$programId]['type'] == 'stealth') {
                    $returnCommand = array(
                            'xcommand' => 'REDUCEBONUS',
                            'xvalue' => array(
                                'type' => 'stealth',
                                'amount' => (int)$Server->wsPrograms[$programId]['rating']
                            )
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));
                }

            }

            $returnCommand = array(
                'xcommand' => 'SYSMSG',
                'xvalue' => '> program unloaded'
            );
            $Server->wsSend($clientID, json_encode($returnCommand));
        }

        else if ($xcommand == 'UPDATEENTITY') {
            $newX = $parsed->xvalue->newX;
            $newY = $parsed->xvalue->newY;

            $Server->wsEntities[$parsed->xvalue->entityId]['x'] = $newX;
            $Server->wsEntities[$parsed->xvalue->entityId]['y'] = $newY;

        }

        else if ($xcommand == 'UPDATEME') {

            $newX = $parsed->xvalue->x;
            $newY = $parsed->xvalue->y;

            $Server->wsUsers[$clientID]['x'] = $newX;
            $Server->wsUsers[$clientID]['y'] = $newY;

            foreach ( $Server->wsClients as $id => $client ) {
                if (isset($Server->wsUsers[$id])) {
                    if ( $id != $clientID ) {

                        //$Server->log('found another user');

                        $otherUser = $Server->wsUsers[$id];

                        $returnCommand = array(
                            'xcommand' => 'OTHERUSER',
                            'xvalue' => array(
                                'userId' => (int)$otherUser['userId'],
                                'speed' => (int)$otherUser['speed'],
                                'roomId' => (int)$otherUser['roomId'],
                                'x' => (int)$otherUser['x'],
                                'y' => (int)$otherUser['y'],
                                'homeId' => (int)$otherUser['homeNode'],
                                'socketId' => (int)$otherUser['socketId'],
                                'credits' => (int)$otherUser['credits'],
                                'secrating' => (int)$otherUser['secrating'],
                                'stealth' => (int)$otherUser['stealth'],
                                'detect' => (int)$otherUser['detect'],
                                'attack' => (int)$otherUser['attack'],
                                'defend' => (int)$otherUser['defend'],
                                'coding' => (int)$otherUser['coding'],
                                'snippets' => (int)$otherUser['snippets'],
                                'eeg' => (int)$otherUser['eeg'],
                                'willpower' => (int)$otherUser['willpower'],
                                'stealthBonus' => (int)$otherUser['stealthBonus'],
                                'detectBonus' => (int)$otherUser['detectBonus'],
                                'attackBonus' => (int)$otherUser['attackBonus'],
                                'defendBonus' => (int)$otherUser['defendBonus'],
                                'maxStorage' => (int)$otherUser['maxStorage'],
                                'maxMemory' => (int)$otherUser['maxMemory'],
                                'decking' => (int)$otherUser['decking'],
                                'slots' => (int)$otherUser['slots']
                            ),
                        );

                        //var_dump($returnCommand);die;
                        
                        $Server->wsSend($clientID, json_encode($returnCommand));
                    }
                }
            }

            foreach ( $Server->wsEntities as $ide => $cliente ) {
                if ($Server->wsEntities[$ide]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {

                    $entity = $Server->wsEntities[$ide];

                    $returnCommand = array(
                        'xcommand' => 'OTHERENTITY',
                        'xvalue' => array(
                            'id' => $ide,
                            'x' => (int)$entity['x'],
                            'y' => (int)$entity['y'],
                            'roomId' => (int)$entity['roomId'],
                            'eeg' => (int)$entity['eeg'],
                            'userId' => (int)$entity['userId'],
                            'type' => $entity['type'],
                            'targetX' => $entity['targetX'],
                            'targetY' => $entity['targetY'],
                            'trajX' => $entity['trajX'],
                            'trajY' => $entity['trajY'],
                            'speed' => $entity['entity'],
                            'moveTimer' => $entity['moveTimer']
                        )
                    );

                    $Server->wsSend($clientID, json_encode($returnCommand));
                }
            }

        }

        else if ($xcommand == 'UPGRADENODE') {

            $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

            $cost = ($room->level * 1000) * $room->level;

            if ($room->type != 'io' && $userObject->profile->credits >= $cost) {

                $userObject->profile->credits -= $cost;
                $userObject->profile->save(false);

                $returnCommand = array(
                    'xcommand' => 'CREDITSCHANGE',
                    'xvalue' => $cost
                );
                $Server->wsSend($clientID, json_encode($returnCommand));

                if ($room->type == 'database') {

                    $bonusMB = (($room->level + 1) * ($roomLevel + 1)) - ($room->level * $room->level);

                    $returnCommand = array(
                        'xcommand' => 'RAISEMAXSTORAGE',
                        'xvalue' => $bonusMB
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));
                    $Server->wsUsers[$clientID]['maxStorage'] += $bonusMB;
                }

                if ($room->type == 'coproc') {
                    $bonusMB = (($room->level + 1) * ($roomLevel + 1)) - ($room->level * $room->level);

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
                    if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                        $Server->wsSend($id, json_encode($returnCommand));
                    }
                }

            }

        }


        // if ($command == 'ATTACKVIRUS') {
        //     $targetVirusId = array_shift($message);
        //     $virusDamage = array_shift($message);
        //     $pAttack = $Server->wsUsers[$clientID]['attack'] + $Server->wsUsers[$clientID]['attackBonus'];
        //     $vDefend = $Server->wsEntities[$targetVirusId]['defend'];
        //     srand();
        //     $skillRoll = rand(2, 20) + $pAttack - $vDefend;
        //     if ($skillRoll >= 11) {

        //         foreach ($Server->wsClients as $recipId => $recipClient) {
        //             if ($Server->wsUsers[$recipId]['roomId'] ==  $Server->wsEntities[$targetVirusId]['roomId']) {
        //                 $Server->wsSend($recipId, 'CREATEBULLET ' . round($Server->wsUsers[$clientID]['x']) . ' ' . round($Server->wsUsers[$clientID]['y']) . ' ' . round($Server->wsEntities[$targetVirusId]['x']) . ' ' . round($Server->wsEntities[$targetVirusId]['y']));
        //             }
        //         }

        //         $bonusDamage = 0;

        //         if ($skillRoll > 20) {
        //             $bonusDamage = $skillRoll - 20;
        //         }

        //         $Server->wsEntities[$targetVirusId]['eeg'] -= ($virusDamage + $bonusDamage);

        //         if ($Server->wsEntities[$targetVirusId]['eeg'] < 1) {
        //             //virus flatlined
        //             $Server->wsSend($clientID, 'SYSMSG > virus flatlined');
        //             $Server->wsSend($clientID, 'FLATLINEVIRUS ' . $targetVirusId);
        //             $Server->wsSend($clientID, "RECALL");
        //             unset($Server->wsEntities[$targetVirusId]);
        //             foreach ( $Server->wsClients as $id => $client ) {
        //                 if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
        //                     $Server->wsSend($id, 'FLATLINEVIRUS ' . $targetVirusId);
        //                     $Server->wsSend($id, "RECALL");
        //                 }
        //             }
        //             $entityObject = Entity::model()->findByPk($targetVirusId);
        //             $entityObject->delete(false);
        //         }
        //         else {

        //             $Server->wsSend($clientID, 'SYSMSG > virus hit');
        //             $Server->wsSend($clientID, 'DAMAGEVIRUS ' . $targetVirusId . ' ' . ($virusDamage + $bonusDamage));

        //             foreach ( $Server->wsClients as $id => $client ) {
        //                 if ( $id != $clientID && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
        //                     $Server->wsSend($id, "RECALL");
        //                 }
        //             }
        //         }

        //     }
        //     else {
        //         $Server->wsSend($clientID, 'SYSMSG > virus missed');   
        //     }
        // }
     
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
            $returnCommand = array(
                'xcommand' => 'REMOVEUSER',
                'xvalue' => $clientID
            );
            //$Server->wsSend($id, "REMOVEUSER " . $clientID);
        }

        $Server->log( "$ip ($clientID) has disconnected." );

    }
}