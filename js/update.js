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

	if (hero.y < 48 && northExit) {
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

	if (hero.y > canvas.height - 48 && southExit) {
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

	if (hero.x < 48 && westExit) {
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

	if (hero.x > canvas.width - 48 && eastExit) {
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

	jQuery.each(bullets, function(i, val) {
		if (bullets[i]) {

			bullets[i].currentX = bullets[i].currentX + (bullets[i].trajX * modifier);
			bullets[i].currentY = bullets[i].currentY + (bullets[i].trajY * modifier);

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
									delete bullets[i];
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
							delete bullets[i];
						}
					}
				}
			}

		}
	});

};