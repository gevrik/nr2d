// Draw everything
var render = function () {

	if (gameReady) {

		ctx.clearRect ( 0 , 0 , canvas.width , canvas.height );
		ctxEffects.clearRect ( 0 , 0 , canvas.width , canvas.height );
		ctxLog.clearRect ( 0 , 0 , canvas.width , canvas.height );

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

			case 'hacking':
				roomTypeImage.src = "../../images/hacking.png";
			break;

			default:
				roomTypeImage.src = "../../images/ph.png";
			break;

		}

		if (music) {
			//bgmSound.play();
		}
		else {
			//bgmSound.pause();
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
			if (bullets[i] && bullets[i].roomId == hero.roomId) {
				if (bulletReady) {
					ctx.beginPath();
					ctx.arc(bullets[i].currentX, bullets[i].currentY, 8, 0, 2 * Math.PI, false);
					ctx.fillStyle = "rgb(255, 0, 0)";
					ctx.fill();
					ctx.drawImage(bulletImage, bullets[i].currentX - 32, bullets[i].currentY - 32);
				}
			}
		});

		jQuery.each(bombs, function(i, val) {
			if (bombs[i] && bombs[i].roomId == hero.roomId) {
				if (bombReady) {
					ctx.beginPath();
					ctx.arc(bombs[i].x, bombs[i].y, 64, 0, 2 * Math.PI, false);
					if (bombs[i].userId == hero.userId) {
						ctx.fillStyle = "rgba(0, 255, 0, 0.5)";
					}
					else {
						ctx.fillStyle = "rgba(255, 0, 0, 0.5)";
					}
					ctx.fill();
					ctx.drawImage(bombImage, bombs[i].x - 16, bombs[i].y - 16);
				}
			}
		});

		jQuery.each(otherUsers, function(i, val) {
			if (otherReady) {
				if (otherUsers[i] && otherUsers[i].roomId == hero.roomId){
					//console.log('rendering other player');
					ctx.drawImage(otherImage, otherUsers[i].x - 16, otherUsers[i].y - 16);
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
				else if (otherEntities[i].type == 'accesscode') {
					monsterImage.src = "../../images/accesscode.png";
				}
				else if (otherEntities[i].type == 'gimp') {
					monsterImage.src = "../../images/gimp.png";
				}
				else {
					monsterImage.src = "../../images/virus.png";
				}

				if (i == selectedEntity && selectedEntity != -1) {
					ctx.beginPath();
					ctx.arc(otherEntities[i].x, otherEntities[i].y, 18, 0, 2 * Math.PI, false);
					ctx.fillStyle = "rgb(255, 255, 255)";
					ctx.fill();
				}

				ctx.beginPath();
				ctx.arc(otherEntities[i].x, otherEntities[i].y, 16, 0, 2 * Math.PI, false);
				ctx.fillStyle = (otherEntities[i].userId == holygrail) ? "rgb(0, 255, 0)" : "rgb(255, 0, 0)";
				ctx.fill();

				ctx.fillStyle = "rgb(250, 250, 250)";
				ctx.font = "8px monospace";
				ctx.textAlign = "middle";
				ctx.textBaseline = "top";
				ctx.fillText(otherEntities[i].eeg, otherEntities[i].x, otherEntities[i].y - 32);

				var canSeeOther = true;

				// if (otherEntities[i].userId == hero.userId) {
				// 	canSeeOther = true;
				// }
				// else {
				// 	if (canSee(otherEntities[i], hero)) {
				// 		canSeeOther = true;
				// 	}
				// }

				if (monsterReady && canSeeOther) {
					ctx.drawImage(monsterImage, otherEntities[i].x - 16, otherEntities[i].y - 16);
				}

			}
		});

		if (heroReady) {
			if (selectedEntity == -1) {
				ctx.beginPath();
				ctx.arc(hero.x, hero.y, 20, 0, 2 * Math.PI, false);
				ctx.fillStyle = "rgb(255, 255, 255)";
				ctx.fill();
			}
			if (combatMode === true) {
				ctx.beginPath();
				ctx.arc(hero.x, hero.y, 18, 0, 2 * Math.PI, false);
				ctx.fillStyle = "rgb(255, 0, 0)";
				ctx.fill();
			}
			ctx.beginPath();
			ctx.arc(hero.x, hero.y, 16, 0, 2 * Math.PI, false);
			ctx.fillStyle = "rgb(0, 0, 255)";
			ctx.fill();
			ctx.drawImage(heroImage, hero.x - 16, hero.y - 16);
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

		if (showLog === true) {
			showLogUI();
			ctx.drawImage(canvasLog, 0, 0);
		}

		if (showMenu === true) {
			showMenuUI();
			ctx.drawImage(canvasEffects, 0, 0);
		}
		else if (showNodeMenu === true) {
			showNodeMenuUI();
			ctx.drawImage(canvasEffects, 0, 0);
		}
		else if (showCharMenu === true) {
			showCharMenuUI();
			ctx.drawImage(canvasEffects, 0, 0);
		}
		else if (showProgramMenu === true) {
			showProgramMenuUI();
			ctx.drawImage(canvasEffects, 0, 0);
		}
		else if (showMemoryMenu === true) {
			showMemoryMenuUI();
			ctx.drawImage(canvasEffects, 0, 0);
		}
		else if (showInventoryMenu === true) {
			showInventoryMenuUI();
			ctx.drawImage(canvasEffects, 0, 0);
		}
		else if (showItemMenu === true) {
			showItemMenuUI();
			ctx.drawImage(canvasEffects, 0, 0);
		}
		else if (showStorageMenu === true) {
			showStorageMenuUI();
			ctx.drawImage(canvasEffects, 0, 0);
		}
		else if (showACMenu === true) {
			showACMenuUI();
			ctx.drawImage(canvasEffects, 0, 0);
		}
		else {
			ctxEffects.clearRect ( 0 , 0 , canvas.width , canvas.height );
		}
	}
	else {
		ctx.clearRect ( 0 , 0 , canvas.width , canvas.height );

		if (bgReady) {
			ctx.drawImage(loadingImage, 0, 0);
		}
	}

};

