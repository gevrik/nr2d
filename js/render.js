		// Draw everything
		var render = function () {

			ctx.clearRect ( 0 , 0 , canvas.width , canvas.height );

			if (bgReady) {
				ctx.drawImage(bgImage, 0, 0);
			}

			switch (roomType) {

				case 'database':
					roomTypeImage.src = "../../images/database.png";
				break;

				case 'firewall':
					roomTypeImage.src = "../../images/firewall.png";
				break;

				case 'io':
					roomTypeImage.src = "../../images/io.png";
				break;

				case 'terminal':
					roomTypeImage.src = "../../images/terminal.png";
				break;

				case 'coproc':
					roomTypeImage.src = "../../images/coproc.png";
				break;

				case 'coding':
					roomTypeImage.src = "../../images/coding.png";
				break;

				default:
					roomTypeImage.src = "../../images/ph.png";
				break;

			}

			bgImage.src = "../../images/bg" + roomLevel  + ".png";

			if (bgReady) {
				ctx.drawImage(bgImage, 0, 0);
			}

			if (roomTypeReady) {
				ctx.drawImage(roomTypeImage, (canvas.width / 2) - 32, (canvas.height / 2) - 32);
			}

			if (northExit == '0') {
				ctx.fillStyle = 'rgba(30,30,30,0.9)';
				ctx.strokeStyle = 'rgba(0,0,0,1)';
				ctx.lineWidth = 3;
				ctx.beginPath();
				ctx.rect(0,0,canvas.width,32);
				ctx.fill();
				ctx.stroke();
			}

			if (eastExit == '0') {
				ctx.fillStyle = 'rgba(30,30,30,0.9)';
				ctx.strokeStyle = 'rgba(0,0,0,1)';
				ctx.lineWidth = 3;
				ctx.beginPath();
				ctx.rect(canvas.width - 32,0,32,canvas.height);
				ctx.fill();
				ctx.stroke();
			}

			if (southExit == '0') {
				ctx.fillStyle = 'rgba(30,30,30,0.9)';
				ctx.strokeStyle = 'rgba(0,0,0,1)';
				ctx.lineWidth = 3;
				ctx.beginPath();
				ctx.rect(0,canvas.height - 32,canvas.width,32);
				ctx.fill();
				ctx.stroke();
			}

			if (westExit == '0') {
				ctx.fillStyle = 'rgba(30,30,30,0.9)';
				ctx.strokeStyle = 'rgba(0,0,0,1)';
				ctx.lineWidth = 3;
				ctx.beginPath();
				ctx.rect(0,0,32,canvas.height);
				ctx.fill();
				ctx.stroke();
			}

			jQuery.each(bullets, function(i, val) {
				if (bullets[i]) {
					if (bulletReady) {
						ctx.drawImage(bulletImage, bullets[i].currentX, bullets[i].currentY);
					}
				}
			});

			jQuery.each(otherUsers, function(i, val) {
				if (otherReady) {
					if (otherUsers[i] && otherUsers[i].roomId == hero.roomId){
						ctx.drawImage(otherImage, otherUsers[i].x, otherUsers[i].y);
					}
				}
			});

			jQuery.each(otherEntities, function(i, val) {
				if (otherEntities[i] && otherEntities[i].roomId == hero.roomId && otherEntities[i].eeg > 0){

					if (otherEntities[i].type == 'bouncer') {
						monsterImage.src = "../../images/bouncer.png";
					}
					else if (otherEntities[i].type == 'user') {
						monsterImage.src = "../../images/user.png";
					}
					else if (otherEntities[i].type == 'fragment') {
						monsterImage.src = "../../images/fragment.png";
					}
					else if (otherEntities[i].type == 'worker') {
						monsterImage.src = "../../images/worker.png";
					}
					else if (otherEntities[i].type == 'codebit') {
						monsterImage.src = "../../images/codebit.png";
					}
					else {
						monsterImage.src = "../../images/virus.png";
					}

					if (monsterReady) {
						ctx.drawImage(monsterImage, otherEntities[i].x, otherEntities[i].y);
					}
				}
			});

			if (heroReady) {
				ctx.drawImage(heroImage, hero.x, hero.y);
			}

			ctx.fillStyle = 'rgba(40,40,40,1)';
			ctx.strokeStyle = 'rgba(40,40,40,1)';
			ctx.lineWidth = 1;
			ctx.beginPath();
			ctx.rect(4,2,110,20);
			ctx.fill();
			ctx.stroke();

			ctx.beginPath();
			ctx.rect(4,24,110,20);
			ctx.fill();
			ctx.stroke();

			if (progressBar > 0) {
				ctx.fillStyle = 'rgba(40,40,40,1)';
				ctx.strokeStyle = 'rgba(40,40,40,1)';
				ctx.lineWidth = 1;
				ctx.beginPath();
				ctx.rect(4,46,110,20);
				ctx.fill();
				ctx.stroke();

				ctx.fillStyle = "rgb(0, 0, 250)";
				ctx.font = "14px monospace";
				ctx.textAlign = "left";
				ctxEffects.textBaseline = "top";
				for(hp=1;hp<progressBar/100;hp++) {
					ctx.fillText('|', 4 + hp, 60);
				}
			}

			for (hp=1;hp<=100;hp++) {
				if (hp <= hero.eeg) {
					ctx.fillStyle = "rgb(0, 0, 250)";
				}
				else {
					ctx.fillStyle = "rgb(250, 0, 0)";
				}
				ctx.font = "14px monospace";
				ctx.textAlign = "left";
				ctxEffects.textBaseline = "top";
				ctx.fillText('|', 4 + hp, 16);
			}

			for (hp=1;hp<=100;hp++) {
				if (hp <= hero.willpower) {
					ctx.fillStyle = "rgb(150, 0, 250)";
				}
				else {
					ctx.fillStyle = "rgb(250, 0, 0)";
				}
				ctx.font = "14px monospace";
				ctx.textAlign = "left";
				ctxEffects.textBaseline = "top";
				ctx.fillText('|', 4 + hp, 38);
			}

			if (showMenu === true) {
				
				renderMenuBG();

				ctxEffects.fillText('ROOM INFO', 32, 32);
				ctxEffects.fillText('===============', 32, 48);

				ctxEffects.fillText('name: ' + roomName, 32, 64);
				ctxEffects.fillText('type: ' + roomType, 32, 80);
				ctxEffects.fillText('user: ' + roomOwner, 32, 96);
				ctxEffects.fillText('level: ' + roomLevel, 32, 112);

				if (roomOwner != username && roomType == 'io') {
					ctxEffects.fillText('1) connect to home system', 32, 144);
				}

				if (roomOwner == username && roomType == 'io') {
					ctxEffects.fillText('2) connect to The Chatsubo', 32, 160);
				}

				if (roomOwner == username && northExit == '0' && hero.credits >= 100) {
					ctxEffects.fillText('3) create node north (100c)', 32, 176);
				}
				if (roomOwner == username && eastExit == '0' && hero.credits >= 100) {
					ctxEffects.fillText('4) create node east (100c)', 32, 192);
				}
				if (roomOwner == username && southExit == '0' && hero.credits >= 100) {
					ctxEffects.fillText('5) create node south (100c)', 32, 208);
				}
				if (roomOwner == username && westExit == '0' && hero.credits >= 100) {
					ctxEffects.fillText('6) create node west (100c)', 32, 224);
				}

				if (roomOwner == username && roomId != hero.homeId) {
					ctxEffects.fillText('7) modify node type', 32, 240);
				}

				if (roomOwner == username && roomId != hero.homeId && roomLevel < 8) {
					ctxEffects.fillText('8) upgrade node (' + (roomLevel * 1000) * roomLevel + ')', 32, 256);
				}

				if (roomType == 'coding' && getStorageUsed() < hero.maxStorage) {
					ctxEffects.fillText('9) code a program', 32, 272);
				}

				ctx.drawImage(canvasEffects, 0, 0);
			}
			else if (showNodeMenu === true) {
				renderMenuBG();
				ctxEffects.fillText('name: ' + roomName, 32, 32);
				ctxEffects.fillText('type: ' + roomType, 32, 48);
				ctxEffects.fillText('user: ' + roomOwner, 32, 64);
				ctxEffects.fillText('level: ' + roomLevel, 32, 80);

				if (roomType != 'firewall') {
					ctxEffects.fillText('1) firewall (750c)', 32, 112);
				}

				if (roomType != 'database') {
					ctxEffects.fillText('2) database (250c)', 32, 128);
				}

				if (roomType != 'terminal') {
					ctxEffects.fillText('3) terminal (250c)', 32, 144);
				}

				if (roomType != 'coproc') {
					ctxEffects.fillText('4) Co-processor (250c)', 32, 160);
				}

				if (roomType != 'coding') {
					ctxEffects.fillText('5) Coding (250c)', 32, 176);
				}

				ctx.drawImage(canvasEffects, 0, 0);
			}
			else if (showCharMenu === true) {
				renderMenuBG();
				ctxEffects.fillText('name: ' + username, 32, 32);
				ctxEffects.fillText('speed: ' + hero.speed, 32, 48);
				ctxEffects.fillText('credits: ' + hero.credits, 32, 64);
				ctxEffects.fillText('secrating: ' + hero.secrating, 32, 80);
				ctxEffects.fillText('snippets: ' + hero.snippets, 32, 96);

				ctxEffects.fillText('stealth: ' + (hero.stealth + hero.stealthBonus) + ' (+' + hero.stealthBonus + ')', 32, 128);
				ctxEffects.fillText('detect: ' + (hero.detect + hero.detectBonus) + ' (+' + hero.detectBonus + ')', 32, 144);
				ctxEffects.fillText('attack: ' + (hero.attack + hero.attackBonus) + ' (+' + hero.attackBonus + ')', 32, 160);
				ctxEffects.fillText('defend: ' + (hero.defend + hero.defendBonus) + ' (+' + hero.defendBonus + ')', 32, 176);
				ctxEffects.fillText('coding: ' + hero.coding, 32, 192);

				ctxEffects.fillText('eeg: ' + hero.eeg, 32, 224);
				ctxEffects.fillText('willpower: ' + hero.willpower, 32, 240);

				ctxEffects.fillText('storage: ' + getStorageUsed() + '/' + hero.maxStorage, 32, 272);
				ctxEffects.fillText('memory: ' + getMemoryUsed() + '/' + hero.maxMemory, 32, 288);

				ctxEffects.fillText('decking: ' + hero.decking, 32, 320);

				ctx.drawImage(canvasEffects, 0, 0);
			}
			else if (showProgramMenu === true) {
				renderMenuBG();
				ctxEffects.fillText('name: ' + username, 32, 32);

				ctxEffects.fillText('credits: ' + hero.credits, 32, 64);
				ctxEffects.fillText('snippets: ' + hero.snippets, 32, 80);
				ctxEffects.fillText('coding: ' + hero.coding, 32, 96);

				ctxEffects.fillText('t) stealth (100c)(10s)', 32, 128);
				ctxEffects.fillText('k) attack (100c)(10s)', 32, 144);
				ctxEffects.fillText('n) antivirus (250)(25s)', 32, 160);
				ctxEffects.fillText('p) detect (100c)(10s)', 32, 176);
				ctxEffects.fillText('f) defend (100c)(10s)', 32, 192);
				ctxEffects.fillText('b) eegbooster (100c)(10s)', 32, 208);
				ctxEffects.fillText('1) scanner (100c)(10s)', 32, 224);

				ctx.drawImage(canvasEffects, 0, 0);
			}
			else if (showMemoryMenu === true) {
				renderMenuBG();
				ctxEffects.textBaseline = "top";
				ctxEffects.fillText('ACTIVE MEMORY', 32, 32);
				ctxEffects.fillText('==============', 32, 48);

				var foundMemProgCounter = 0;

				jQuery.each(memoryPrograms, function(i, val) {
					if (memoryPrograms[i]) {
						++foundMemProgCounter;
						availableChoices[foundMemProgCounter] = {
							programId: memoryPrograms[i].id
						};
						ctxEffects.fillText(foundMemProgCounter + ') ' + memoryPrograms[i].name, 32, 112 + (foundMemProgCounter * 16));
					}
				});

				//ctxEffects.fillText('stealth: ' + storagePrograms[hero.stealthProgram].name, 32, 64);
				
				ctxEffects.fillText('r) load program into active memory', 32, 80);
				if (getMemoryUsed() > 0) {
					ctxEffects.fillText('u) unload all programs', 32, 96);
				}

				ctx.drawImage(canvasEffects, 0, 0);
			}
			else if (showInventoryMenu === true) {
				renderMenuBG();
				ctxEffects.fillText('STORAGE SPACE', 32, 32);
				ctxEffects.fillText('==============', 32, 48);

				var foundInvProgCounter = 0;

				jQuery.each(storagePrograms, function(i, val) {
					if (storagePrograms[i]) {
						++foundInvProgCounter;
						availableChoices[foundInvProgCounter] = {
							programId: storagePrograms[i].id
						};
						var loadedString = (storagePrograms[i].loaded == 1) ? ' (loaded)' : ' (not loaded)';
						ctxEffects.fillText(foundInvProgCounter + ') ' + storagePrograms[i].name + loadedString, 32, 64 + (foundInvProgCounter * 16));
					}
				});

				ctx.drawImage(canvasEffects, 0, 0);
			}
			else if (showItemMenu === true) {
				renderMenuBG();
				var programObject = storagePrograms[showMemProgId];
				ctxEffects.fillText(programObject.name, 32, 32);
				ctxEffects.fillText('=========================================', 32, 48);

				ctxEffects.fillText('type: ' + programObject.type, 32, 64);
				ctxEffects.fillText('rating: ' + programObject.rating, 32, 80);
				ctxEffects.fillText('condition: ' + programObject.condition, 32, 96);
				ctxEffects.fillText('type: ' + programObject.type, 32, 112);
				ctxEffects.fillText('max upgrades: ' + programObject.maxUpgrades, 32, 128);
				ctxEffects.fillText('upgrades: ' + programObject.upgrades, 32, 144);

				if (programObject.rating < 8 && programObject.loaded === 0) {
					ctxEffects.fillText('g) upgrade', 32, 176);
				}

				if (programObject.loaded == 1) {
					ctxEffects.fillText('u) unload', 32, 192);
				}

				if (programObject.loaded == 1) {
					if (programObject.type == 'eegbooster' || programObject.type == 'scanner') {
						ctxEffects.fillText('1) execute', 32, 208);
					}
				}
				
				ctx.drawImage(canvasEffects, 0, 0);
			}
			else if (showStorageMenu === true) {
				renderMenuBG();
				ctxEffects.fillText('Choose a program from storage memory:', 32, 32);

				var foundProgramsCounter = 0;

				jQuery.each(storagePrograms, function(i, val) {
					if (storagePrograms[i]) {
						if (storagePrograms[i].loaded != 1) {
							if (getMemoryUsed() + storagePrograms[i].rating <= hero.maxMemory) {
								++foundProgramsCounter;
								availableChoices[foundProgramsCounter] = {
									programId: storagePrograms[i].id
								};
								ctxEffects.fillText(foundProgramsCounter + ') ' + storagePrograms[i].name, 32, 64 + (foundProgramsCounter * 16));
							}
						}
					}
				});

				ctx.drawImage(canvasEffects, 0, 0);
			}
			else if (showVirusMenu === true) {
				renderMenuBG();
				ctxEffects.fillText('Attack which virus:', 32, 32);

				var foundVirusCounter = 0;

				jQuery.each(otherEntities, function(i, val) {
					if (otherEntities[i]) {
						if (otherEntities[i].roomId == hero.roomId && otherEntities[i].type == 'default' && otherEntities[i].eeg > 0) {
							++foundVirusCounter;
							availableChoices[foundVirusCounter] = {
								entityId: otherEntities[i].id
							};
							ctxEffects.fillText(foundVirusCounter + ') ' + ((otherEntities[i].type == 'default') ? 'virus' : otherEntities[i].type) + '(' + otherEntities[i].eeg + ')', 32, 64 + (foundVirusCounter * 16));
						}
					}
				});

				ctx.drawImage(canvasEffects, 0, 0);
			}
			else {
				ctxEffects.clearRect ( 0 , 0 , canvas.width , canvas.height );
			}

		};