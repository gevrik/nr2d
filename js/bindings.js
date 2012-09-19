$(document).bind('keyup', 'b', function(){
	if (!$('.message').hasFocus) {
		if (showProgramMenu === true) {
			progressBar = 2500;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = 'eegbooster';
			showProgramMenu = false;
		}
	}
});

$(document).bind('keyup', 'c', function(){
	if (!$('.message').hasFocus && progressBar === 0) {
		closeAllMenus('char');
		if (showCharMenu === false) {
			showCharMenu = true;
		} else {
			showCharMenu = false;
		}
	}
});

$(document).bind('keyup', 'e', function(){
	if (!$('.message').hasFocus && progressBar === 0) {
		closeAllMenus('inventory');
		if (showInventoryMenu === false) {

			var programCounter = 0;
			maxPage = 1;
			currentPage = 1;

			jQuery.each(storagePrograms, function(i, val) {
				if (storagePrograms[i]) {
					++programCounter;

					pageArray.push({
						id: storagePrograms[i].id,
						page: currentPage,
						hotkey: programCounter,
						name: storagePrograms[i].name
					});

					if (programCounter % 9 === 0 ) {
						++maxPage;
						++currentPage;
						
						programCounter = 0;
					}
				}
			});
			currentPage = 1;
			showInventoryMenu = true;
			
		} else {
			pageArray = [];
			currentPage = 1;
			maxPage = 1;
			showInventoryMenu = false;
		}
	}
});

$(document).bind('keyup', 'f', function(){
	if (!$('.message').hasFocus) {
		if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = 'defend';
			showProgramMenu = false;
		}
	}
});

$(document).bind('keyup', 'i', function(){
	if (!$('.message').hasFocus && progressBar === 0) {
		closeAllMenus('info');
		if (showMenu === false) {
			showMenu = true;
		} else {
			showMenu = false;
		}
	}
});

$(document).bind('keyup', 'k', function(){
	if (!$('.message').hasFocus) {
		if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = 'attack';
			showProgramMenu = false;
		}
	}
});

$(document).bind('keyup', 'l', function(){
	if (!$('.message').hasFocus) {
		if (showItemMenu === true) {
			progressBar = 2000;
			barOriginal = progressBar;
			barCommand = 'LOADPROGRAM';
			barParam = showMemProgId;
			showMemProgId = 0;
			showItemMenu = false;
		}
	}
});

$(document).bind('keyup', 'n', function(){
	if (!$('.message').hasFocus) {
		if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = 'antivirus';
			showProgramMenu = false;
		}
		else if (showStorageMenu === true) {
			if (currentPage < maxPage) {
				++currentPage;
			}
			availableChoices = [];
		}
		else if (showInventoryMenu === true) {
			if (currentPage < maxPage) {
				++currentPage;
			}
			availableChoices = [];
		}
	}
});

$(document).bind('keyup', 'p', function(){
	if (!$('.message').hasFocus) {
		if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = 'detect';
			showProgramMenu = false;
		}
		else if (showStorageMenu === true) {
			--currentPage;
			if (currentPage < 1) {
				currentPage = 1;
			}
			availableChoices = [];
		}
		else if (showInventoryMenu === true) {
			--currentPage;
			if (currentPage < 1) {
				currentPage = 1;
			}
			availableChoices = [];
		}
	}
});


$(document).bind('keyup', 'r', function(){
	if (!$('.message').hasFocus && progressBar === 0) {
		closeAllMenus('memory');
		if (showMemoryMenu === false) {
			showMemoryMenu = true;
		}
		else {
			if (usedSlots < hero.slots) {
				showMemoryMenu = false;
				var programCounter = 0;
				currentPage = 1;
				maxPage = 1;

				jQuery.each(storagePrograms, function(i, val) {
					if (storagePrograms[i]) {
						if (storagePrograms[i].loaded != 1) {
							if (getMemoryUsed() + storagePrograms[i].rating <= hero.maxMemory) {
								++programCounter;

								pageArray.push({
									id: storagePrograms[i].id,
									page: currentPage,
									hotkey: programCounter,
									name: storagePrograms[i].name
								});

								if (programCounter % 9 === 0 ) {
									++maxPage;
									++currentPage;
									
									programCounter = 0;
								}
							}
						}
					}
				});
				currentPage = 1;
				showStorageMenu = true;
			}
			else {
				showMemoryMenu = false;
			}
		}
	}
});