var showMenuUI = function() {
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
};

var showNodeMenuUI = function() {
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

	if (roomType != 'hacking') {
		ctxEffects.fillText('6) Hacking (250c)', 32, 192);
	}
};

var showCharMenuUI = function() {
	renderMenuBG();
	ctxEffects.fillText('name: ' + username, 32, 32);

	ctxEffects.fillText('eeg: ' + hero.eeg, 32, 64);
	ctxEffects.fillText('willpower: ' + hero.willpower, 32, 80);

	ctxEffects.fillText('secrating: ' + hero.secrating, 32, 112);

	ctxEffects.fillText('credits: ' + hero.credits, 32, 144);
	ctxEffects.fillText('snippets: ' + hero.snippets, 32, 160);

	ctxEffects.fillText('speed: ' + hero.speed, 32, 192);
	ctxEffects.fillText('attack speed: ' + hero.attackspeed, 32, 208);
	ctxEffects.fillText('decking: ' + hero.decking, 32, 224);
	ctxEffects.fillText('coding: ' + hero.coding, 32, 240);

	ctxEffects.fillText('stealth: ' + (hero.stealth + hero.stealthBonus) + ' (+' + hero.stealthBonus + ')', 32, 272);
	ctxEffects.fillText('detect: ' + (hero.detect + hero.detectBonus) + ' (+' + hero.detectBonus + ')', 32, 288);
	ctxEffects.fillText('attack: ' + (hero.attack + hero.attackBonus) + ' (+' + hero.attackBonus + ')', 32, 304);
	ctxEffects.fillText('defend: ' + (hero.defend + hero.defendBonus) + ' (+' + hero.defendBonus + ')', 32, 320);

	ctxEffects.fillText('storage: ' + getStorageUsed() + '/' + hero.maxStorage, 32, 352);
	ctxEffects.fillText('memory: ' + getMemoryUsed() + '/' + hero.maxMemory, 32, 368);

	if (roomType == 'io') {
		ctxEffects.fillText('1) access codes', canvas.width / 3, 32);
	}
};

