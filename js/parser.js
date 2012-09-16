function parseReply( text ) {
			//console.log(text);
			var args = text.split(' ');
			var command = args.shift();
			var chatMessage;
			
			// check command
			if (command == 'CHAT') {
				// received chat text
				var actorname = args.shift();
				chatMessage = args.join(' ');
				log(actorname+': ' + chatMessage);
			}

			else if (command == 'INITP') {
				// received INITIAL PLAYER DATA
				var heroRoom = args.shift();
				var heroHome = args.shift();
				var heroSpeed = args.shift();
				var heroSocketId = args.shift();
				var heroCredits = args.shift();
				var heroSecRating = args.shift();
				var heroStealth = args.shift();
				var heroDetect = args.shift();
				var heroAttack = args.shift();
				var heroDefend = args.shift();
				var heroCoding = args.shift();
				var heroSnippets = args.shift();
				var heroEEG = args.shift();
				var heroWillpower = args.shift();
				var heroStealthBonus = args.shift();
				var heroAttackBonus = args.shift();
				var heroMaxStorage = args.shift();
				var heroMaxMemory = args.shift();
				var heroDetectBonus = args.shift();
				var heroDefendBonus = args.shift();
				var heroDecking = args.shift();

				hero.speed = heroSpeed * 1;
				hero.roomId = heroRoom * 1;
				hero.homeId = heroHome * 1;
				hero.socketId = heroSocketId * 1;
				hero.credits = heroCredits * 1;
				hero.secrating = heroSecRating * 1;
				hero.stealth = heroStealth * 1;
				hero.detect = heroDetect * 1;
				hero.attack = heroAttack * 1;
				hero.defend = heroDefend * 1;
				hero.coding = heroCoding * 1;
				hero.snippets = heroSnippets * 1;
				hero.eeg = heroEEG * 1;
				hero.willpower = heroWillpower * 1;
				hero.stealthBonus = heroStealthBonus * 1;
				hero.attackBonus = heroAttackBonus * 1;
				hero.maxStorage = heroMaxStorage * 1;
				hero.maxMemory = heroMaxMemory * 1;
				hero.detectBonus = heroDetectBonus * 1;
				hero.defendBonus = heroDefendBonus * 1;
				hero.decking = heroDecking * 1;

				console.log(hero);

			}

			else if (command == 'NORTHEXIT') {
				// received chat text
				northExit = args.join(' ');
			}
			else if (command == 'EASTEXIT') {
				// received chat text
				eastExit = args.join(' ');
			}
			else if (command == 'SOUTHEXIT') {
				// received chat text
				southExit = args.join(' ');
			}
			else if (command == 'WESTEXIT') {
				// received chat text
				westExit = args.join(' ');
			}


			else if (command == 'ADDTOSTORAGE') {
				// received chat text
				var memProgId = args.shift();
				var memProgUserId = args.shift();
				var memProgType = args.shift();
				var memProgRating = args.shift();
				var memProgCondition = args.shift();
				var memProgMaxUpgrades = args.shift();
				var memProgUpgrades = args.shift();
				var memProgName = args.join(' ');

				if (!storagePrograms[memProgId]) {
					storagePrograms[memProgId] = {
						id: memProgId * 1,
						userId: memProgUserId * 1,
						type: memProgType,
						rating: memProgRating * 1,
						condition: memProgCondition * 1,
						maxUpgrades: memProgMaxUpgrades * 1,
						upgrades: memProgUpgrades * 1,
						name: memProgName,
						loaded : 0
					};
				}

				storagePrograms[memProgId].id = memProgId * 1;
				storagePrograms[memProgId].userId = memProgUserId * 1;
				storagePrograms[memProgId].type = memProgType;
				storagePrograms[memProgId].rating = memProgRating * 1;
				storagePrograms[memProgId].condition = memProgCondition * 1;
				storagePrograms[memProgId].maxUpgrades = memProgMaxUpgrades * 1;
				storagePrograms[memProgId].upgrades = memProgUpgrades * 1;
				storagePrograms[memProgId].name = memProgName;
				storagePrograms[memProgId].loaded = 0;

			}

			else if (command == 'CHANGEATTACKBONUS') {
				var attackBonusChange = args.shift();
				// received chat text
				hero.attackBonus += attackBonusChange * 1;
				if (hero.attackBonus > 10) {
					hero.attackBonus = 10;
				}
			}

			else if (command == 'CHANGEDETECTBONUS') {
				var detectBonusChange = args.shift();
				// received chat text
				hero.detectBonus += detectBonusChange * 1;
				if (hero.detectBonus > 10) {
					hero.detectBonus = 10;
				}
			}

			else if (command == 'CHANGEDEFENDBONUS') {
				var defendBonusChange = args.shift();
				// received chat text
				hero.defendBonus += defendBonusChange * 1;
				if (hero.defendBonus > 10) {
					hero.defendBonus = 10;
				}
			}

			else if (command == 'CHANGEEEG') {
				var eegChange = args.shift();
				// received chat text
				hero.eeg -= eegChange;
				if (hero.eeg < 0) {
					hero.eeg = 0;
				}
			}

			else if (command == 'CHANGESTEALTHBONUS') {
				var stealthBonusChange = args.shift();
				// received chat text
				hero.stealthBonus += stealthBonusChange * 1;
				if (hero.stealthBonus > 10) {
					hero.stealthBonus = 10;
				}
			}
			
			else if (command == 'CHANGEWILLPOWER') {
				var willpowerChange = args.shift();
				// received chat text
				hero.willpower -= willpowerChange;
				if (hero.willpower < 0) {
					hero.willpower = 0;
				}
			}
			
			else if (command == 'CREATEBULLET') {
				// received chat text
				var currentX = args.shift();
				var currentY = args.shift();
				var targetX = args.shift();
				var targetY = args.shift();

				bullets.push({
					currentX: currentX * 1,
					currentY: currentY * 1,
					targetX: targetX * 1,
					targetY: targetY * 1
				});

				console.log(bullets);

				
			}

			else if (command == 'CREDITSCHANGE') {
				// received chat text
				var creditsChange = args.shift();
				hero.credits -= creditsChange;
				if (hero.credits < 0) {
					hero.credits = 0;
				}
			}

			else if (command == 'DAMAGEVIRUS') {
				var damagedVirusId = args.shift();
				var virusDamageAmount = args.shift();
				// received chat text
				if (otherEntities[damagedVirusId]) {
					otherEntities[damagedVirusId].eeg -= virusDamageAmount;
					if (otherEntities[damagedVirusId].eeg < 0) {
						otherEntities[damagedVirusId].eeg = 0;
					}
				}
			}

			else if (command == 'FLATLINE') {
				progressBar = 0;
				barOriginal = progressBar;
				barCommand = '';
				barParam = '';

				hero.eeg = 100;
				hero.willpower = 100;
				hero.secrating = 0;

				send('ROOMUPDATE');
			}

			else if (command == 'FLATLINEVIRUS') {
				var flatlinedVirusId = args.shift();
				// received chat text
				if (otherEntities[flatlinedVirusId]) {
					delete otherEntities[flatlinedVirusId];
				}
			}

			else if (command == 'LOADPROGRAM') {
				// received chat text
				var loadProgId = args.shift();
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
			}

			else if (command == 'OTHERENTITY') {
				//console.log('other entity spotted');
				var otherEntityId = args.shift();
				var otherEntityX = args.shift();
				var otherEntityY = args.shift();
				var otherEntityRoom = args.shift();
				var otherEntityEEG = args.shift();
				var otherEntityType = args.join(' ');

				if (!otherEntities[otherEntityId]) {
					otherEntities[otherEntityId] = {
						id: otherEntityId * 1,
						x: otherEntityX * 1,
						y: otherEntityY * 1,
						roomId: otherEntityRoom * 1,
						eeg: otherEntityEEG * 1,
						type: otherEntityType
					};
				}

				otherEntities[otherEntityId].id = otherEntityId * 1;
				otherEntities[otherEntityId].x = otherEntityX * 1;
				otherEntities[otherEntityId].y = otherEntityY * 1;
				otherEntities[otherEntityId].roomId = otherEntityRoom * 1;
				otherEntities[otherEntityId].eeg = otherEntityEEG * 1;
				otherEntities[otherEntityId].type = otherEntityType;

				//console.log(otherEntities[otherEntityId]);
			}
			
			else if (command == 'OTHERUSER') {
				//console.log('other user spotted');
				var otherUserSocketId = args.shift();
				var otherUserX = args.shift();
				var otherUserY = args.shift();
				var otherUserRoom = args.shift();

				if (!otherUsers[otherUserSocketId]) {
					otherUsers[otherUserSocketId] = {
						socketId: otherUserSocketId,
						x: otherUserX,
						y: otherUserY,
						roomId: otherUserRoom
					};
				}

				otherUsers[otherUserSocketId].socketId = otherUserSocketId;
				otherUsers[otherUserSocketId].x = otherUserX;
				otherUsers[otherUserSocketId].y = otherUserY;
				otherUsers[otherUserSocketId].roomId = otherUserRoom;

				//console.log(otherUsers);
			}

			else if (command == 'PROGRESSBAR') {
				progressBar = args.shift();
			}

			else if (command == 'RAISEEEG') {
				var eegRaise = args.shift();
				// received chat text
				hero.eeg += (eegRaise * 1);
				if (hero.eeg > 100) {
					hero.eeg = 100;
				}
			}

			else if (command == 'RAISEMAXMEMORY') {
				var maxMemoryRaise = args.shift();
				// received chat text
				hero.maxMemory = hero.maxMemory + (maxMemoryRaise * 1);
			}
			
			else if (command == 'RAISEMAXSTORAGE') {
				var maxStorageRaise = args.shift();
				// received chat text
				hero.maxStorage = hero.maxStorage + (maxStorageRaise * 1);
			}

			else if (command == 'RECALL') {
				bullets = [];
				send('ROOMUPDATE');
			}

			else if (command == 'REDUCEATTACKBONUS') {
				var attackBonusReduction = args.shift();
				// received chat text
				hero.attackBonus -= attackBonusReduction * 1;
				if (hero.attackBonus < 0) {
					hero.attackBonus = 0;
				}
			}

			else if (command == 'REDUCEDEFENDBONUS') {
				var defendBonusReduction = args.shift();
				// received chat text
				hero.defendBonus -= defendBonusReduction * 1;
				if (hero.defendBonus < 0) {
					hero.defendBonus = 0;
				}
			}

			else if (command == 'REDUCEDETECTBONUS') {
				var detectBonusReduction = args.shift();
				// received chat text
				hero.detectBonus -= detectBonusReduction * 1;
				if (hero.detectBonus < 0) {
					hero.detectBonus = 0;
				}
			}
						
			else if (command == 'REDUCEMAXMEMORY') {
				var maxMemoryReduction = args.shift();
				// received chat text
				hero.maxMemory -= maxMemoryReduction * 1;
				if (hero.maxMemory < 0) {
					hero.maxMemory = 0;
				}
			}
			
			else if (command == 'REDUCEMAXSTORAGE') {
				var maxStorageReduction = args.shift();
				// received chat text
				hero.maxStorage -= maxStorageReduction * 1;
				if (hero.maxStorage < 0) {
					hero.maxStorage = 0;
				}
			}

			else if (command == 'REDUCEPROGCOND') {
				var condRedProgId = args.shift();
				// received chat text
				storagePrograms[condRedProgId].condition -= 1;
			}

			else if (command == 'REDUCESTEALTHBONUS') {
				var stealthBonusReduction = args.shift();
				// received chat text
				hero.stealthBonus -= stealthBonusReduction * 1;
				if (hero.stealthBonus < 0) {
					hero.stealthBonus = 0;
				}
			}

			else if (command == 'REMOVEUSER') {
				var removeUserSocketId = args.shift();
				if (otherUsers[removeUserSocketId]) {
					otherUsers[removeUserSocketId].x = -9999;
					otherUsers[removeUserSocketId].y = -9999;
				}
			}

			else if (command == 'RESETPROGRESS') {
				progressBar = 0;
				barOriginal = progressBar;
				barCommand = '';
				barParam = '';
			}

			else if (command == 'ROOMID') {
				// received chat text
				roomId = args.join(' ');
				hero.roomId = roomId;
				//console.log('ROOMUPDATE');
			}

			else if (command == 'ROOMLEVEL') {
				// received chat text
				roomLevel = args.join(' ');
			}

			else if (command == 'ROOMNAME') {
				// received chat text
				roomName = args.join(' ');
			}

			else if (command == 'ROOMOWNER') {
				// received chat text
				roomOwner = args.join(' ');
			}

			else if (command == 'ROOMTYPE') {
				// received chat text
				roomType = args.join(' ');
			}

			else if (command == 'SNIPPETSCHANGE') {
				// received chat text
				var snippetsChange = args.shift();
				hero.snippets -= snippetsChange;
				if (hero.snippets < 0) {
					hero.snippets = 0;
				}
			}
			
			else if (command == 'SYSMSG') {
				// received chat text
				chatMessage = args.join(' ');
				log(chatMessage);
			}

			else if (command == 'UNLOADPROGRAM') {
				var unloadedProgram = args.shift();
				// received chat text
				if (storagePrograms[unloadedProgram]) {
					storagePrograms[unloadedProgram].loaded = 0;
					if (memoryPrograms[unloadedProgram]) {
						delete memoryPrograms[unloadedProgram];
					}
				}
			}

		}