$(document).bind('keyup', 't', function(){
	if (!$('.message').hasFocus) {
		if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = 'stealth';
			showProgramMenu = false;
		}
	}
});

$(document).bind('keyup', 'u', function(){
	if (!$('.message').hasFocus) {
		if (showMemoryMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'UNLOADALL';
			showMemoryMenu = false;
		}
		else if (showItemMenu === true) {
			progressBar = 2000;
			barOriginal = progressBar;
			barCommand = 'UNLOADPROGRAM';
			barParam = showMemProgId;
			showMemProgId = 0;
			showItemMenu = false;
		}
	}
});


$(document).bind('keyup', 'v', function(){
	if (!$('.message').hasFocus && progressBar === 0) {
		closeAllMenus('virus');
		if (showVirusMenu === false) {
			showVirusMenu = true;
		} else {
			showVirusMenu = false;
		}
	}
});

$(document).bind('keyup', 'x', function(){
	if (!$('.message').hasFocus) {
		closeAllMenus();
		availableChoices = [];
		pageArray = [];
		currentPage = 1;
		maxPage = 1;
	}
});

$(document).bind('keyup', 'esc', function(){
	if (!$('.message').hasFocus) {
		closeAllMenus();
		availableChoices = [];
		pageArray = [];
		currentPage = 1;
		maxPage = 1;
	}
});

$(document).bind('keyup', '1', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 5000;
			barOriginal = progressBar;
			barCommand = 'RECALL';
			showMenu = false;
		}
		else if (showItemMenu === true) {
			if (canExecute) {
				progressBar = 1000;
				barOriginal = progressBar;
				barCommand = 'EXECUTEPROGRAM';
				barParam = showMemProgId;
				showMemProgId = 0;
				showItemMenu = false;
				canExecute = false;
			}
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = 'firewall';
			//send('MODIFYNODE firewall');
			showNodeMenu = false;
		}
		else if (showStorageMenu === true) {
			if (availableChoices[1]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'LOADPROGRAM';
				barParam = availableChoices[1].programId;
				availableChoices = [];
				showStorageMenu = false;
			}
		}
		else if (showVirusMenu === true) {
			if (availableChoices[1]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'ATTACKVIRUS';
				barParam = availableChoices[1].entityId + " " + getVirusDamage();
				availableChoices = [];
			}
			showVirusMenu = false;
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[1].programId;
			availableChoices = [];
			showMemoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[1].programId;
			availableChoices = [];
			showInventoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = 'scanner';
			showProgramMenu = false;
		}
	}
});

$(document).bind('keyup', '2', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 5000;
			barOriginal = progressBar;
			barCommand = 'CHATSUBO';
			showMenu = false;
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = 'database';
			//send('MODIFYNODE database');
			showNodeMenu = false;
		}
		else if (showStorageMenu === true) {
			if (availableChoices[2]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'LOADPROGRAM';
				barParam = availableChoices[2].programId;
				availableChoices = [];
				showStorageMenu = false;
			}
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[2].programId;
			availableChoices = [];
			showMemoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[2].programId;
			availableChoices = [];
			showInventoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showVirusMenu === true) {
			if (availableChoices[2]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'ATTACKVIRUS';
				barParam = availableChoices[2].entityId + " " + getVirusDamage();
				availableChoices = [];
			}
			showVirusMenu = false;
		}
	}
});

$(document).bind('keyup', '3', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATENODE';
			barParam = 'north';
			//send( 'CREATENODE north');
			showMenu = false;
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = 'terminal';
			//send('MODIFYNODE terminal');
			showNodeMenu = false;
		}
		else if (showStorageMenu === true) {
			if (availableChoices[3]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'LOADPROGRAM';
				barParam = availableChoices[3].programId;
				availableChoices = [];
				showStorageMenu = false;
			}
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[3].programId;
			availableChoices = [];
			showMemoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[3].programId;
			availableChoices = [];
			showInventoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showVirusMenu === true) {
			if (availableChoices[3]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'ATTACKVIRUS';
				barParam = availableChoices[3].entityId + " " + getVirusDamage();
				availableChoices = [];
			}
			showVirusMenu = false;
		}
	}
});

