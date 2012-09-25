<?php
class WSExecuteActions
{
	public function executeP($parsed, $clientID)
	{
        $Server = WebSocket::getInstance();
		$executeProgId = $parsed->xvalue->programId;
        $entityId = $parsed->xvalue->entityId;

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

                    if ($Server->wsPrograms[$executeProgId]['type'] == 'dataminer' && $entityId != -1) {

                        $Server->log('datamining');
                        $entityFound = $entityId;

                        if ($entityFound != 0) {

                            if ($Server->wsEntities[$entityFound]) {

                                $targetEntity = $Server->wsEntities[$entityFound];
                                $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

                                if ($targetEntity['type'] == 'fragment') {
                                    $Server->wsUsers[$clientID]['credits'] += $targetEntity['eeg'];
                                    $userObject->profile->credits += $targetEntity['eeg'];
                                }

                                if ($targetEntity['type'] == 'codebit') {
                                    $Server->wsUsers[$clientID]['snippets'] += $targetEntity['eeg'];
                                    $userObject->profile->snippets += $targetEntity['eeg'];
                                }

                                if ($targetEntity['type'] == 'accesscode') {

                                    srand();
                                    $codeType = rand(1, 100);

                                    // determine a random npc system
                                    $criteria = new CDbCriteria;

                                    if ($codeType > 50) {
                                        $criteria->condition = 'userId != 0 AND userId != ' . $Server->wsUsers[$clientID]['userId'];

                                    } else {
                                        $criteria->condition = 'userId = 0 AND id != 1 AND level = ' . $Server->wsRooms[$targetEntity['roomId']]['level'];
                                    }

                                    $areas = Area::model()->findAll($criteria);

                                    $targetArea = rand(1, count($areas));
                                    $targetCount = 1;

                                    foreach ($areas as $area) {
                                        if ($targetCount == $targetArea) {
                                            $targetAreaObject = $area;
                                            break;
                                        }
                                        $targetCount++;
                                    }

                                    if (isset($targetAreaObject)) {

                                        $criteriaRoom = new CDbCriteria;
                                        $criteriaRoom->condition = 'areaId = ' . $targetAreaObject->id . ' AND type = "io"';
                                        $targetRoom = Room::model()->find($criteriaRoom);
                                    }


                                    if (isset($targetAreaObject) && isset($targetRoom)) {
                                        //var_dump($targetAreaObject);
                                        $accesscode = new Accesscode;
                                        $accesscode->roomId = (int)$targetRoom->id;
                                        $accesscode->userId = (int)$Server->wsUsers[$clientID]['userId'];
                                        $accesscode->created = date( 'Y-m-d H:i:s', time());
                                        $accesscode->expires = date( 'Y-m-d H:i:s', time() + 3600);
                                        $accesscode->condition = rand(1,8);
                                        $accesscode->save();
                                    }
                                    else {

                                        $area = new Area;
                                        $area->userId = 0;
                                        $area->created = date( 'Y-m-d H:i:s', time());
                                        $area->accessCode = 'aaa';
                                        $area->level = $Server->wsRooms[$targetEntity['roomId']]['level'];

                                        srand();

                                        $totalCorpNames = count(Area::$corpNameArray);
                                        $randomCorpName = rand(0, $totalCorpNames - 1);
                                        $corpName = Area::$corpNameArray[$randomCorpName];

                                        $totalCorpNames = count(Area::$areaNameArray);
                                        $randomCorpName = rand(0, $totalCorpNames - 1);
                                        $corpArea = Area::$areaNameArray[$randomCorpName];

                                        $totalCorpNames = count(Area::$corpDeptArray);
                                        $randomCorpName = rand(0, $totalCorpNames - 1);
                                        $corpDept = Area::$corpDeptArray[$randomCorpName];

                                        $area->name = $corpName . ' ' . $corpDept . ' (' . $corpArea . ')';

                                        $area->save();

                                        $room = new Room;
                                        $room->areaId = $area->id;
                                        $room->userId = 0;
                                        $room->created = date( 'Y-m-d H:i:s', time());
                                        $room->level = $area->level;
                                        $room->x = 0;
                                        $room->y = 0;
                                        $room->type = 'io';
                                        $room->name = $area->name . ' (Lobby)';
                                        $room->description = 'The lobby of this corporate system.';

                                        $room->save();

                                        $currentX = 0;
                                        $currentY = 0;
                                        $currentLevel = $room->level;

                                        for ($x = 1; $x <= $area->level * 8; $x++) {

                                            srand();
                                            $randDir = rand(1, 100);
                                            if ($randDir > 50) {
                                                $currentX += 1;
                                            }
                                            else {
                                                $currentY += 1;
                                            }

                                            $randLevel = rand(1, 100);
                                            if ($randLevel > 75) {
                                                $currentLevel += 1;
                                                if ($currentLevel > 8) {
                                                    $currentLevel = 8;
                                                }
                                            }

                                            if ($x == 1) {
                                                $roomType = 'firewall';
                                                $roomName = $area->name . ' (FW)';
                                                $roomDesc = 'A firewall node.';
                                            }
                                            else {
                                                $randType = rand(1, 6);
                                                if ($randType == 1) {
                                                    $roomType = 'firewall';
                                                    $roomName = $area->name . ' (FW)';
                                                    $roomDesc = 'A firewall node.';
                                                }
                                                else if ($randType == 2) {
                                                    $roomType = 'database';
                                                    $roomName = $area->name . ' (DB)';
                                                    $roomDesc = 'A database node.';
                                                }
                                                else if ($randType == 3) {
                                                    $roomType = 'terminal';
                                                    $roomName = $area->name . ' (TR)';
                                                    $roomDesc = 'A terminal node.';
                                                }
                                                else if ($randType == 4) {
                                                    $roomType = 'coproc';
                                                    $roomName = $area->name . ' (CP)';
                                                    $roomDesc = 'A coproc node.';
                                                }
                                                else if ($randType == 5) {
                                                    $roomType = 'coding';
                                                    $roomName = $area->name . ' (CD)';
                                                    $roomDesc = 'A coding node.';
                                                }
                                                else if ($randType == 6) {
                                                    $roomType = 'hacking';
                                                    $roomName = $area->name . ' (HX)';
                                                    $roomDesc = 'A hacking node.';
                                                }
                                            }

                                            $room = new Room;
                                            $room->areaId = $area->id;
                                            $room->userId = 0;
                                            $room->created = date( 'Y-m-d H:i:s', time());
                                            $room->level = $currentLevel;
                                            $room->x = $currentX;
                                            $room->y = $currentY;
                                            $room->name = $roomName;
                                            $room->type = $roomType;
                                            $room->description = $roomDesc;
                                            $room->save();

                                        }

                                    }

                                }

                                $userObject->profile->save(false);

                                $returnCommand = array(
                                'xcommand' => 'MINEENTITY',
                                'xvalue' => $entityFound
                            );
                            $Server->wsSend($clientID, json_encode($returnCommand));
                            }
                        }

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

                    if ($Server->wsPrograms[$executeProgId]['type'] == 'gimp') {
                        $archeItem = $Server->wsPrograms[$executeProgId];
                        $entity = new Entity;
                        $entity->userId = $archeItem['userId'];
                        $entity->roomId = $Server->wsUsers[$clientID]['roomId'];
                        $entity->type = 'gimp';
                        $entity->attack = (int)$archeItem['rating'];
                        $entity->defend = (int)ceil($archeItem['rating']/2);
                        $entity->stealth = 0;
                        $entity->detect = (int)ceil($archeItem['rating']/2);
                        $entity->eeg = 10 * $archeItem['rating'];
                        $entity->credits = 0;
                        $entity->created = date( 'Y-m-d H:i:s', time());
                        $entity->x = (int)$Server->wsUsers[$clientID]['x'];
                        $entity->y = (int)$Server->wsUsers[$clientID]['y'];

                        $entity->save();

                        //var_dump($entity);

                        $Server->wsEntities[$entity->id] = array(
                            'entityId' => $entity->id, 
                            'userId' => $entity->userId, 
                            'roomId' => $entity->roomId, 
                            'type' => $entity->type, 
                            'attack' => $entity->attack, 
                            'defend' => $entity->defend, 
                            'stealth' => $entity->stealth, 
                            'detect' => $entity->detect, 
                            'eeg' => $entity->eeg, 
                            'x' => $entity->x, 
                            'y' => $entity->y, 
                            'credits' => $entity->credits, 
                            'targetX' => $entity->x, 
                            'targetY' => $entity->y, 
                            'trajX' => 0, 
                            'trajY' => 0, 
                            'speed' => 32, 
                            'moveTimer' => 0);

                        unset($Server->wsPrograms[$executeProgId]);
                        $programObject = Program::model()->findByPk($executeProgId);
                        $programObject->delete(false);

                        $returnCommand = array(
                            'xcommand' => 'UNLOADPROGRAM',
                            'xvalue' => $executeProgId
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));

                        $returnCommand = array(
                            'xcommand' => 'DELETEPROGRAM',
                            'xvalue' => $executeProgId
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));

                    }

