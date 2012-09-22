<?php

/*
	Based on PHP WebSocket Server 0.2
	 - http://code.google.com/p/php-websocket-server/
	 - http://code.google.com/p/php-websocket-server/wiki/Scripting

	WebSocket Protocol 07
	 - http://tools.ietf.org/html/draft-ietf-hybi-thewebsocketprotocol-07
	 - Supported by Firefox 6 (30/08/2011)

	Whilst a big effort is made to follow the protocol documentation, the current script version may unknowingly differ.
	Please report any bugs you may find, all feedback and questions are welcome!
*/


class WebSocket
{
	// maximum amount of clients that can be connected at one time
	const WS_MAX_CLIENTS = 100;

	// maximum amount of clients that can be connected at one time on the same IP v4 address
	const WS_MAX_CLIENTS_PER_IP = 15;

	// amount of seconds a client has to send data to the server, before a ping request is sent to the client,
	// if the client has not completed the opening handshake, the ping request is skipped and the client connection is closed
	const WS_TIMEOUT_RECV = 10;

	// amount of seconds a client has to reply to a ping request, before the client connection is closed
	const WS_TIMEOUT_PONG = 5;

	// the maximum length, in bytes, of a frame's payload data (a message consists of 1 or more frames), this is also internally limited to 2,147,479,538
	const WS_MAX_FRAME_PAYLOAD_RECV = 100000;

	// the maximum length, in bytes, of a message's payload data, this is also internally limited to 2,147,483,647
	const WS_MAX_MESSAGE_PAYLOAD_RECV = 500000;

	private static $instance;

	private function __construct()
    {

    }

    static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	// internal
	const WS_FIN =  128;
	const WS_MASK = 128;

	const WS_OPCODE_CONTINUATION = 0;
	const WS_OPCODE_TEXT =         1;
	const WS_OPCODE_BINARY =       2;
	const WS_OPCODE_CLOSE =        8;
	const WS_OPCODE_PING =         9;
	const WS_OPCODE_PONG =         10;

	const WS_PAYLOAD_LENGTH_16 = 126;
	const WS_PAYLOAD_LENGTH_63 = 127;

	const WS_READY_STATE_CONNECTING = 0;
	const WS_READY_STATE_OPEN =       1;
	const WS_READY_STATE_CLOSING =    2;
	const WS_READY_STATE_CLOSED =     3;

	const WS_STATUS_NORMAL_CLOSE =             1000;
	const WS_STATUS_GONE_AWAY =                1001;
	const WS_STATUS_PROTOCOL_ERROR =           1002;
	const WS_STATUS_UNSUPPORTED_MESSAGE_TYPE = 1003;
	const WS_STATUS_MESSAGE_TOO_BIG =          1004;

	const WS_STATUS_TIMEOUT = 3000;

	// global vars
	public $wsClients       = array();
	public $wsRead          = array();
	public $wsClientCount   = 0;
	public $wsClientIPCount = array();
	public $wsOnEvents      = array();
	public $wsUsers			= array();
	public $wsEntities		= array();
	public $wsRooms			= array();
	public $now				= 0;
	public $wsPrograms		= array();
	public $wsBullets		= array();

	/*
		$this->wsClients[ integer ClientID ] = array(
			0 => resource  Socket,                            // client socket
			1 => string    MessageBuffer,                     // a blank string when there's no incoming frames
			2 => integer   ReadyState,                        // between 0 and 3
			3 => integer   LastRecvTime,                      // set to time() when the client is added
			4 => int/false PingSentTime,                      // false when the server is not waiting for a pong
			5 => int/false CloseStatus,                       // close status that wsOnClose() will be called with
			6 => integer   IPv4,                              // client's IP stored as a signed long, retrieved from ip2long()
			7 => int/false FramePayloadDataLength,            // length of a frame's payload data, reset to false when all frame data has been read (cannot reset to 0, to allow reading of mask key)
			8 => integer   FrameBytesRead,                    // amount of bytes read for a frame, reset to 0 when all frame data has been read
			9 => string    FrameBuffer,                       // joined onto end as a frame's data comes in, reset to blank string when all frame data has been read
			10 => integer  MessageOpcode,                     // stored by the first frame for fragmented messages, default value is 0
			11 => integer  MessageBufferLength                // the payload data length of MessageBuffer
		)

		$wsRead[ integer ClientID ] = resource Socket         // this one-dimensional array is used for socket_select()
															  // $wsRead[ 0 ] is the socket listening for incoming client connections

		$wsClientCount = integer ClientCount                  // amount of clients currently connected

		$wsClientIPCount[ integer IP ] = integer ClientCount  // amount of clients connected per IP v4 address
	*/

