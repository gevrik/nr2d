<?php
class WSMemoryActions
{

	public function unloadprogramCommand($parsed, $clientID)
	{
		$Server = WebSocket::getInstance();
		$programId = $parsed->xvalue->id;

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

	public function unloadallCommand($clientID)
	{
		$Server = WebSocket::getInstance();
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

}