var showProgramMenuUI = function() {
	renderMenuBG();
	ctxEffects.fillText('name: ' + username, 32, 32);

	ctxEffects.fillText('credits: ' + hero.credits, 32, 64);
	ctxEffects.fillText('snippets: ' + hero.snippets, 32, 80);
	ctxEffects.fillText('coding: ' + hero.coding, 32, 96);

	ctxEffects.fillText('t) stealth booster (100c)(10s)', 32, 128);
	ctxEffects.fillText('k) attack booster (100c)(10s)', 32, 144);
	ctxEffects.fillText('n) antivirus (250)(25s)', 32, 160);
	ctxEffects.fillText('p) detect booster (100c)(10s)', 32, 176);
	ctxEffects.fillText('f) defend booster (100c)(10s)', 32, 192);
	ctxEffects.fillText('b) eeg recover (100c)(10s)', 32, 208);
	ctxEffects.fillText('1) scanner (100c)(10s)', 32, 224);
	ctxEffects.fillText('2) dataminer (100c)(10s)', 32, 240);
	ctxEffects.fillText('3) radblaster (100000c)(1000s)', 32, 256);
	ctxEffects.fillText('4) gimp (1000c)(100s)', 32, 272);
	ctxEffects.fillText('5) dropline (100c)(10s)', 32, 288);
	ctxEffects.fillText('6) logicbomb (100c)(10s)', 32, 304);
};

var showMemoryMenuUI = function() {
	renderMenuBG();
	ctxEffects.textBaseline = "top";
	ctxEffects.fillText('ACTIVE MEMORY (' + getMemoryUsed() + '/' + hero.maxMemory + ') | (' + usedSlots + '/' + hero.slots + ')', 32, 32);
	ctxEffects.fillText('============================', 32, 48);

	var foundMemProgCounter = 0;

	jQuery.each(memoryPrograms, function(i, val) {
		if (memoryPrograms[i] && foundMemProgCounter < hero.slots) {
			++foundMemProgCounter;
			availableChoices[foundMemProgCounter] = {
				programId: memoryPrograms[i].id
			};
			ctxEffects.fillText(foundMemProgCounter + ') ' + memoryPrograms[i].name, 32, 112 + (foundMemProgCounter * 16));
		}
	});

	if (getMemoryUsed() > 0) {
		ctxEffects.fillText('u) unload all programs', 32, 80);
	}
};

var showACMenuUI = function() {
	renderMenuBG();
	ctxEffects.textBaseline = "top";
	ctxEffects.fillText('AVAILABLE ACCESS CODES', 32, 32);
	ctxEffects.fillText('============================', 32, 48);

	var foundInvProgCounter = 0;
	var shownPrevPage = 0;

	jQuery.each(accessCodes, function(i, val) {
		if (pageArray[i] && pageArray[i].page == currentPage) {
			++foundInvProgCounter;
			availableChoices[foundInvProgCounter] = {
				roomId: pageArray[i].roomId
			};
			ctxEffects.fillText(pageArray[i].hotkey + ') ' + pageArray[i].roomName, 32, 64 + (pageArray[i].hotkey * 16));
			if (foundInvProgCounter == 9 && currentPage < maxPage) {
				ctxEffects.fillText('n) next page', 32, 96 + (pageArray[i].hotkey * 16));
			}
			if (currentPage > 1 && shownPrevPage === 0) {
				shownPrevPage = 1;
				ctxEffects.fillText('p) previous page', 32, 112 + (pageArray[i].hotkey * 16));
			}
		}
	});
};

var showInventoryMenuUI = function() {
	renderMenuBG();
	ctxEffects.fillText('STORAGE SPACE (' + getStorageUsed() + '/' + hero.maxStorage + ')' , 32, 32);
	ctxEffects.fillText('=====================', 32, 48);

	var foundInvProgCounter = 0;
	var shownPrevPage = 0;

	jQuery.each(pageArray, function(i, val) {
		if (pageArray[i].page == currentPage) {
			++foundInvProgCounter;
			availableChoices[foundInvProgCounter] = {
			programId: pageArray[i].id
			};
			var loadedString = (storagePrograms[pageArray[i].id].loaded == 1) ? ' (*)' : '';
			ctxEffects.fillText(pageArray[i].hotkey + ') ' + pageArray[i].name + loadedString, 32, 64 + (pageArray[i].hotkey * 16));
			if (foundInvProgCounter == 9 && currentPage < maxPage) {
				ctxEffects.fillText('n) next page', 32, 96 + (pageArray[i].hotkey * 16));
			}
			if (currentPage > 1 && shownPrevPage === 0) {
				shownPrevPage = 1;
				ctxEffects.fillText('p) previous page', 32, 112 + (pageArray[i].hotkey * 16));
			}
		}
	});
};