	// server state functions
	function wsStartServer($host, $port) {
		date_default_timezone_set('Europe/Berlin');
		if (isset($this->wsRead[0])) return false;

		if (!$this->wsRead[0] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) {
			return false;
		}
		if (!socket_set_option($this->wsRead[0], SOL_SOCKET, SO_REUSEADDR, 1)) {
			socket_close($this->wsRead[0]);
			return false;
		}
		if (!socket_bind($this->wsRead[0], $host, $port)) {
			socket_close($this->wsRead[0]);
			return false;
		}
		if (!socket_listen($this->wsRead[0], 10)) {
			socket_close($this->wsRead[0]);
			return false;
		}

		$this->now = time();

        $this->log($this->now);

		$rooms = Room::model()->findAll();

		foreach ($rooms as $room) {

			$this->wsRooms[$room->id] = array('roomId' => $room->id, 'areaId' => $room->areaId, 'userId' => $room->userId, 'type' => $room->type, 'level' => $room->level, 'x' => $room->x, 'y' => $room->y, 'name' => $room->name, 'entityAmount' => $room->entityAmount);

			$entityAmount = $room->entityAmount;

			if ($entityAmount < $room->level && $room->type == 'firewall') {
				$this->wsRooms[$room->id]['entityAmount'] += 1;
				$entity = new Entity;
				$entity->userId = $room->userId;
				$entity->roomId = $room->id;
				$entity->type = 'bouncer';
				$entity->created = date( 'Y-m-d H:i:s', time());
				$entity->attack = 0;
				$entity->defend = 0;
				$entity->stealth = 0;
				$entity->detect = 1;
				$entity->eeg = 10;
				$entity->x = rand(2, 14) * 32;
				$entity->y = rand(2, 14) * 32;
				$entity->credits = 0;

				$entity->save();

			}

			if ($entityAmount < $room->level && $room->type == 'terminal') {
				$this->wsRooms[$room->id]['entityAmount'] += 1;
				$entity = new Entity;
				$entity->userId = $room->userId;
				$entity->roomId = $room->id;
				$entity->type = 'user';
				$entity->created = date( 'Y-m-d H:i:s', time());
				$entity->attack = 0;
				$entity->defend = 1;
				$entity->stealth = 0;
				$entity->detect = 0;
				$entity->eeg = 10;
				$entity->x = rand(2, 14) * 32;
				$entity->y = rand(2, 14) * 32;
				$entity->credits = 0;

				$entity->save();

			}

			if ($entityAmount < $room->level && $room->type == 'database') {
				$this->wsRooms[$room->id]['entityAmount'] += 1;
				$entity = new Entity;
				$entity->userId = $room->userId;
				$entity->roomId = $room->id;
				$entity->type = 'fragment';
				$entity->created = date( 'Y-m-d H:i:s', time());
				$entity->attack = 0;
				$entity->defend = 0;
				$entity->stealth = 1;
				$entity->detect = 0;
				$entity->eeg = 10;
				$entity->x = rand(2, 14) * 32;
				$entity->y = rand(2, 14) * 32;
				$entity->credits = 0;

				$entity->save();

			}

			if ($entityAmount < $room->level && $room->type == 'coproc') {
				$this->wsRooms[$room->id]['entityAmount'] += 1;
				$entity = new Entity;
				$entity->userId = $room->userId;
				$entity->roomId = $room->id;
				$entity->type = 'worker';
				$entity->created = date( 'Y-m-d H:i:s', time());
				$entity->attack = 0;
				$entity->defend = 1;
				$entity->stealth = 0;
				$entity->detect = 0;
				$entity->eeg = 10;
				$entity->x = rand(2, 14) * 32;
				$entity->y = rand(2, 14) * 32;
				$entity->credits = 0;

				$entity->save();

			}

			if ($entityAmount < $room->level && $room->type == 'coding') {
				$this->wsRooms[$room->id]['entityAmount'] += 1;
				$entity = new Entity;
				$entity->userId = $room->userId;
				$entity->roomId = $room->id;
				$entity->type = 'codebit';
				$entity->created = date( 'Y-m-d H:i:s', time());
				$entity->attack = 0;
				$entity->defend = 0;
				$entity->stealth = 1;
				$entity->detect = 0;
				$entity->eeg = 10;
				$entity->x = rand(2, 14) * 32;
				$entity->y = rand(2, 14) * 32;
				$entity->credits = 0;

				$entity->save();

			}

		}

		$entities = Entity::model()->findAll();

		foreach ($entities as $entity) {
			$this->wsEntities[$entity->id] = array(
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
				'moveTimer' => 0
			);
		}

		$programs = Program::model()->findAll();

		foreach ($programs as $program) {
			$this->wsPrograms[$program->id] = array(
				'programId' => $program->id, 
				'userId' => $program->userId, 
				'type' => $program->type, 
				'rating' => $program->rating, 
				'condition' => $program->condition, 
				'maxUpgrades' => $program->maxUpgrades, 
				'upgrades' => $program->upgrades, 
				'name' => CHtml::encode($program->name), 
				'loaded' => 0);
		}

		$write = array();
		$except = array();

		$nextPingCheck = time() + 1;
		$nextSpawnCheck = time() + 300;
		$nextMobCheck = time() + 2;
		while (isset($this->wsRead[0])) {
			$changed = $this->wsRead;
			$result = socket_select($changed, $write, $except, 1);

			if ($result === false) {
				socket_close($this->wsRead[0]);
				return false;
			}
			elseif ($result > 0) {
				foreach ($changed as $clientID => $socket) {
					if ($clientID != 0) {
						// client socket changed
						$buffer = '';
						$bytes = @socket_recv($socket, $buffer, 4096, 0);

						if ($bytes === false) {
							// error on recv, remove client socket (will check to send close frame)
							$this->wsSendClientClose($clientID, self::WS_STATUS_PROTOCOL_ERROR);
						}
						elseif ($bytes > 0) {
							// process handshake or frame(s)
							if (!$this->wsProcessClient($clientID, $buffer, $bytes)) {
								$this->wsSendClientClose($clientID, self::WS_STATUS_PROTOCOL_ERROR);
							}
						}
						else {
							// 0 bytes received from client, meaning the client closed the TCP connection
							$this->wsRemoveClient($clientID);
						}
					}
					else {
						// listen socket changed
						$client = socket_accept($this->wsRead[0]);
						if ($client !== false) {
							// fetch client IP as integer
							$clientIP = '';
							$result = socket_getpeername($client, $clientIP);
							$clientIP = ip2long($clientIP);

							if ($result !== false && $this->wsClientCount < self::WS_MAX_CLIENTS && (!isset($this->wsClientIPCount[$clientIP]) || $this->wsClientIPCount[$clientIP] < self::WS_MAX_CLIENTS_PER_IP)) {
								$this->wsAddClient($client, $clientIP);
							}
							else {
								socket_close($client);
							}
						}
					}
				}
			}

			if (time() >= $nextPingCheck) {
				$this->wsCheckIdleClients();
				$nextPingCheck = time() + 1;
			}

			if (time() >= $nextSpawnCheck) {
				$this->wsCronStuff();
				$nextSpawnCheck = time() + 300;
			}

			if (time() >= $nextMobCheck) {
				$this->wsMobStuff();
				$nextMobCheck = time() + 2;
			}

		}

		return true; // returned when wsStopServer() is called
	}
	function wsStopServer() {
		// check if server is not running
		if (!isset($this->wsRead[0])) return false;

		// close all client connections
		foreach ($this->wsClients as $clientID => $client) {
			// if the client's opening handshake is complete, tell the client the server is 'going away'
			if ($client[2] != self::WS_READY_STATE_CONNECTING) {
				$this->wsSendClientClose($clientID, self::WS_STATUS_GONE_AWAY);
			}
			socket_close($client[0]);
		}

		// close the socket which listens for incoming clients
		socket_close($this->wsRead[0]);

		// reset variables
		$this->wsRead          = array();
		$this->wsClients       = array();
		$this->wsClientCount   = 0;
		$this->wsClientIPCount = array();
		$this->wsUsers		   = array();
		$this->wsEntities	   = array();

		return true;
	}

