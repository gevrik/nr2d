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
            //$Server->log($hash);

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

            foreach ($Server->wsClients as $checkClientId => $checkClient) {
                if (isset($Server->wsUsers[$checkClientId])) {
                    if ($Server->wsUsers[$checkClientId]['userId'] == $userObject->id) {
                        $Server->wsClose($clientID);
                        return;
                    }
                }
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
                    'attackspeed' => (int)$userObject->profile->attackspeed
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

        else if ($xcommand == 'UPDATEME') {

            $newX = $parsed->xvalue->x;
            $newY = $parsed->xvalue->y;

            $Server->wsUsers[$clientID]['x'] = $newX;
            $Server->wsUsers[$clientID]['y'] = $newY;

            $returnCommand = array(
                'xcommand' => 'UPDATESTATS',
                'xvalue' => array(
                    'credits' => $Server->wsUsers[$clientID]['credits']
                )
            );
            $Server->wsSend($clientID, json_encode($returnCommand));

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
                            'targetX' => (int)$entity['targetX'],
                            'targetY' => (int)$entity['targetY'],
                            'trajX' => (int)$entity['trajX'],
                            'trajY' => (int)$entity['trajY'],
                            'speed' => 32,
                            'moveTimer' => (int)$entity['moveTimer']
                        )
                    );

                    $Server->wsSend($clientID, json_encode($returnCommand));
                }
            }

        }

        else if ($xcommand == 'ADDBULLET') {
            WSMPActions::addbulletCommand($parsed);
        }

        else if ($xcommand == 'DELETEBULLET') {
            // send a message to all clients to delete the bullet
            WSMPActions::deletebulletCommand($parsed);
        }

        else if ($xcommand == 'CHAT') {
            // send a chat message to everyone
            self::chatCommand($parsed);
        }

        else if ($xcommand == 'CHATSUBO') {
            // recall to the chatsubo
            WSNodeActions::chatsuboCommand($clientID);
        }

        if ($xcommand == 'CREATENODE') {
            WSNodeActions::createnodeCommand($parsed, $clientID);
        }

        else if ($xcommand == 'CREATEPROGRAM') {
            WSProgramActions::createprogramCommand($parsed,$clientID);
        }

        else if ($xcommand == 'DAMAGEENTITY') {
            WSMPActions::damageentityCommand($parsed);
        }

        else if ($xcommand == 'DAMAGEUSER') {
            WSCombatActions::damageuserFunction($parsed);
        }

        else if ($xcommand == 'DELETEPROGRAM') {
            WSProgramActions::deleteprogramCommand($parsed, $clientID);
        }

        else if ($xcommand == 'EXECUTEPROGRAM') {
            WSExecuteActions::executeP($parsed, $clientID);
        } 

        else if ($xcommand == 'LOADPROGRAM') {
            WSProgramActions::loadprogramCommand($parsed, $clientID);
        }

        else if ($xcommand == 'MODIFYNODE') {
            WSNodeActions::modifynodeCommand($parsed, $clientID);
        } 

        else if ($xcommand == 'MOVETO') {
            WSNodeActions::movetoCommand($parsed, $clientID);
        }

        else if ($xcommand == 'RECALL') {
            // recall to home system
            WSNodeActions::recallCommand($clientID);
        }

        else if ($xcommand == 'REMOVEENTITY') {
            // remove entity for all players in room
            WSMPActions::removeentityCommand($parsed);
        }

        else if ($xcommand == 'ROOMUPDATE') {
            WSNodeActions::roomupdateCommand($clientID);
        }

        else if ($xcommand == 'UNLOADALL') {
            // unload all programs form memory
            WSMemoryActions::unloadallCommand($clientID);
        }

        else if ($xcommand == 'UNLOADPROGRAM') {
            // unload one program from memory
            WSMemoryActions::unloadprogramCommand($parsed, $clientID);
        }

        else if ($xcommand == 'UPDATEENTITY') {
            $newX = $parsed->xvalue->newX;
            $newY = $parsed->xvalue->newY;

            $Server->wsEntities[$parsed->xvalue->entityId]['x'] = $newX;
            $Server->wsEntities[$parsed->xvalue->entityId]['y'] = $newY;

        }

        else if ($xcommand == 'UPGRADENODE') {
            WSNodeActions::upgradenodeCommand($clientID);
        }

        else if ($xcommand == 'UPGRADEPROGRAM') {
            WSProgramActions::upgradeprogramCommand($parsed, $clientID);
        }

        else if ($xcommand == 'CONNECT') {
            $Server->log('connect to accesscode');
            WSNodeActions::movetoCommand($parsed, $clientID);
        }

        else if ($xcommand == 'SPEEDMALUS') {
            $amount = $parsed->xvalue;
            $returnCommand = array(
                'xcommand' => 'SPEEDMALUS',
                'xvalue' => (int)$amount
            );
            $Server->wsSend($clientID, json_encode($returnCommand));

        }

        else if ($xcommand == 'SHOWAC') {
            $Server->log('show ac');
            $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
            $codes = $userObject->acs;

            foreach($codes as $code) {
                $Server->log(strtotime($code->expires) . ' ' . time());
                if (strtotime($code->expires) > time() &&
                    $code->condition > 0) {
                    $returnCommand = array(
                        'xcommand' => 'ADDAC',
                        'xvalue' => array(
                            'id' => $code->id,
                            'roomId' => $code->roomId,
                            'userId' => $code->userId,
                            'roomName' => $code->room->name,
                        )
                    );
                    $Server->wsSend($clientID, json_encode($returnCommand));
                }
            }

            $returnCommand = array(
                'xcommand' => 'GENERATECODEPAGES',
                'xvalue' => 0
            );
            $Server->wsSend($clientID, json_encode($returnCommand));

        }
     
    }

    public function chatCommand($parsed)
    {
        $Server = WebSocket::getInstance();

        $returnCommand = array(
            'xcommand' => 'CHAT',
            'xvalue' => $parsed->xvalue
        );

        foreach ( $Server->wsClients as $id => $client ) {
            $Server->wsSend($id, json_encode($returnCommand));
        }
    }

    // when a client connects
    static function wsOnOpen($clientID)
    {
        $Server = WebSocket::getInstance();
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
            $Server->wsSend($id, json_encode($returnCommand));
        }

        unset($Server->wsUsers[$clientID]);

        $Server->log( "$ip ($clientID) has disconnected." );

    }
}