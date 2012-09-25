<?php
class WSProgramActions
{
	public function createprogramCommand($parsed, $clientID)
	{
		$Server = WebSocket::getInstance();
		$programType = $parsed->xvalue->type;
        $room = Room::model()->findByPk($Server->wsUsers[$clientID]['roomId']);
        $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);

        switch($programType) {

            case 'stealth':
            case 'detect':
            case 'defend':
            case 'attack':
            case 'eegbooster':
            case 'scanner':
            case 'dataminer':
            case 'dropline':
                $creditCost = 100;
                $snippetCost = 10;
            break;

            case 'antivirus':
                $creditCost = 250;
                $snippetCost = 25;
            break;

            case 'radblaster';
                $creditCost = 100000;
                $snippetCost = 1000;
            break;

            case 'gimp':
            case 'logicbomb':
                $creditcost = 1000;
                $snippetCost = 100;
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

                // $returnCommand = array(
                //     'xcommand' => 'CREDITSCHANGE',
                //     'xvalue' => $creditCost
                // );
                // $Server->wsSend($clientID, json_encode($returnCommand));

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
                    $newModel->name = 'eeg booster v1.0';
                    $newModel->description = 'This program boosts your EEG value.';
                }

                if ($programType == 'scanner') {
                    $newModel->name = 'scanner v1.0';
                    $newModel->description = 'This program allows you to scan the node.';
                }

                if ($programType == 'dataminer') {
                    $newModel->condition = 10;
                    $newModel->name = 'dataminer v1.0';
                    $newModel->description = 'This program can be used on entities to gain their data.';
                }

                if ($programType == 'radblaster') {
                    $newModel->name = 'radblaster v1.0';
                    $newModel->description = 'One of the most sophisticated cyberweapons out there. It allows the user to shoot into any direction.';
                }

                if ($programType == 'gimp') {
                    $newModel->condition = 1;
                    $newModel->name = 'gimp v1.0';
                    $newModel->description = 'A basic daemon that attacks virii.';
                }

                if ($programType == 'dropline') {
                    $newModel->condition = 1;
                    $newModel->name = 'dropline v1.0';
                    $newModel->description = 'This is an emergency recall back to your home system.';
                }

                if ($programType == 'logicbomb') {
                    $newModel->name = 'logicbomb v1.0';
                    $newModel->description = 'You can drop this bomb at your location and cause lots of damage.';
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

	public function deleteprogramCommand($parsed, $clientID)
	{
		$Server = WebSocket::getInstance();
		$programToDelete = $parsed->xvalue;
        unset($Server->wsPrograms[$programToDelete]);
        $programObject = Program::model()->findByPk($programToDelete);
        $programObject->delete(false);

        $returnCommand = array(
            'xcommand' => 'SYSMSG',
            'xvalue' => '> program deleted'
        );
        $Server->wsSend($clientID, json_encode($returnCommand));
	}

    public function loadprogramCommand($parsed, $clientID)
    {
        $Server = WebSocket::getInstance();
        $loadProgId = $parsed->xvalue->id;

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

        if ($Server->wsPrograms[$loadProgId]['type'] == 'radblaster') {
            $returnCommand = array(
                'xcommand' => 'CHANGEFIREMODE',
                'xvalue' => 1
            );
            $Server->wsSend($clientID, json_encode($returnCommand));

        }

        $returnCommand = array(
            'xcommand' => 'SYSMSG',
            'xvalue' => '> program loaded'
        );
        $Server->wsSend($clientID, json_encode($returnCommand));
    }

    public function upgradeprogramCommand($parsed, $clientID)
    {
        $Server = WebSocket::getInstance();
        if (isset($parsed->xvalue->id)) {
            $program = Program::model()->findByPk($parsed->xvalue->id);
            $upgrader = $Server->wsUsers[$clientID];
            
            $creditsCost = ($program->rating * $program->rating) * 1000;
            $snippetsCost = ($program->rating * $program->rating) * 10;

            if ($program->maxUpgrades > $program->upgrades &&
                $creditsCost <= $upgrader['credits'] &&
                $snippetsCost <= $upgrader['snippets'] &&
                $program->rating < 8
            ) {
                $userObject = User::model()->findByPk($Server->wsUsers[$clientID]['userId']);
                $userObject->profile->credits -= $creditsCost;
                $userObject->profile->snippets -= $snippetsCost;
                $userObject->profile->save(false);
                // $returnCommand = array(
                //     'xcommand' => 'CREDITSCHANGE',
                //     'xvalue' => $creditsCost
                // );
                // $Server->wsSend($clientID, json_encode($returnCommand));
                $Server->wsUsers[$clientID]['credits'] -= $creditsCost;
                $returnCommand = array(
                    'xcommand' => 'SNIPPETSCHANGE',
                    'xvalue' => $snippetsCost
                );
                $Server->wsSend($clientID, json_encode($returnCommand));
                $Server->wsUsers[$clientID]['snippets'] -= $snippetsCost;

                $program->rating += 1;
                $program->upgrades += 1;
                $newProgName = substr_replace($program->name ,"",-3);
                $program->name = $newProgName . $program->rating . '.0';
                
                $program->save(false);

                $Server->wsPrograms[$program->id]['rating'] += 1;
                $Server->wsPrograms[$program->id]['upgrades'] += 1;
                $Server->wsPrograms[$program->id]['name'] = $newProgName . $program->rating . '.0';

                $returnCommand = array(
                    'xcommand' => 'UPGRADEITEM',
                    'xvalue' => array(
                        'itemId' => $program->id,
                        'newName' => $newProgName . $program->rating . '.0',
                    )
                );
                $Server->wsSend($clientID, json_encode($returnCommand));

                $returnCommand = array(
                    'xcommand' => 'SYSMSG',
                    'xvalue' => '> program upgraded'
                );
                $Server->wsSend($clientID, json_encode($returnCommand));


            }

        }

        //var_dump($program->name);
    }

}