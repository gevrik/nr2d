// Update game objects
var update = function (modifier) {

	//console.log(modifier);

	var serverMessage = {};

	if (progressBar > 0) {
		progressBar -= modifier * 1000;

		if (progressBar < 0) {
			progressBar = 0;

			serverMessage = {
				xcommand: barCommand,
				xvalue: barParam
			};

			send(JSON.stringify(serverMessage));
			barCommand = '';
			barParam = '';
			barOriginal = 0;
		}

	}

	if (baseAttackDelay > 0) {
		baseAttackDelay -= 1;

		if (baseAttackDelay < 0) {
			baseAttackDelay = 0;
		}

	}

	if (showLogTimer > 0) {
		showLogTimer -= 1;

		if (showLogTimer <= 0) {
			showLog = false;
			showLogTimer = 0;
		}

	}

	serverMessage = {
		xcommand: 'UPDATEME',
		xvalue: {x: hero.x, y: hero.y}
	};
	send(JSON.stringify(serverMessage));
		
	if (87 in keysDown) { // Player holding up
		if (!blockControls) {
			hero.y -= hero.speed * modifier;
		}
	}
	if (83 in keysDown) { // Player holding down
		if (!blockControls) {
			hero.y += hero.speed * modifier;
		}
	}
	if (65 in keysDown) { // Player holding left
		if (!blockControls) {
			hero.x -= hero.speed * modifier;
		}
	}
	if (68 in keysDown) { // Player holding right
		if (!blockControls) {
			hero.x += hero.speed * modifier;
		}
	}

	if (hero.y < 48 && northExit && !combatMode) {
		hero.y = canvas.height - 48;
		serverMessage = {
			xcommand: 'MOVETO',
			xvalue: northExit
		};
		//console.log(serverMessage);
		send( JSON.stringify(serverMessage) );
	}
	else if (hero.y < 48) {
		hero.y = 48;
	}

	if (hero.y > canvas.height - 48 && southExit && !combatMode) {
		hero.y = 48;
		serverMessage = {
			xcommand: 'MOVETO',
			xvalue: southExit
		};
		send( JSON.stringify(serverMessage) );
	}
	else if (hero.y > canvas.height - 48) {
		hero.y = canvas.height - 48;
	}

	if (hero.x < 48 && westExit && !combatMode) {
		hero.x = canvas.width - 48;
		serverMessage = {
			xcommand: 'MOVETO',
			xvalue: westExit
		};
		send( JSON.stringify(serverMessage) );
	}
	else if (hero.x < 48) {
		hero.x = 48;
	}

	if (hero.x > canvas.width - 48 && eastExit && !combatMode) {
		hero.x = 48;
		serverMessage = {
			xcommand: 'MOVETO',
			xvalue: eastExit
		};
		send( JSON.stringify(serverMessage) );
	}
	else if (hero.x > canvas.width - 48) {
		hero.x = canvas.width - 48;
	}

	jQuery.each(otherEntities, function(i, val) {

		if (otherEntities[i] && otherEntities[i].roomId == roomId) {
			var newTrajX = otherEntities[i].targetX - otherEntities[i].x;
			var newTrajY = otherEntities[i].targetY - otherEntities[i].y;
			var newX = otherEntities[i].x + (newTrajX * modifier);
			var newY = otherEntities[i].y + (newTrajY * modifier);
			otherEntities[i].x = newX;
			otherEntities[i].y = newY;

			var serverMessage = {
				xcommand: 'UPDATEENTITY',
				xvalue: {
					entityId: i,
					newX: newX,
					newY: newY
				}
			};
			send(JSON.stringify(serverMessage));

		}


	});

	jQuery.each(bullets, function(i, val) {
		if (bullets[i]) {

			bullets[i].currentX = bullets[i].currentX + (bullets[i].trajX * 2 *modifier);
			bullets[i].currentY = bullets[i].currentY + (bullets[i].trajY * 2 *modifier);

			if (bullets[i]) {

				jQuery.each(otherEntities, function(ie, vale) {

					if (otherEntities[ie]) {

						if (bullets[i]) {
							if (
								bullets[i].currentX <= (otherEntities[ie].x + 16) &&
								otherEntities[ie].x <= (bullets[i].currentX + 16) &&
								bullets[i].currentY <= (otherEntities[ie].y + 16) &&
								otherEntities[ie].y <= (bullets[i].currentY + 16) &&
								bullets[i].userId != otherEntities[ie].userId
							) {
								if (bullets[i]) {
									//console.log(otherEntities[ie]);

									bullets[i].currentX = -9999;
									bullets[i].currentY = -9999;
									
									var serverMessage = {
										xcommand: 'DELETEBULLET',
										xvalue: i
									};
									send(JSON.stringify(serverMessage));
									serverMessage = {
										xcommand: 'DAMAGEENTITY',
										xvalue: otherEntities[ie].id
									};
									send(JSON.stringify(serverMessage));
									//console.log(serverMessage);
								}
							}
						}
					}

				});

				if (bullets[i]) {
					if (
						bullets[i].currentX <= (hero.x + 16) &&
						hero.x <= (bullets[i].currentX + 16) &&
						bullets[i].currentY <= (hero.y + 16) &&
						hero.y <= (bullets[i].currentY + 16) &&
						bullets[i].userId != hero.userId &&
						bullets[i].roomId == hero.roomId
					) {
						if (bullets[i]) {
							//console.log(otherEntities[ie]);
							//delete bullets[i];
							bullets[i].currentX = -9999;
							bullets[i].currentY = -9999;
							serverMessage = {
								xcommand: 'DELETEBULLET',
								xvalue: i
							};
							send(JSON.stringify(serverMessage));
							serverMessage = {
								xcommand: 'DAMAGEUSER',
								xvalue: hero.socketId
							};
							send(JSON.stringify(serverMessage));
						}
					}
				}

				jQuery.each(otherUsers, function(ie, vale) {

					if (otherUsers[ie]) {

						if (bullets[i]) {
							if (
								bullets[i].currentX <= (otherUsers[ie].x + 16) &&
								otherUsers[ie].x <= (bullets[i].currentX + 16) &&
								bullets[i].currentY <= (otherUsers[ie].y + 16) &&
								otherUsers[ie].y <= (bullets[i].currentY + 16) &&
								bullets[i].userId != otherUsers[ie].userId &&
								bullets[i].roomId == otherUsers[ie].roomId
							) {
								if (bullets[i]) {
									//console.log(otherEntities[ie]);
									//delete bullets[i];

									bullets[i].currentX = -9999;
									bullets[i].currentY = -9999;

									var serverMessage = {
										xcommand: 'DELETEBULLET',
										xvalue: i
									};
									send(JSON.stringify(serverMessage));
									serverMessage = {
										xcommand: 'DAMAGEUSER',
										xvalue: otherUsers[ie].socketId
									};
									send(JSON.stringify(serverMessage));
								}
							}
						}
					}

				});

				if (bullets[i]) {
					if (
						bullets[i].currentX <= (bullets[i].targetX + 8) &&
						bullets[i].targetX <= (bullets[i].currentX + 8) &&
						bullets[i].currentY <= (bullets[i].targetY + 8) &&
						bullets[i].targetY <= (bullets[i].currentY + 8)
					) {
						if (bullets[i]) {
							//delete bullets[i];
							serverMessage = {
								xcommand: 'DELETEBULLET',
								xvalue: i
							};
							send(JSON.stringify(serverMessage));
						}
					}
				}
			}

		}
	});

};