var showItemMenuUI = function() {
	renderMenuBG();
	var programObject = storagePrograms[showMemProgId];
	ctxEffects.fillText(programObject.name, 32, 32);
	ctxEffects.fillText('=========================================', 32, 48);

	ctxEffects.fillText('type: ' + programObject.type, 32, 64);
	ctxEffects.fillText('rating: ' + programObject.rating, 32, 80);
	ctxEffects.fillText('condition: ' + programObject.condition, 32, 96);
	ctxEffects.fillText('type: ' + programObject.type, 32, 112);

	if (programObject.type != 'dataminer' &&
		programObject.type != 'scanner' &&
		programObject.type != 'eegbooster' ) {
			ctxEffects.fillText('max upgrades: ' + programObject.maxUpgrades, 32, 128);
			ctxEffects.fillText('upgrades: ' + programObject.upgrades, 32, 144);
	}

	if (programObject.rating < 8 &&
		programObject.loaded === 0 &&
		programObject.upgrades < programObject.maxUpgrades &&
		hero.credits >= (programObject.rating * programObject.rating) * 1000 &&
		hero.snippets >= (programObject.rating * programObject.rating) * 10 &&
		programObject.type != 'dataminer' &&
		programObject.type != 'scanner' &&
		programObject.type != 'eegbooster' &&
		programObject.type != 'dropline') {
		var upgradeCredCost = (programObject.rating * programObject.rating) * 1000;
		var upgradeSnipCost = (programObject.rating * programObject.rating) * 10;
		ctxEffects.fillText('g) upgrade (' + upgradeCredCost + 'c) (' + upgradeSnipCost + 's)', 32, 176);
	}

	if (programObject.loaded == 1) {
		ctxEffects.fillText('u) unload', 32, 192);
	} else {
		if (getMemoryUsed() + programObject.rating <= hero.maxMemory && usedSlots < hero.slots) {
			ctxEffects.fillText('l) load', 32, 192);
		}
		ctxEffects.fillText('t) delete', 32, 208);
	}

};

var showStorageMenuUI = function() {
	renderMenuBG();
	ctxEffects.fillText('Choose a program from storage memory:', 32, 32);

	//console.log(pageArray);

	var foundProgramsCounter = 0;
	var shownPrevPage = 0;

	jQuery.each(pageArray, function(i, val) {
		if (pageArray[i].page == currentPage) {
			++foundProgramsCounter;
			availableChoices[foundProgramsCounter] = {
			programId: pageArray[i].id
			};
	
			ctxEffects.fillText(pageArray[i].hotkey + ') ' + pageArray[i].name, 32, 64 + (pageArray[i].hotkey * 16));
			if (foundProgramsCounter == 9 && currentPage < maxPage) {
				ctxEffects.fillText('n) next page', 32, 96 + (pageArray[i].hotkey * 16));
			}
			if (currentPage > 1 && shownPrevPage === 0) {
				shownPrevPage = 1;
				ctxEffects.fillText('p) previous page', 32, 112 + (pageArray[i].hotkey * 16));
			}
		}
	});
};

var showLogUI = function() {
	var offset = 1;
	//console.log(logText);
	ctxLog.clearRect ( 0 , 0 , canvas.width , canvas.height );

	ctxLog.strokeStyle = 'rgba(20,20,20,0.5)';
	ctxLog.fillStyle = 'rgba(0,0,0,0.25)';
	ctxLog.lineWidth = 5;
	ctxLog.beginPath();
	ctxLog.rect(32,canvas.height - 168,canvas.width - 64, 160);
	ctxLog.fill();
	ctxLog.stroke();

	ctxLog.fillStyle = "rgb(0, 250, 250)";
	ctxLog.font = "12px monospace";
	ctxLog.textAlign = "left";
	ctxLog.textBaseline = "top";

	jQuery.each(logText, function(i, val) {
		ctxLog.fillText(logText[i].xvalue, 32, canvas.height - (14 * offset + 14));
		++offset;
	});
};