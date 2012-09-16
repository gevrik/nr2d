// Update game objects
var update = function (modifier) {

	//console.log(modifier);

	if (progressBar > 0) {
		progressBar -= modifier * 1000;

		if (progressBar < 0) {
			progressBar = 0;
			send(barCommand + ' ' + barParam);
			barCommand = '';
			barParam = '';
			barOriginal = 0;
		}

	}

	send('UPDATEME ' + hero.x + ' ' + hero.y);
		
	if (87 in keysDown) { // Player holding up
			hero.y -= hero.speed * modifier;
	}
	if (83 in keysDown) { // Player holding down
		hero.y += hero.speed * modifier;
	}
	if (65 in keysDown) { // Player holding left
		hero.x -= hero.speed * modifier;
	}
	if (68 in keysDown) { // Player holding right
		hero.x += hero.speed * modifier;
	}

	if (hero.y < 32 && northExit != 0) {
		hero.y = canvas.height - 64;
		send( 'MOVETO ' + northExit);
	}
	else if (hero.y < 32) {
		hero.y = 32;
	}

	if (hero.y > canvas.height - 64 && southExit != 0) {
		hero.y = 32;
		send( 'MOVETO ' + southExit);
	}
	else if (hero.y > canvas.height - 64) {
		hero.y = canvas.height - 64;
	}

	if (hero.x < 32 && westExit != 0) {
		hero.x = canvas.width - 64;
		send( 'MOVETO ' + westExit);
	}
	else if (hero.x < 32) {
		hero.x = 32;
	}

	if (hero.x > canvas.width - 64 && eastExit != 0) {
		hero.x = 32;
		send( 'MOVETO ' + eastExit);
	}
	else if (hero.x > canvas.width - 64) {
		hero.x = canvas.width - 64;
	}

	jQuery.each(bullets, function(i, val) {
		if (bullets[i]) {
			var currentX = bullets[i].currentX;
			var currentY = bullets[i].currentY;
			var targetX = bullets[i].targetX;
			var targetY = bullets[i].targetY;

			if (currentX > targetX) {
				if (bullets[i]) {
					bullets[i].currentX -= 512 * modifier;
				}
			}
			if (currentX < targetX) {
				if (bullets[i]) {
					bullets[i].currentX += 512 * modifier;
				}
			}

			if (currentY > targetY) {
				if (bullets[i]) {
					bullets[i].currentY -= 512 * modifier;
				}
			}
			if (currentY < targetY) {
				if (bullets[i]) {
				bullets[i].currentY += 512 * modifier;
				}
			}

			if (bullets[i]) {
				if (
					currentX <= (targetX + 16) &&
					targetX <= (currentX + 16) &&
					currentY <= (targetY + 16) &&
					targetY <= (currentY + 16)
				) {
					if (bullets[i]) {
						delete bullets[i];
					}
				}
			}

		}
	});

};