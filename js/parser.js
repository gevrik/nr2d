function parseReply( text ) {

			//console.log(text);

			var parsed = JSON.parse(text);
            var xcommand = parsed.xcommand;
            var xvalue = parsed.xvalue;

            //console.log('c: ' + xcommand + ' v: ' + xvalue);

			var args = text.split(' ');
			var command = args.shift();
			var chatMessage;


			if (xcommand == 'INITP') {
				hero.userId = xvalue.userId;
				hero.speed = xvalue.speed;
				hero.roomId = xvalue.roomId;
				hero.homeId = xvalue.homeId;
				hero.socketId = xvalue.socketId;
				hero.credits = xvalue.credits;
				hero.secrating = xvalue.secrating;
				hero.stealth = xvalue.stealth;
				hero.detect = xvalue.detect;
				hero.attack = xvalue.attack;
				hero.defend = xvalue.defend;
				hero.coding = xvalue.coding;
				hero.snippets = xvalue.snippets;
				hero.eeg = xvalue.eeg;
				hero.willpower = xvalue.willpower;
				hero.stealthBonus = xvalue.stealthBonus;
				hero.detectBonus = xvalue.detectBonus;
				hero.attackBonus = xvalue.attackBonus;
				hero.defendBonus = xvalue.defendBonus;
				hero.maxStorage = xvalue.maxStorage;
				hero.maxMemory = xvalue.maxMemory;
				hero.decking = xvalue.decking;
				hero.slots = xvalue.slots;
				hero.name = xvalue.name;
				hero.attackspeed = xvalue.attackspeed;

				username = hero.name;
				holygrail = hero.userId;

				console.log(hero);

				gameReady = true;

				serverMessage = {
					xcommand: 'ROOMUPDATE',
					xvalue: 0
				};
				send(JSON.stringify(serverMessage));

			}

			else if (xcommand == 'ADDBULLET') {
				//console.log(xvalue);
				if (!bullets[xvalue.bulletId]) {
					bullets[xvalue.bulletId] = {
						id: xvalue.bulletId,
						currentX: xvalue.currentX,
						currentY: xvalue.currentY,
						targetX: xvalue.targetX,
						targetY: xvalue.targetY,
						trajX: xvalue.trajX,
						trajY: xvalue.trajY,
						userId: xvalue.userId,
						roomId: xvalue.roomId,
						hadImpact: xvalue.hadImpact
					};
				}

			}

			else if (xcommand == 'ADDBULLETE') {
				//console.log(xvalue);
				virusBlastSound.play();
				if (!bullets[xvalue.bulletId]) {
					bullets[xvalue.bulletId] = {
						id: xvalue.bulletId,
						currentX: xvalue.currentX,
						currentY: xvalue.currentY,
						targetX: xvalue.targetX,
						targetY: xvalue.targetY,
						trajX: xvalue.trajX,
						trajY: xvalue.trajY,
						userId: xvalue.userId,
						roomId: xvalue.roomId,
						hadImpact: xvalue.hadImpact
					};
				}

			}

			else if (xcommand == 'ADDTOSTORAGE') {
				
				if (!storagePrograms[xvalue.programId]) {
					storagePrograms[xvalue.programId] = {
						id: xvalue.programId,
						userId: xvalue.userId,
						type: xvalue.type,
						rating: xvalue.rating,
						condition: xvalue.condition,
						maxUpgrades: xvalue.maxUpgrades,
						upgrades: xvalue.upgrades,
						name: xvalue.name,
						loaded : 0
					};
				}

				storagePrograms[xvalue.programId].id = xvalue.programId;
				storagePrograms[xvalue.programId].userId = xvalue.userId;
				storagePrograms[xvalue.programId].type = xvalue.type;
				storagePrograms[xvalue.programId].rating = xvalue.rating;
				storagePrograms[xvalue.programId].condition = xvalue.condition;
				storagePrograms[xvalue.programId].maxUpgrades = xvalue.maxUpgrades;
				storagePrograms[xvalue.programId].upgrades = xvalue.upgrades;
				storagePrograms[xvalue.programId].name = xvalue.name;
				storagePrograms[xvalue.programId].loaded = 0;

				//console.log(storagePrograms);

			}

			else if (xcommand == 'CHAT') {
				// received chat text
				showLog = true;
				showLogTimer = 125;
				logText.unshift({xvalue: xvalue});
				if (logText.length > 10) {
					logText.pop();
				}
				log(xvalue);
			}

			else if (xcommand == 'CREDITSCHANGE') {
				var creditsChange = xvalue;
				hero.credits -= creditsChange;
				if (hero.credits < 0) {
					hero.credits = 0;
				}
			}

			else if (xcommand == 'DELETEBULLET') {
				if (bullets[xvalue]) {
					var explosionX = bullets[xvalue].currentX;
					var explosionY = bullets[xvalue].currentY;
					ctx.beginPath();
					ctx.arc(explosionX, explosionY, 16, 0, 2 * Math.PI, false);
					ctx.fillStyle = "rgb(255, 0, 0)";
					ctx.fill();
					delete bullets[xvalue];
				}
			}

			else if (xcommand == 'LOADPROGRAM') {
				// received chat text
				var loadProgId = xvalue;
				storagePrograms[loadProgId].loaded = 1;
				if (memoryPrograms[loadProgId]) {
					memoryPrograms[loadProgId].id = loadProgId;
					memoryPrograms[loadProgId].name = storagePrograms[loadProgId].name;
					memoryPrograms[loadProgId].rating = storagePrograms[loadProgId].rating;
					memoryPrograms[loadProgId].type = storagePrograms[loadProgId].type;
				}
				else {
					memoryPrograms[loadProgId] = {
						id: storagePrograms[loadProgId].id,
						name: storagePrograms[loadProgId].name,
						rating: storagePrograms[loadProgId].rating,
						type: storagePrograms[loadProgId].type
					};
				}
				++usedSlots;
				pageArray = [];
				currentPage = 1;
				maxPage = 1;
			}

			else if (xcommand == 'MINEENTITY') {
				var minedEntityId = xvalue;

				if (otherEntities[minedEntityId].type == 'fragment') {
					hero.credits += otherEntities[minedEntityId].eeg;
				}
				else if (otherEntities[minedEntityId].type == 'codebit') {
					hero.snippets += otherEntities[minedEntityId].eeg;
				}

				var serverCommandME = {
				xcommand: 'REMOVEENTITY',
				xvalue: minedEntityId
				};
				send(JSON.stringify(serverCommandME));

			}

			else if (xcommand == 'OTHERENTITY') {

				//console.log('entity found');

				if (!otherEntities[xvalue.id]) {
					//console.log('entity does not exists');
					otherEntities[xvalue.id] = {
						id: xvalue.id,
						x: xvalue.x,
						y: xvalue.y,
						roomId: xvalue.roomId,
						eeg: xvalue.eeg,
						userId: xvalue.userId,
						type: xvalue.type,
						targetX: xvalue.targetX,
						targetY: xvalue.targetY,
						trajX: xvalue.trajX,
						trajY: xvalue.trajY,
						speed: xvalue.speed,
						moveTimer: 0
					};
				}

				otherEntities[xvalue.id].id = xvalue.id;
				otherEntities[xvalue.id].roomId = xvalue.roomId;
				otherEntities[xvalue.id].eeg = xvalue.eeg;
				otherEntities[xvalue.id].userId = xvalue.userId;
				otherEntities[xvalue.id].type = xvalue.type;
				otherEntities[xvalue.id].targetX = xvalue.targetX;
				otherEntities[xvalue.id].targetY = xvalue.targetY;
				
				//console.log(otherEntities);
			}

			else if (xcommand == 'OTHERUSER') {

				if (!otherUsers[xvalue.socketId]) {
					otherUsers[xvalue.socketId] = {
					userId: xvalue.userId,
					speed: xvalue.speed,
					roomId: xvalue.roomId,
					homeId: xvalue.homeId,
					socketId: xvalue.socketId,
					credits: xvalue.socketId,
					secrating: xvalue.secrating,
					stealth: xvalue.stealth,
					detect: xvalue.detect,
					attack: xvalue.attack,
					defend: xvalue.defend,
					coding: xvalue.coding,
					snippets: xvalue.snippets,
					eeg: xvalue.eeg,
					willpower: xvalue.willpower,
					stealthBonus: xvalue.stealthBonus,
					detectBonus: xvalue.detectBonus,
					attackBonus: xvalue.attackBonus,
					defendBonus: xvalue.defendBonus,
					maxStorage: xvalue.maxStorage,
					maxMemory: xvalue.maxMemory,
					decking: xvalue.decking,
					slots: xvalue.slots,
					x: xvalue.x,
					y: xvalue.y
					};
				}

				//console.log(otherUsers[xvalue.socketId]);

				otherUsers[xvalue.socketId].userId = xvalue.userId;
				otherUsers[xvalue.socketId].speed = xvalue.speed;
				otherUsers[xvalue.socketId].roomId = xvalue.roomId;
				otherUsers[xvalue.socketId].homeId = xvalue.homeId;
				otherUsers[xvalue.socketId].socketId = xvalue.socketId;
				otherUsers[xvalue.socketId].credits = xvalue.socketId;
				otherUsers[xvalue.socketId].secrating = xvalue.secrating;
				otherUsers[xvalue.socketId].stealth = xvalue.stealth;
				otherUsers[xvalue.socketId].detect = xvalue.detect;
				otherUsers[xvalue.socketId].attack = xvalue.attack;
				otherUsers[xvalue.socketId].defend = xvalue.defend;
				otherUsers[xvalue.socketId].coding = xvalue.coding;
				otherUsers[xvalue.socketId].snippets = xvalue.snippets;
				otherUsers[xvalue.socketId].eeg = xvalue.eeg;
				otherUsers[xvalue.socketId].willpower = xvalue.willpower;
				otherUsers[xvalue.socketId].stealthBonus = xvalue.stealthBonus;
				otherUsers[xvalue.socketId].detectBonus = xvalue.detectBonus;
				otherUsers[xvalue.socketId].attackBonus = xvalue.attackBonus;
				otherUsers[xvalue.socketId].defendBonus = xvalue.defendBonus;
				otherUsers[xvalue.socketId].maxStorage = xvalue.maxStorage;
				otherUsers[xvalue.socketId].maxMemory = xvalue.maxMemory;
				otherUsers[xvalue.socketId].decking = xvalue.decking;
				otherUsers[xvalue.socketId].slots = xvalue.slots;
				otherUsers[xvalue.socketId].x = xvalue.x;
				otherUsers[xvalue.socketId].y = xvalue.y;

				//console.log(otherUsers);
			}

			else if (xcommand == 'RAISEBONUS') {
				var bonusType = xvalue.type;
				var bonusChange = xvalue.amount;
				//hero[bonusType] += bonusChange;
				var bonusTypeAttr = bonusType + 'Bonus';
				hero[bonusTypeAttr] += bonusChange;
			}

			else if (xcommand == 'RAISECREDITS') {
				var creditsRaise = xvalue;
				hero.credits += (creditsRaise * 1);
			}

			else if (xcommand == 'RAISEEEG') {
				var eegRaise = xvalue;
				hero.eeg += (eegRaise * 1);
				if (hero.eeg > 100) {
					hero.eeg = 100;
				}
			}

			else if (xcommand == 'RAISEMAXMEMORY') {
				var maxMemoryRaise = xvalue;
				hero.maxMemory = hero.maxMemory + maxMemoryRaise;
			}
			
			else if (xcommand == 'RAISEMAXSTORAGE') {
				var maxStorageRaise = xvalue;
				hero.maxStorage = hero.maxStorage + maxStorageRaise;
			}

			else if (xcommand == 'RECALL') {
				bullets = [];
				var serverCommand = {
					xcommand: 'ROOMUPDATE',
					xvalue: 0
				};
				send(JSON.stringify(serverCommand));
			}

			else if (xcommand == 'REDUCEBONUS') {
				var malusType = xvalue.type;
				var malusChange = xvalue.amount;
				//hero[malusType] -= malusChange;
				var malusTypeAttr = malusType + 'Bonus';
				hero[malusTypeAttr] -= malusChange;
				//console.log('stat changed');
			}

			else if (xcommand == 'REDUCEEEG') {
				var eegReduction = xvalue;
				hero.eeg -= (eegReduction * 1);
				if (hero.eeg < 0) {
					hero.eeg = 0;
				}
			}

			else if (xcommand == 'REDUCEENTEEG') {
				var entEegReduction = xvalue.amount;
				var entEegId = xvalue.entityId;
				
				if (otherEntities[entEegId]) {
					console.log('entity damaged');
					otherEntities[entEegId].eeg -= (entEegReduction * 1);
				
					if (otherEntities[entEegId].eeg <= 0) {
						console.log('entity flatlined');
						var serverCommandREE = {
						xcommand: 'REMOVEENTITY',
						xvalue: entEegId
						};
						send(JSON.stringify(serverCommandREE));
					}
				}
			}

			else if (xcommand == 'REDUCEMAXMEMORY') {
				var maxMemoryReduction = xvalue;
				hero.maxMemory -= maxMemoryReduction * 1;
				if (hero.maxMemory < 0) {
					hero.maxMemory = 0;
				}
			}
			
			else if (xcommand == 'REDUCEMAXSTORAGE') {
				var maxStorageReduction = xvalue;
				hero.maxStorage -= maxStorageReduction;
				if (hero.maxStorage < 0) {
					hero.maxStorage = 0;
				}
			}

			else if (xcommand == 'REDUCEPROGCOND') {
				var condRedProgId = xvalue;
				storagePrograms[condRedProgId].condition -= 1;
				if (storagePrograms[condRedProgId].condition < 0) {
					storagePrograms[condRedProgId].condition = 0;
				}
			}

			else if (xcommand == 'REMOVEENTITY') {
				var removeEntId = xvalue;
				delete otherEntities[removeEntId];
			}

			else if (xcommand == 'RESETPROGRESS') {
				progressBar = 0;
				barOriginal = progressBar;
				barCommand = '';
				barParam = {};
			}

			else if (xcommand == 'ROOMUPDATE') {
				closeAllMenus();
				selectedEntity = 0;
				roomName = xvalue.name;
				roomType = xvalue.type;
				roomOwner = xvalue.owner;
				roomLevel = xvalue.level;
				roomId = xvalue.roomId;
				northExit = xvalue.northExit;
				eastExit = xvalue.eastExit;
				southExit = xvalue.southExit;
				westExit = xvalue.westExit;
				hero.roomId = xvalue.roomId;
			}

			else if (xcommand == 'SNIPPETSCHANGE') {
				var snippetsChange = xvalue;
				hero.snippets -= snippetsChange;
				if (hero.snippets < 0) {
					hero.snippets = 0;
				}
			}

			else if (xcommand == 'SYSMSG') {
				// received msg text
				showLog = true;
				showLogTimer = 125;
				logText.unshift({xvalue: xvalue});
				if (logText.length > 10) {
					logText.pop();
				}
				chatMessage = xvalue;
				log(chatMessage);
			}

			else if (xcommand == 'UNLOADPROGRAM') {
				var unloadedProgram = xvalue;
				if (storagePrograms[unloadedProgram]) {
					storagePrograms[unloadedProgram].loaded = 0;
					if (memoryPrograms[unloadedProgram]) {
						delete memoryPrograms[unloadedProgram];
						--usedSlots;
					}
				}
			}

			else if (xcommand == 'UPGRADEITEM') {
				var upgradedItem = xvalue.itemId;
				var newName = xvalue.newName;

				storagePrograms[upgradedItem].name = newName;
				storagePrograms[upgradedItem].rating += 1;
				storagePrograms[upgradedItem].upgrades += 1;

				upgradeItemSound.play();

			}

			else if (xcommand == 'FLATLINE') {
				progressBar = 0;
				barOriginal = progressBar;
				barCommand = '';
				barParam = '';

				hero.eeg = 100;
				hero.willpower = 100;
				hero.secrating = 0;

				var serverCommandFL = {
					xcommand: 'ROOMUPDATE',
					xvalue: 0
				};
				send(JSON.stringify(serverCommandFL));
			}

			else if (command == 'PROGRESSBAR') {
				progressBar = args.shift();
			}
						
			else if (xcommand == 'REMOVEUSER') {
				console.log('removed a user from the room');
				var removeUserSocketId = xvalue;
				if (otherUsers[removeUserSocketId]) {
					otherUsers[removeUserSocketId].x = -9999;
					otherUsers[removeUserSocketId].y = -9999;
				}
			}

			else {
				console.log('unknown command received: ' + text);
			}

		}