                    if ($Server->wsPrograms[$executeProgId]['type'] == 'dropline') {
                        WSNodeActions::recallCommand($clientID);
                        unset($Server->wsPrograms[$executeProgId]);
                        $programObject = Program::model()->findByPk($executeProgId);
                        $programObject->delete(false);

                        $returnCommand = array(
                            'xcommand' => 'UNLOADPROGRAM',
                            'xvalue' => $executeProgId
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));

                        $returnCommand = array(
                            'xcommand' => 'DELETEPROGRAM',
                            'xvalue' => $executeProgId
                        );
                        $Server->wsSend($clientID, json_encode($returnCommand));
                    }

                    if ($Server->wsPrograms[$executeProgId]['type'] == 'logicbomb') {

                        $returnCommand = array(
                            'xcommand' => 'ADDBOMB',
                            'xvalue' => array(
                                'userId' => (int)$Server->wsUsers[$clientID]['userId'],
                                'x' => (int)$Server->wsUsers[$clientID]['x'],
                                'y' => (int)$Server->wsUsers[$clientID]['y'],
                                'roomId' => (int)$Server->wsUsers[$clientID]['roomId'],
                                'damage' => (int)$Server->wsPrograms[$executeProgId]['rating'] * 10
                            )
                        );
                        
                        foreach ( $Server->wsClients as $id => $client ) {
                            if (isset($Server->wsUsers[$id]) && $Server->wsUsers[$id]['roomId'] == $Server->wsUsers[$clientID]['roomId']) {
                                $Server->wsSend($id, json_encode($returnCommand));
                            }
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
}