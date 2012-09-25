$(document).bind('keyup', 'b', function(){
	if (!$('.message').hasFocus) {
		if (showProgramMenu === true) {
			progressBar = 2000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = {
				type: 'eegbooster'
			};
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
			pageArray = [];
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
			barParam = {
				type: 'defend'
			};
			showProgramMenu = false;
		}
	}
});

$(document).bind('keyup', 'g', function(){
	if (!$('.message').hasFocus) {
		if (showItemMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'UPGRADEPROGRAM';
			barParam = {
				id: showMemProgId
			};
			showMemProgId = 0;
			showItemMenu = false;
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
			barParam = {
				type: 'attack'
			};
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
			barParam = {
				id: showMemProgId
			};
			showMemProgId = 0;
			showItemMenu = false;
		}
	}
});

$(document).bind('keyup', 'm', function(){
	if (music) {
		music = false;
	}
	else {
		music = true;
	}
});

$(document).bind('keyup', 'n', function(){
	if (!$('.message').hasFocus) {
		if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = {
				type: 'antivirus'
			};
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
			barParam = {
				type: 'detect'
			};
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
			showMemoryMenu = false;
		}
	}
});

$(document).bind('keyup', 't', function(){
	if (!$('.message').hasFocus) {
		if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = {
				type: 'stealth'
			};
			showProgramMenu = false;
		}
		else if (showItemMenu === true) {
			progressBar = 2000;
			barOriginal = progressBar;
			barCommand = 'DELETEPROGRAM';
			barParam = showMemProgId;

			pageArray = [];
			currentPage = 1;
			maxPage = 1;

			delete storagePrograms[showMemProgId];
			showMemProgId = 0;

			showItemMenu = false;
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
			barParam = {
				id: showMemProgId
			};
			showMemProgId = 0;
			showItemMenu = false;
		}
	}
});


$(document).bind('keyup', 'v', function(){
	if (!$('.message').hasFocus && progressBar === 0) {
		closeAllMenus();
		if (roomType != 'io') {
			if (combatMode === true) {
				combatMode = false;
				logIG('> combat mode disabled');
			}
			else {
				combatMode = true;
				logIG('> combat mode enabled');
			}
		} else {
			logIG('> combat mode not available in io nodes');
			log('> combat mode not available in io nodes');
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
		showMemProgId = 0;
		selectedEntity = 0;
	}
});

$(document).bind('keyup', 'esc', function(){
	if (!$('.message').hasFocus) {
		closeAllMenus();
		availableChoices = [];
		pageArray = [];
		currentPage = 1;
		maxPage = 1;
		showMemProgId = 0;
		selectedEntity = 0;
	}
});

$(document).bind('keyup', '1', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			if (roomType == 'io') {
				progressBar = 5000;
				barOriginal = progressBar;
				barCommand = 'RECALL';
			}
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = {
				type: 'firewall'
			};
		}
		else if (showACMenu === true) {
			progressBar = 5000;
			barOriginal = progressBar;
			barCommand = 'CONNECT';
			barParam = availableChoices[1].roomId;
			console.log(availableChoices[1].roomId);
			availableChoices = [];
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[1].programId;
			availableChoices = [];
			showMemoryMenu = false;
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[1].programId;
			availableChoices = [];
			showInventoryMenu = false;
			showItemMenu = true;
		}
		else if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = {
				type: 'scanner'
			};
		}
		else if (showCharMenu === true) {
			progressBar = 2000;
			barOriginal = progressBar;
			console.log('showac');
			barCommand = 'SHOWAC';
			barParam = 0;
			showCharMenu = false;
		}
		else if (selectedEntity !== 0) {
			var currentProgram = 0;
			var executeProgram = 0;

			jQuery.each(memoryPrograms, function(i, val) {
				if (memoryPrograms[i]) {
					++currentProgram;
					if (currentProgram == 1) {
						executeProgram = memoryPrograms[i].id;
					}
				}
			});

			if (executeProgram !== 0 && progressBar === 0) {
				console.log('shortcut 1');
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'EXECUTEPROGRAM';
				barParam = {
					programId: executeProgram,
					entityId: selectedEntity
				};
				showMemProgId = executeProgram;
			}
		}
	}
});