	// client timeout functions
	function wsCheckIdleClients() {
		$time = time();
		foreach ($this->wsClients as $clientID => $client) {
			if ($client[2] != self::WS_READY_STATE_CLOSED) {
				// client ready state is not closed
				if ($client[4] !== false) {
					// ping request has already been sent to client, pending a pong reply
					if ($time >= $client[4] + self::WS_TIMEOUT_PONG) {
						// client didn't respond to the server's ping request in self::WS_TIMEOUT_PONG seconds
						$this->wsSendClientClose($clientID, self::WS_STATUS_TIMEOUT);
						$this->wsRemoveClient($clientID);
					}
				}
				elseif ($time >= $client[3] + self::WS_TIMEOUT_RECV) {
					// last data was received >= self::WS_TIMEOUT_RECV seconds ago
					if ($client[2] != self::WS_READY_STATE_CONNECTING) {
						// client ready state is open or closing
						$this->wsClients[$clientID][4] = time();
						$this->wsSendClientMessage($clientID, self::WS_OPCODE_PING, '');
					}
					else {
						// client ready state is connecting
						$this->wsRemoveClient($clientID);
					}
				}
			}
		}
	}

	// client existence functions
	function wsAddClient($socket, $clientIP) {
		// increase amount of clients connected
		$this->wsClientCount++;

		// increase amount of clients connected on this client's IP
		if (isset($this->wsClientIPCount[$clientIP])) {
			$this->wsClientIPCount[$clientIP]++;
		}
		else {
			$this->wsClientIPCount[$clientIP] = 1;
		}

		// fetch next client ID
		$clientID = $this->wsGetNextClientID();

		// store initial client data
		$this->wsClients[$clientID] = array($socket, '', self::WS_READY_STATE_CONNECTING, time(), false, 0, $clientIP, false, 0, '', 0, 0);

		// store socket - used for socket_select()
		$this->wsRead[$clientID] = $socket;
	}
	function wsRemoveClient($clientID) {
		// fetch close status (which could be false), and call wsOnClose
		$closeStatus = $this->wsClients[$clientID][5];
		if ( array_key_exists('close', $this->wsOnEvents) )
			foreach ( $this->wsOnEvents['close'] as $array ) {
				call_user_func_array($array, array($clientID, $closeStatus));
			}

		// close socket
		$socket = $this->wsClients[$clientID][0];
		socket_close($socket);

		// decrease amount of clients connected on this client's IP
		$clientIP = $this->wsClients[$clientID][6];
		if ($this->wsClientIPCount[$clientIP] > 1) {
			$this->wsClientIPCount[$clientIP]--;
		}
		else {
			unset($this->wsClientIPCount[$clientIP]);
		}

		// decrease amount of clients connected
		$this->wsClientCount--;

		// remove socket and client data from arrays
		unset($this->wsRead[$clientID], $this->wsClients[$clientID]);
	}

	// client data functions
	function wsGetNextClientID() {
		$i = 1; // starts at 1 because 0 is the listen socket
		while (isset($this->wsRead[$i])) $i++;
		return $i;
	}
	function wsGetClientSocket($clientID) {
		return $this->wsClients[$clientID][0];
	}