$(document).bind('keyup', '4', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATENODE';
			barParam = 'east';
			//send( 'CREATENODE east');
			showMenu = false;
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = 'coproc';
			//send('MODIFYNODE coproc');
			showNodeMenu = false;
		}
		else if (showStorageMenu === true) {
			if (availableChoices[4]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'LOADPROGRAM';
				barParam = availableChoices[4].programId;
				availableChoices = [];
				showStorageMenu = false;
			}
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[4].programId;
			availableChoices = [];
			showMemoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[4].programId;
			availableChoices = [];
			showInventoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showVirusMenu === true) {
			if (availableChoices[4]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'ATTACKVIRUS';
				barParam = availableChoices[4].entityId + " " + getVirusDamage();
				availableChoices = [];
			}
			showVirusMenu = false;
		}
	}
});

$(document).bind('keyup', '5', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATENODE';
			barParam = 'south';
			//send( 'CREATENODE south');
			showMenu = false;
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = 'coding';
			showNodeMenu = false;
		}
		else if (showStorageMenu === true) {
			if (availableChoices[5]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'LOADPROGRAM';
				barParam = availableChoices[5].programId;
				availableChoices = [];
				showStorageMenu = false;
			}
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[5].programId;
			availableChoices = [];
			showMemoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[5].programId;
			availableChoices = [];
			showInventoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showVirusMenu === true) {
			if (availableChoices[5]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'ATTACKVIRUS';
				barParam = availableChoices[5].entityId + " " + getVirusDamage();
				availableChoices = [];
			}
			showVirusMenu = false;
		}
	}
});

$(document).bind('keyup', '6', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATENODE';
			barParam = 'west';
			//send( 'CREATENODE west');
			showMenu = false;
		}
		else if (showStorageMenu === true) {
			if (availableChoices[6]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'LOADPROGRAM';
				barParam = availableChoices[6].programId;
				availableChoices = [];
				showStorageMenu = false;
			}
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[6].programId;
			availableChoices = [];
			showMemoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[6].programId;
			availableChoices = [];
			showInventoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showVirusMenu === true) {
			if (availableChoices[6]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'ATTACKVIRUS';
				barParam = availableChoices[6].entityId + " " + getVirusDamage();
				availableChoices = [];
			}
			showVirusMenu = false;
		}
	}
});

$(document).bind('keyup', '7', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			showMenu = false;
			showNodeMenu = true;
		}
		else if (showStorageMenu === true) {
			if (availableChoices[7]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'LOADPROGRAM';
				barParam = availableChoices[7].programId;
				availableChoices = [];
				showStorageMenu = false;
			}
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[7].programId;
			availableChoices = [];
			showMemoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[7].programId;
			availableChoices = [];
			showInventoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showVirusMenu === true) {
			if (availableChoices[7]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'ATTACKVIRUS';
				barParam = availableChoices[7].entityId + " " + getVirusDamage();
				availableChoices = [];
			}
			showVirusMenu = false;
		}
	}
});

$(document).bind('keyup', '8', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			showMenu = false;
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'UPGRADENODE';
		}
		else if (showStorageMenu === true) {
			if (availableChoices[8]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'LOADPROGRAM';
				barParam = availableChoices[8].programId;
				availableChoices = [];
				showStorageMenu = false;
			}
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[8].programId;
			availableChoices = [];
			showMemoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[8].programId;
			availableChoices = [];
			showInventoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showVirusMenu === true) {
			if (availableChoices[8]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'ATTACKVIRUS';
				barParam = availableChoices[8].entityId + " " + getVirusDamage();
				availableChoices = [];
			}
			showVirusMenu = false;
		}
	}
});

$(document).bind('keyup', '9', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			showMenu = false;
			showProgramMenu = true;
		}
		else if (showStorageMenu === true) {
			if (availableChoices[9]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'LOADPROGRAM';
				barParam = availableChoices[9].programId;
				availableChoices = [];
				showStorageMenu = false;
			}
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[9].programId;
			availableChoices = [];
			showMemoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[9].programId;
			availableChoices = [];
			showInventoryMenu = false;
			//console.log(showMemProgId);
			showItemMenu = true;
		}
		else if (showVirusMenu === true) {
			if (availableChoices[9]) {
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'ATTACKVIRUS';
				barParam = availableChoices[9].entityId + " " + getVirusDamage();
				availableChoices = [];
			}
			showVirusMenu = false;
		}
	}
});