$(document).bind('keyup', '2', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			if (roomType == 'io') {
				progressBar = 5000;
				barOriginal = progressBar;
				barCommand = 'CHATSUBO';
				showMenu = false;
			}
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = {
				type: 'database'
			};
			showNodeMenu = false;
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[2].programId;
			availableChoices = [];
			showMemoryMenu = false;
			showItemMenu = true;
		}
		else if (showACMenu === true) {
			progressBar = 5000;
			barOriginal = progressBar;
			barCommand = 'CONNECT';
			barParam = availableChoices[2].roomId;
			console.log(availableChoices[2].roomId);
			availableChoices = [];
		}
		else if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = {
				type: 'dataminer'
			};
			showProgramMenu = false;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[2].programId;
			availableChoices = [];
			showInventoryMenu = false;
			showItemMenu = true;
		}
		else if (selectedEntity !== 0) {
			var currentProgram = 0;
			var executeProgram = 0;

			jQuery.each(memoryPrograms, function(i, val) {
				if (memoryPrograms[i]) {
					++currentProgram;
					if (currentProgram == 2) {
						executeProgram = memoryPrograms[i].id;
					}
				}
			});

			if (executeProgram !== 0 && progressBar === 0) {
				console.log('shortcut 2');
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'EXECUTEPROGRAM';
				barParam = {
					programId: executeProgram,
					entityId: selectedEntity
				};
				showMemProgId = executeProgram;
			}
		}
	}
});

$(document).bind('keyup', '3', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATENODE';
			barParam = {
				direction: 'north'
			};
			showMenu = false;
		}
		else if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = {
				type: 'radblaster'
			};
			showProgramMenu = false;
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = {
				type: 'terminal'
			};
			showNodeMenu = false;
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[3].programId;
			availableChoices = [];
			showMemoryMenu = false;
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[3].programId;
			availableChoices = [];
			showInventoryMenu = false;
			showItemMenu = true;
		}
		else if (selectedEntity !== 0) {
			var currentProgram = 0;
			var executeProgram = 0;

			jQuery.each(memoryPrograms, function(i, val) {
				if (memoryPrograms[i]) {
					++currentProgram;
					if (currentProgram == 3) {
						executeProgram = memoryPrograms[i].id;
					}
				}
			});

			if (executeProgram !== 0 && progressBar === 0) {
				console.log('shortcut 3');
				progressBar = 2000;
				barOriginal = progressBar;
				barCommand = 'EXECUTEPROGRAM';
				barParam = {
					programId: executeProgram,
					entityId: selectedEntity
				};
				showMemProgId = executeProgram;
			}
		}
	}
});

$(document).bind('keyup', '4', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATENODE';
			barParam = {
				direction: 'east'
			};
			showMenu = false;
		}
		else if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = {
				type: 'gimp'
			};
			showProgramMenu = false;
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = {
				type: 'coproc'
			};
			showNodeMenu = false;
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[4].programId;
			availableChoices = [];
			showMemoryMenu = false;
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[4].programId;
			availableChoices = [];
			showInventoryMenu = false;
			showItemMenu = true;
		}
	}
});

$(document).bind('keyup', '5', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATENODE';
			barParam = {
				direction: 'south'
			};
			showMenu = false;
		}
		else if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = {
				type: 'dropline'
			};
			showProgramMenu = false;
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = {
				type: 'coding'
			};
			showNodeMenu = false;
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[5].programId;
			availableChoices = [];
			showMemoryMenu = false;
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[5].programId;
			availableChoices = [];
			showInventoryMenu = false;
			showItemMenu = true;
		}
	}
});

$(document).bind('keyup', '6', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATENODE';
			barParam = {
				direction: 'west'
			};
			showMenu = false;
		}
		else if (showNodeMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'MODIFYNODE';
			barParam = {
				type: 'hacking'
			};
			showNodeMenu = false;
		}
		else if (showProgramMenu === true) {
			progressBar = 10000;
			barOriginal = progressBar;
			barCommand = 'CREATEPROGRAM';
			barParam = {
				type: 'logicbomb'
			};
			showProgramMenu = false;
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[6].programId;
			availableChoices = [];
			showMemoryMenu = false;
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[6].programId;
			availableChoices = [];
			showInventoryMenu = false;
			showItemMenu = true;
		}
	}
});

$(document).bind('keyup', '7', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			showMenu = false;
			showNodeMenu = true;
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[7].programId;
			availableChoices = [];
			showMemoryMenu = false;
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[7].programId;
			availableChoices = [];
			showInventoryMenu = false;
			showItemMenu = true;
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
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[8].programId;
			availableChoices = [];
			showMemoryMenu = false;
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[8].programId;
			availableChoices = [];
			showInventoryMenu = false;
			showItemMenu = true;
		}
	}
});

$(document).bind('keyup', '9', function(){
	if (!$('.message').hasFocus) {
		if (showMenu === true) {
			showMenu = false;
			showProgramMenu = true;
		}
		else if (showMemoryMenu === true) {
			showMemProgId = availableChoices[9].programId;
			availableChoices = [];
			showMemoryMenu = false;
			showItemMenu = true;
		}
		else if (showInventoryMenu === true) {
			showMemProgId = availableChoices[9].programId;
			availableChoices = [];
			showInventoryMenu = false;
			showItemMenu = true;
		}
	}

});

$(document).bind('keyup', '0', function(){

	$('.log').toggle();

});

$(document).bind('keyup', 'return', function(){

	$('.message').show();
	$('.message').focus();

});