	// client read functions
	function wsProcessClient($clientID, &$buffer, $bufferLength) {
		if ($this->wsClients[$clientID][2] == self::WS_READY_STATE_OPEN) {
			// handshake completed
			$result = $this->wsBuildClientFrame($clientID, $buffer, $bufferLength);
		}
		elseif ($this->wsClients[$clientID][2] == self::WS_READY_STATE_CONNECTING) {
			// handshake not completed
			$result = $this->wsProcessClientHandshake($clientID, $buffer);
			if ($result) {
				$this->wsClients[$clientID][2] = self::WS_READY_STATE_OPEN;

				if ( array_key_exists('open', $this->wsOnEvents) )
					foreach ( $this->wsOnEvents['open'] as $array) {
						call_user_func($array, $clientID);
					}
			} 
		}
		else {
			// ready state is set to closed
			$result = false;
		}

		return $result;
	}
	function wsBuildClientFrame($clientID, &$buffer, $bufferLength) {
		// increase number of bytes read for the frame, and join buffer onto end of the frame buffer
		$this->wsClients[$clientID][8] += $bufferLength;
		$this->wsClients[$clientID][9] .= $buffer;

		// check if the length of the frame's payload data has been fetched, if not then attempt to fetch it from the frame buffer
		if ($this->wsClients[$clientID][7] !== false || $this->wsCheckSizeClientFrame($clientID) == true) {
			// work out the header length of the frame
			$headerLength = ($this->wsClients[$clientID][7] <= 125 ? 0 : ($this->wsClients[$clientID][7] <= 65535 ? 2 : 8)) + 6;

			// check if all bytes have been received for the frame
			$frameLength = $this->wsClients[$clientID][7] + $headerLength;
			if ($this->wsClients[$clientID][8] >= $frameLength) {
				// check if too many bytes have been read for the frame (they are part of the next frame)
				$nextFrameBytesLength = $this->wsClients[$clientID][8] - $frameLength;
				if ($nextFrameBytesLength > 0) {
					$this->wsClients[$clientID][8] -= $nextFrameBytesLength;
					$nextFrameBytes = substr($this->wsClients[$clientID][9], $frameLength);
					$this->wsClients[$clientID][9] = substr($this->wsClients[$clientID][9], 0, $frameLength);
				}

				// process the frame
				$result = $this->wsProcessClientFrame($clientID);

				// check if the client wasn't removed, then reset frame data
				if (isset($this->wsClients[$clientID])) {
					$this->wsClients[$clientID][7] = false;
					$this->wsClients[$clientID][8] = 0;
					$this->wsClients[$clientID][9] = '';
				}

				// if there's no extra bytes for the next frame, or processing the frame failed, return the result of processing the frame
				if ($nextFrameBytesLength <= 0 || !$result) return $result;

				// build the next frame with the extra bytes
				return $this->wsBuildClientFrame($clientID, $nextFrameBytes, $nextFrameBytesLength);
			}
		}

		return true;
	}
	function wsCheckSizeClientFrame($clientID) {
		// check if at least 2 bytes have been stored in the frame buffer
		if ($this->wsClients[$clientID][8] > 1) {
			// fetch payload length in byte 2, max will be 127
			$payloadLength = ord(substr($this->wsClients[$clientID][9], 1, 1)) & 127;

			if ($payloadLength <= 125) {
				// actual payload length is <= 125
				$this->wsClients[$clientID][7] = $payloadLength;
			}
			elseif ($payloadLength == 126) {
				// actual payload length is <= 65,535
				if (substr($this->wsClients[$clientID][9], 3, 1) !== false) {
					// at least another 2 bytes are set
					$payloadLengthExtended = substr($this->wsClients[$clientID][9], 2, 2);
					$array = unpack('na', $payloadLengthExtended);
					$this->wsClients[$clientID][7] = $array['a'];
				}
			}
			else {
				// actual payload length is > 65,535
				if (substr($this->wsClients[$clientID][9], 9, 1) !== false) {
					// at least another 8 bytes are set
					$payloadLengthExtended = substr($this->wsClients[$clientID][9], 2, 8);

					// check if the frame's payload data length exceeds 2,147,483,647 (31 bits)
					// the maximum integer in PHP is "usually" this number. More info: http://php.net/manual/en/language.types.integer.php
					$payloadLengthExtended32_1 = substr($payloadLengthExtended, 0, 4);
					$array = unpack('Na', $payloadLengthExtended32_1);
					if ($array['a'] != 0 || ord(substr($payloadLengthExtended, 4, 1)) & 128) {
						$this->wsSendClientClose($clientID, self::WS_STATUS_MESSAGE_TOO_BIG);
						return false;
					}

					// fetch length as 32 bit unsigned integer, not as 64 bit
					$payloadLengthExtended32_2 = substr($payloadLengthExtended, 4, 4);
					$array = unpack('Na', $payloadLengthExtended32_2);

					// check if the payload data length exceeds 2,147,479,538 (2,147,483,647 - 14 - 4095)
					// 14 for header size, 4095 for last recv() next frame bytes
					if ($array['a'] > 2147479538) {
						$this->wsSendClientClose($clientID, self::WS_STATUS_MESSAGE_TOO_BIG);
						return false;
					}

					// store frame payload data length
					$this->wsClients[$clientID][7] = $array['a'];
				}
			}

			// check if the frame's payload data length has now been stored
			if ($this->wsClients[$clientID][7] !== false) {

				// check if the frame's payload data length exceeds self::WS_MAX_FRAME_PAYLOAD_RECV
				if ($this->wsClients[$clientID][7] > self::WS_MAX_FRAME_PAYLOAD_RECV) {
					$this->wsClients[$clientID][7] = false;
					$this->wsSendClientClose($clientID, self::WS_STATUS_MESSAGE_TOO_BIG);
					return false;
				}

				// check if the message's payload data length exceeds 2,147,483,647 or self::WS_MAX_MESSAGE_PAYLOAD_RECV
				// doesn't apply for control frames, where the payload data is not internally stored
				$controlFrame = (ord(substr($this->wsClients[$clientID][9], 0, 1)) & 8) == 8;
				if (!$controlFrame) {
					$newMessagePayloadLength = $this->wsClients[$clientID][11] + $this->wsClients[$clientID][7];
					if ($newMessagePayloadLength > self::WS_MAX_MESSAGE_PAYLOAD_RECV || $newMessagePayloadLength > 2147483647) {
						$this->wsSendClientClose($clientID, self::WS_STATUS_MESSAGE_TOO_BIG);
						return false;
					}
				}

				return true;
			}
		}

		return false;
	}
	function wsProcessClientFrame($clientID) {
		// store the time that data was last received from the client
		$this->wsClients[$clientID][3] = time();

		// fetch frame buffer
		$buffer = &$this->wsClients[$clientID][9];

		// check at least 6 bytes are set (first 2 bytes and 4 bytes for the mask key)
		if (substr($buffer, 5, 1) === false) return false;

		// fetch first 2 bytes of header
		$octet0 = ord(substr($buffer, 0, 1));
		$octet1 = ord(substr($buffer, 1, 1));

		$fin = $octet0 & self::WS_FIN;
		$opcode = $octet0 & 15;

		$mask = $octet1 & self::WS_MASK;
		if (!$mask) return false; // close socket, as no mask bit was sent from the client

		// fetch byte position where the mask key starts
		$seek = $this->wsClients[$clientID][7] <= 125 ? 2 : ($this->wsClients[$clientID][7] <= 65535 ? 4 : 10);

		// read mask key
		$maskKey = substr($buffer, $seek, 4);

		$array = unpack('Na', $maskKey);
		$maskKey = $array['a'];
		$maskKey = array(
			$maskKey >> 24,
			($maskKey >> 16) & 255,
			($maskKey >> 8) & 255,
			$maskKey & 255
		);
		$seek += 4;

		// decode payload data
		if (substr($buffer, $seek, 1) !== false) {
			$data = str_split(substr($buffer, $seek));
			foreach ($data as $key => $byte) {
				$data[$key] = chr(ord($byte) ^ ($maskKey[$key % 4]));
			}
			$data = implode('', $data);
		}
		else {
			$data = '';
		}

		// check if this is not a continuation frame and if there is already data in the message buffer
		if ($opcode != self::WS_OPCODE_CONTINUATION && $this->wsClients[$clientID][11] > 0) {
			// clear the message buffer
			$this->wsClients[$clientID][11] = 0;
			$this->wsClients[$clientID][1] = '';
		}

		// check if the frame is marked as the final frame in the message
		if ($fin == self::WS_FIN) {
			// check if this is the first frame in the message
			if ($opcode != self::WS_OPCODE_CONTINUATION) {
				// process the message
				return $this->wsProcessClientMessage($clientID, $opcode, $data, $this->wsClients[$clientID][7]);
			}
			else {
				// increase message payload data length
				$this->wsClients[$clientID][11] += $this->wsClients[$clientID][7];

				// push frame payload data onto message buffer
				$this->wsClients[$clientID][1] .= $data;

				// process the message
				$result = $this->wsProcessClientMessage($clientID, $this->wsClients[$clientID][10], $this->wsClients[$clientID][1], $this->wsClients[$clientID][11]);

				// check if the client wasn't removed, then reset message buffer and message opcode
				if (isset($this->wsClients[$clientID])) {
					$this->wsClients[$clientID][1] = '';
					$this->wsClients[$clientID][10] = 0;
					$this->wsClients[$clientID][11] = 0;
				}

				return $result;
			}
		}
		else {
			// check if the frame is a control frame, control frames cannot be fragmented
			if ($opcode & 8) return false;

			// increase message payload data length
			$this->wsClients[$clientID][11] += $this->wsClients[$clientID][7];

			// push frame payload data onto message buffer
			$this->wsClients[$clientID][1] .= $data;

			// if this is the first frame in the message, store the opcode
			if ($opcode != self::WS_OPCODE_CONTINUATION) {
				$this->wsClients[$clientID][10] = $opcode;
			}
		}

		return true;
	}
	function wsProcessClientMessage($clientID, $opcode, &$data, $dataLength) {
		// check opcodes
		if ($opcode == self::WS_OPCODE_PING) {
			// received ping message
			return $this->wsSendClientMessage($clientID, self::WS_OPCODE_PONG, $data);
		}
		elseif ($opcode == self::WS_OPCODE_PONG) {
			// received pong message (it's valid if the server did not send a ping request for this pong message)
			if ($this->wsClients[$clientID][4] !== false) {
				$this->wsClients[$clientID][4] = false;
			}
		}
		elseif ($opcode == self::WS_OPCODE_CLOSE) {
			// received close message
			if (substr($data, 1, 1) !== false) {
				$array = unpack('na', substr($data, 0, 2));
				$status = $array['a'];
			}
			else {
				$status = false;
			}

			if ($this->wsClients[$clientID][2] == self::WS_READY_STATE_CLOSING) {
				// the server already sent a close frame to the client, this is the client's close frame reply
				// (no need to send another close frame to the client)
				$this->wsClients[$clientID][2] = self::WS_READY_STATE_CLOSED;
			}
			else {
				// the server has not already sent a close frame to the client, send one now
				$this->wsSendClientClose($clientID, self::WS_STATUS_NORMAL_CLOSE);
			}

			$this->wsRemoveClient($clientID);
		}
		elseif ($opcode == self::WS_OPCODE_TEXT || $opcode == self::WS_OPCODE_BINARY) {
			if ( array_key_exists('message', $this->wsOnEvents) )
				foreach ( $this->wsOnEvents['message'] as $array)
					call_user_func_array($array, array($clientID, $data, $dataLength, self::WS_OPCODE_BINARY));
		}
		else {
			// unknown opcode
			return false;
		}

		return true;
	}
	function wsProcessClientHandshake($clientID, &$buffer) {
		// fetch headers and request line
		$sep = strpos($buffer, "\r\n\r\n");
		if (!$sep) return false;

		$headers = explode("\r\n", substr($buffer, 0, $sep));
		$headersCount = sizeof($headers); // includes request line
		if ($headersCount < 1) return false;

		// fetch request and check it has at least 3 parts (space tokens)
		$request = &$headers[0];
		$requestParts = explode(' ', $request);
		$requestPartsSize = sizeof($requestParts);
		if ($requestPartsSize < 3) return false;

		// check request method is GET
		if (strtoupper($requestParts[0]) != 'GET') return false;

		// check request HTTP version is at least 1.1
		$httpPart = &$requestParts[$requestPartsSize - 1];
		$httpParts = explode('/', $httpPart);
		if (!isset($httpParts[1]) || (float) $httpParts[1] < 1.1) return false;

		// store headers into a keyed array: array[headerKey] = headerValue
		$headersKeyed = array();
		for ($i=1; $i<$headersCount; $i++) {
			$parts = explode(':', $headers[$i]);
			if (!isset($parts[1])) return false;

			$headersKeyed[trim($parts[0])] = trim($parts[1]);
		}

		// check Host header was received
		if (!isset($headersKeyed['Host'])) return false;

		// check Sec-WebSocket-Key header was received and decoded value length is 16
		if (!isset($headersKeyed['Sec-WebSocket-Key'])) return false;
		$key = $headersKeyed['Sec-WebSocket-Key'];
		if (strlen(base64_decode($key)) != 16) return false;

		// check Sec-WebSocket-Version header was received and value is 7
		if (!isset($headersKeyed['Sec-WebSocket-Version']) || (int) $headersKeyed['Sec-WebSocket-Version'] < 7) return false; // should really be != 7, but Firefox 7 beta users send 8

		// work out hash to use in Sec-WebSocket-Accept reply header
		$hash = base64_encode(sha1($key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

		// build headers
		$headers = array(
			'HTTP/1.1 101 Switching Protocols',
			'Upgrade: websocket',
			'Connection: Upgrade',
			'Sec-WebSocket-Accept: '.$hash
		);
		$headers = implode("\r\n", $headers)."\r\n\r\n";

		// send headers back to client
		$socket = $this->wsClients[$clientID][0];

		$left = strlen($headers);
		do {
			$sent = @socket_send($socket, $headers, $left, 0);
			if ($sent === false) return false;

			$left -= $sent;
			if ($sent > 0) $headers = substr($headers, $sent);
		}
		while ($left > 0);

		return true;
	}

	// client write functions
	function wsSendClientMessage($clientID, $opcode, $message) {
		// check if client ready state is already closing or closed
		if ($this->wsClients[$clientID][2] == self::WS_READY_STATE_CLOSING || $this->wsClients[$clientID][2] == self::WS_READY_STATE_CLOSED) return true;

		// fetch message length
		$messageLength = strlen($message);

		// set max payload length per frame
		$bufferSize = 4096;

		// work out amount of frames to send, based on $bufferSize
		$frameCount = ceil($messageLength / $bufferSize);
		if ($frameCount == 0) $frameCount = 1;

		// set last frame variables
		$maxFrame = $frameCount - 1;
		$lastFrameBufferLength = ($messageLength % $bufferSize) != 0 ? ($messageLength % $bufferSize) : ($messageLength != 0 ? $bufferSize : 0);

		// loop around all frames to send
		for ($i=0; $i<$frameCount; $i++) {
			// fetch fin, opcode and buffer length for frame
			$fin = $i != $maxFrame ? 0 : self::WS_FIN;
			$opcode = $i != 0 ? self::WS_OPCODE_CONTINUATION : $opcode;

			$bufferLength = $i != $maxFrame ? $bufferSize : $lastFrameBufferLength;

			// set payload length variables for frame
			if ($bufferLength <= 125) {
				$payloadLength = $bufferLength;
				$payloadLengthExtended = '';
				$payloadLengthExtendedLength = 0;
			}
			elseif ($bufferLength <= 65535) {
				$payloadLength = self::WS_PAYLOAD_LENGTH_16;
				$payloadLengthExtended = pack('n', $bufferLength);
				$payloadLengthExtendedLength = 2;
			}
			else {
				$payloadLength = self::WS_PAYLOAD_LENGTH_63;
				$payloadLengthExtended = pack('xxxxN', $bufferLength); // pack 32 bit int, should really be 64 bit int
				$payloadLengthExtendedLength = 8;
			}

			// set frame bytes
			$buffer = pack('n', (($fin | $opcode) << 8) | $payloadLength) . $payloadLengthExtended . substr($message, $i*$bufferSize, $bufferLength);

			// send frame
			$socket = $this->wsClients[$clientID][0];

			$left = 2 + $payloadLengthExtendedLength + $bufferLength;
			do {
				$sent = @socket_send($socket, $buffer, $left, 0);
				if ($sent === false) return false;

				$left -= $sent;
				if ($sent > 0) $buffer = substr($buffer, $sent);
			}
			while ($left > 0);
		}

		return true;
	}
	function wsSendClientClose($clientID, $status=false) {
		// check if client ready state is already closing or closed
		if ($this->wsClients[$clientID][2] == self::WS_READY_STATE_CLOSING || $this->wsClients[$clientID][2] == self::WS_READY_STATE_CLOSED) return true;

		// store close status
		$this->wsClients[$clientID][5] = $status;

		// send close frame to client
		$status = $status !== false ? pack('n', $status) : '';
		$this->wsSendClientMessage($clientID, self::WS_OPCODE_CLOSE, $status);

		// set client ready state to closing
		$this->wsClients[$clientID][2] = self::WS_READY_STATE_CLOSING;
	}

	// client non-internal functions
	function wsClose($clientID) {
		return $this->wsSendClientClose($clientID, self::WS_STATUS_NORMAL_CLOSE);
	}
	function wsSend($clientID, $message, $binary=false) {
		return $this->wsSendClientMessage($clientID, $binary ? self::WS_OPCODE_BINARY : self::WS_OPCODE_TEXT, $message);
	}

	function log( $message )
	{
		date_default_timezone_set('Europe/Berlin');
		echo date('Y-m-d H:i:s: ') . $message . "\n";
	}

	// function wsMobStuff()
	// {
	// 	srand();
	// 	date_default_timezone_set('Europe/Berlin');
	//     //$this->log('mobcheck!');

	// 	foreach ($this->wsClients as $id => $client) {
	// 		if (isset($this->wsUsers[$id])) {
	// 		//$this->log('found player!');
	// 			foreach ($this->wsEntities as $entityId => $entityObject) {
	// 				if ($this->wsEntities[$entityId]['roomId'] == $this->wsUsers[$id]['roomId'] && $this->wsEntities[$entityId]['eeg'] > 0){
	// 					//$this->log('player in same room as entity!');

	// 					if ($this->wsEntities[$entityId]['type'] == 'bouncer') {
							
	// 						if ($this->wsEntities[$entityId]['userId'] != $this->wsUsers[$id]['userId']) {
	// 							$pStealth = $this->wsUsers[$id]['stealth'] + $this->wsUsers[$id]['stealthBonus'];
	// 							$eDetect = $this->wsEntities[$entityId]['userId'];

	// 							$skillRoll = rand(2, 20) + $eDetect - $pStealth;

	// 							if ($skillRoll >= 11) {

	// 								foreach ($this->wsClients as $recipId => $recipClient) {
	// 									if ($this->wsUsers[$recipId]['roomId'] ==  $this->wsEntities[$entityId]['roomId']) {
	// 										$this->wsSend($recipId, 'CREATEBULLET ' . $this->wsEntities[$entityId]['userId'] . ' ' . round($this->wsEntities[$entityId]['x']) . ' ' . round($this->wsEntities[$entityId]['y']) . ' ' . round($this->wsUsers[$id]['x']) . ' ' . round($this->wsUsers[$id]['y']));
	// 									}
	// 								}
	// 								//$this->wsSend($id, 'SYSMSG A bouncer ICE detected you!');
	// 								$this->wsUsers[$id]['willpower'] -= 1;
									
	// 								if ($this->wsUsers[$id]['willpower'] < 0) {
	// 									//player flatlined
	// 									$this->flatlinePlayer($id);
	// 								}
	// 								else {
	// 									$this->wsSend($id, 'CHANGEWILLPOWER 1');
	// 									$userObject = User::model()->findByPk($this->wsUsers[$id]['userId']);
	// 									$profileObject = $userObject->profile;
	// 									$profileObject->willpower -= 1;
	// 									$profileObject->save(false);
	// 								}

	// 							}
	// 						}

	// 					}

	// 					if ($this->wsEntities[$entityId]['type'] == 'default') {
	// 						$pStealth = $this->wsUsers[$id]['stealth'] + $this->wsUsers[$id]['stealthBonus'];
	// 						$eDetect = $this->wsEntities[$entityId]['userId'];

	// 						$skillRoll = rand(2, 20) + $eDetect - $pStealth;

	// 						if ($skillRoll >= 11) {
	// 							foreach ($this->wsClients as $recipId => $recipClient) {
	// 								if ($this->wsUsers[$recipId]['roomId'] ==  $this->wsEntities[$entityId]['roomId']) {
	// 									$this->wsSend($recipId, 'CREATEBULLET 0 ' . round($this->wsEntities[$entityId]['x']) . ' ' . round($this->wsEntities[$entityId]['y']) . ' ' . round($this->wsUsers[$id]['x']) . ' ' . round($this->wsUsers[$id]['y']));
	// 								}
	// 							}
	// 							//$this->wsSend($id, 'SYSMSG A Murphy Virus attacked you!');
	// 							$this->wsUsers[$id]['eeg'] -= 1;
	// 							if ($this->wsUsers[$id]['eeg'] < 0) {
	// 								//player flatlined
	// 								$this->flatlinePlayer($id);
	// 							}
	// 							else {
	// 								$this->wsSend($id, 'CHANGEEEG 1');
	// 								$userObject = User::model()->findByPk($this->wsUsers[$id]['userId']);
	// 								$profileObject = $userObject->profile;
	// 								$profileObject->eeg -= 1;
	// 								$profileObject->save(false);
	// 							}
								
	// 						}
	// 					}

	// 				}
	// 			}
	// 		}
	// 	}

	// }

	function wsMobStuff()
	{
		srand();
		date_default_timezone_set('Europe/Berlin');

		foreach ($this->wsEntities as $entityId => $entityObject) {
			$this->wsEntities[$entityId]['targetX'] = rand(2, 25) * 32;
			$this->wsEntities[$entityId]['targetY'] = rand(2, 20) * 32;
		}

		foreach ($this->wsClients as $id => $client) {
			if (isset($this->wsUsers[$id])) {
				foreach ($this->wsEntities as $entityId => $entityObject) {
					if ($this->wsEntities[$entityId]['roomId'] == $this->wsUsers[$id]['roomId'] && $this->wsEntities[$entityId]['eeg'] > 0){
						//$this->log('player in same room as entity!');

						if ($this->wsEntities[$entityId]['type'] == 'bouncer') {

							// check if bouncer should attack
							if ($this->wsEntities[$entityId]['userId'] != $this->wsUsers[$id]['userId']) {
								$pStealth = $this->wsUsers[$id]['stealth'] + $this->wsUsers[$id]['stealthBonus'];
								$eDetect = $this->wsEntities[$entityId]['userId'];

								$skillRoll = rand(2, 20) + $eDetect - $pStealth;

								if ($skillRoll >= 11) {
													
									$currentBullets = count($this->wsBullets);
						            //$Server->log($currentBullets);
						            $this->wsBullets[$currentBullets] = array(
						                'bulletId' => $currentBullets,
						                'currentX' => (int)$this->wsEntities[$entityId]['x'],
						                'currentY' => (int)$this->wsEntities[$entityId]['y'],
						                'targetX' => (int)$this->wsUsers[$id]['x'],
						                'targetY' => (int)$this->wsUsers[$id]['y'],
						                'userId' => (int)$this->wsEntities[$entityId]['userId'],
						                'trajX' => (int)$this->wsUsers[$id]['x'] - $this->wsEntities[$entityId]['x'],
						                'trajY' => (int)$this->wsUsers[$id]['y'] - $this->wsEntities[$entityId]['y'],
						                'roomId' => (int)$this->wsEntities[$entityId]['roomId'],
						                'hadImpact' => 0
						            );

						            $returnCommand = array(
						                'xcommand' => 'ADDBULLET',
						                'xvalue' => $this->wsBullets[$currentBullets]
						            );

						            foreach ( $this->wsClients as $idc => $clientc ) {
						                $this->wsSend($idc, json_encode($returnCommand));
						            }




								}
							}

						}


						if ($this->wsEntities[$entityId]['type'] == 'default'){
							$currentBullets = count($this->wsBullets);
				            //$Server->log($currentBullets);
				            $this->wsBullets[$currentBullets] = array(
				                'bulletId' => $currentBullets,
				                'currentX' => (int)$this->wsEntities[$entityId]['x'],
				                'currentY' => (int)$this->wsEntities[$entityId]['y'],
				                'targetX' => (int)$this->wsUsers[$id]['x'],
				                'targetY' => (int)$this->wsUsers[$id]['y'],
				                'userId' => (int)$this->wsEntities[$entityId]['userId'],
				                'trajX' => (int)$this->wsUsers[$id]['x'] - $this->wsEntities[$entityId]['x'],
				                'trajY' => (int)$this->wsUsers[$id]['y'] - $this->wsEntities[$entityId]['y'],
				                'roomId' => (int)$this->wsEntities[$entityId]['roomId'],
				                'hadImpact' => 0
				            );

				            $returnCommand = array(
				                'xcommand' => 'ADDBULLET',
				                'xvalue' => $this->wsBullets[$currentBullets]
				            );

				            foreach ( $this->wsClients as $idc => $clientc ) {
				                $this->wsSend($idc, json_encode($returnCommand));
				            }							
						}

					}
				}
			}
		}
	}

	function wsCronStuff()
	{
		srand();
		date_default_timezone_set('Europe/Berlin');
	    $this->log('spawncheck!');
	    foreach ($this->wsRooms as $roomId => $roomObject) {

	    	// normal entity spawn
	    	if ($this->wsRooms[$roomId]['entityAmount'] < $this->wsRooms[$roomId]['level']) {

	    		$spawnChance = rand(1, 100);

	    		if ($spawnChance > 50 && $this->wsRooms[$roomId]['type'] != 'io') {
	    			$this->wsRooms[$roomId]['entityAmount'] += 1;
					$entity = new Entity;
					$entity->userId = $this->wsRooms[$roomId]['userId'];
					$entity->roomId = $roomId;

					if ($this->wsRooms[$roomId]['type'] == 'database') {
						$entity->type = 'fragment';
						$entity->attack = 0;
						$entity->defend = 0;
						$entity->stealth = 1;
						$entity->detect = 0;
						$entity->eeg = 10;
						$entity->credits = 0;
					}

					if ($this->wsRooms[$roomId]['type'] == 'firewall') {
						$entity->type = 'bouncer';
						$entity->attack = 0;
						$entity->defend = 0;
						$entity->stealth = 0;
						$entity->detect = 1;
						$entity->eeg = 10;
						$entity->credits = 0;
					}

					if ($this->wsRooms[$roomId]['type'] == 'terminal') {
						$entity->type = 'user';
						$entity->attack = 0;
						$entity->defend = 1;
						$entity->stealth = 0;
						$entity->detect = 0;
						$entity->eeg = 10;
						$entity->credits = 0;
					}

					if ($this->wsRooms[$roomId]['type'] == 'coproc') {
						$entity->type = 'worker';
						$entity->attack = 0;
						$entity->defend = 1;
						$entity->stealth = 0;
						$entity->detect = 0;
						$entity->eeg = 10;
						$entity->credits = 0;
					}

					if ($this->wsRooms[$roomId]['type'] == 'coding') {
						$entity->type = 'codebit';
						$entity->attack = 0;
						$entity->defend = 0;
						$entity->stealth = 1;
						$entity->detect = 0;
						$entity->eeg = 10;
						$entity->credits = 0;
					}

					$entity->created = date( 'Y-m-d H:i:s', time());
					$entity->x = rand(2, 23) * 32;
					$entity->y = rand(2, 18) * 32;

					$entity->save();

					$this->wsEntities[$entity->id] = array('entityId' => $entity->id, 'userId' => $entity->userId, 'roomId' => $entity->roomId, 'type' => $entity->type, 'attack' => $entity->attack, 'defend' => $entity->defend, 'stealth' => $entity->stealth, 'detect' => $entity->detect, 'eeg' => $entity->eeg, 'x' => $entity->x, 'y' => $entity->y, 'credits' => $entity->credits);
				}

	    	}

	    	// virus spawn
	    	if ($this->wsRooms[$roomId]['entityAmount'] < $this->wsRooms[$roomId]['level'] * 2) {
		    	$spawnChance = rand(1, 100);
		    	if ($spawnChance > 98 && $this->wsRooms[$roomId]['type'] != 'io') {
		    		$this->wsRooms[$roomId]['entityAmount'] += 1;
		    		//$this->log('virus spawned!');
					$entity = new Entity;
					$entity->userId =0;
					$entity->roomId = $roomId;
					$entity->type = 'default';
					$entity->created = date( 'Y-m-d H:i:s', time());
					$entity->attack = 1;
					$entity->defend = 0;
					$entity->stealth = 0;
					$entity->detect = 0;
					$entity->eeg = 10;
					$entity->x = rand(2, 23) * 32;
					$entity->y = rand(2, 18) * 32;
					$entity->credits = 1;

					$entity->save();

					$this->wsEntities[$entity->id] = array('entityId' => $entity->id, 'userId' => $entity->userId, 'roomId' => $entity->roomId, 'type' => $entity->type, 'attack' => $entity->attack, 'defend' => $entity->defend, 'stealth' => $entity->stealth, 'detect' => $entity->detect, 'eeg' => $entity->eeg, 'x' => $entity->x, 'y' => $entity->y, 'credits' => $entity->credits);
				}
			}

	    }
	        		
	}

	function flatlinePlayer($id)
	{
		$this->wsUsers[$id]['roomId'] = $this->wsUsers[$id]['homeNode'];
		$this->wsUsers[$id]['willpower'] = 100;
		$this->wsUsers[$id]['eeg'] = 100;
		$this->wsUsers[$id]['secrating'] = 0;

		$userObject = User::model()->findByPk($this->wsUsers[$id]['userId']);
		$profileObject = $userObject->profile;

		$profileObject->location = $this->wsUsers[$id]['homeNode'];
		$profileObject->willpower = 100;
		$profileObject->eeg = 100;
		$profileObject->secrating = 0;

		$profileObject->save(false);

		$this->wsSend($id, 'SYSMSG You have been flatlined!');
		$this->wsSend($id, 'FLATLINE');
	}

	function bind( $type, $func )
	{
		if ( !isset($this->wsOnEvents[$type]) )
			$this->wsOnEvents[$type] = array();
		$this->wsOnEvents[$type][] = $func;
	}

	function unbind( $type='' )
	{
		if ( $type ) unset($this->wsOnEvents[$type]);
		else $this->wsOnEvents = array();
	}
}
?>