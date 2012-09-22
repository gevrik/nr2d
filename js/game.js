
(function($p)
{

	$p.init = function(passedHash) {

		hash = passedHash;
		//console.log(hash);

		$(window).keydown(function(event) {
			// check for escape key
			if (event.which == 27) {
				// the following seems to fix the symptom but only in case the document has the focus
				event.preventDefault();
				$('.message').blur();
				$('.message').toggle();
			}
		});

		canvas.width = 800;
		canvas.height = 640;
		$('.mainContent').prepend(canvas);

		canvasEffects.width = canvas.width ;
		canvasEffects.height = canvas.height;

		canvasLog.width = canvas.width ;
		canvasLog.height = canvas.height;

		// mouse coord fetcher
		function getMousePos(canvas, evt) {
			var rect = canvas.getBoundingClientRect();

			// return relative mouse position
			var mouseX = evt.clientX - rect.left;
			var mouseY = evt.clientY - rect.top;

			//console.log(mouseX);
			//console.log(mouseY);

			ctx.beginPath();
			ctx.arc(mouseX, mouseY, 4, 0, 2 * Math.PI, false);
			ctx.fillStyle = "rgb(255, 0, 0)";
			ctx.fill();

			return {
				x: mouseX,
				y: mouseY
			};
		}

		addEventListener("keydown", function (e) {
				keysDown[e.keyCode] = true;
		}, false);

		addEventListener("keyup", function (e) {
				delete keysDown[e.keyCode];
		}, false);

		// The main game loop
		var main = function () {
			var now = Date.now();
			var delta = now - then;

			update(delta / 1000);
			render();

			then = now;
		};

		// websocket
		log('> connecting...');
		Server = new FancyWebSocket('ws://127.0.0.1:9300');
		//Server = new FancyWebSocket('ws://totalmadownage.de:9300');

		$('#message').keypress(function(e) {
			if ( e.keyCode == 13 && this.value ) {


				var serverMessage = {
					xcommand: 'CHAT',
					xvalue: username + ': ' + this.value
				};

				send( JSON.stringify(serverMessage) );

				$(this).val('');
				$(this).blur();

			}
		});

		//Let the user know we're connected
		Server.bind('open', function() {
			log( "> connected" );
			reset();
			then = Date.now();

			var serverMessage = {
				xcommand: 'INITP',
				xvalue: hash
			};
			send(JSON.stringify(serverMessage));

			setInterval(main, 40);

			serverMessage = {
				xcommand: 'ROOMUPDATE',
				xvalue: 0
			};

			send(JSON.stringify(serverMessage));
		});

		//OH NOES! Disconnection occurred.
		Server.bind('close', function( data ) {
			log( "Disconnected." );
			//alert('You have been disconnected!');
		});

		//Log any messages sent from server
		Server.bind('message', function( payload ) {
			parseReply( payload );
		});

		Server.connect();
		var then = Date.now();

		window.onbeforeunload = function() {
			return "You will be disconnected if you refresh the page. This will unload all of your programs from active memory in the game. You for serious?";
		};

		// block inputs if chat input has focus
		$('.message').focus(function() {
			blockControls = true;
		});

		$('.message').blur(function() {
			blockControls = false;
		});

		canvas.addEventListener('click', function(evt) {

			var mousePos = getMousePos(canvas, evt);
			//console.log(mousePos);

			var currentX = hero.x;
			var currentY = hero.y;
			var targetX = mousePos.x;
			var targetY = mousePos.y;

			if (combatMode === true && baseAttackDelay < 1) {

				var angle = Math.atan2(targetY - hero.y, targetX - hero.x) * 180 / Math.PI;

				if (angle < 0) {
					angle = 360 + angle;
				}

				if (angle > 315 || angle < 45) {
					//console.log('shoot right');
					targetX = hero.x + hero.speed;
					targetY = hero.y;
				}
				else if( angle > 45 && angle < 135)
				{
					//console.log('shoot down');
					targetX = hero.x;
					targetY = hero.y + hero.speed;
				}
				else if( angle > 135 && angle < 225)
				{
					//console.log('shoot left');
					targetX = hero.x - hero.speed;
					targetY = hero.y;
				}
				else if( angle > 225 && angle < 315)
				{
					//console.log('shoot up');
					targetX = hero.x;
					targetY = hero.y - hero.speed;
				}

				var trajX = targetX - currentX;
				var trajY = targetY - currentY;

				var serverMessage = {
					xcommand: 'ADDBULLET',
					xvalue: {
						currentX: currentX,
						currentY: currentY,
						targetX: targetX,
						targetY: targetY,
						trajX: trajX,
						trajY: trajY,
						userId: holygrail,
						roomId: hero.roomId * 1
					}
				};
				send( JSON.stringify(serverMessage) );
				baseAttackDelay = 10 - hero.attackspeed;
				//console.log(serverMessage);

				//baseAttackDelay = 20;
				//console.log(bullets);
			}
			else if (combatMode === false) {
				if (targetX > canvas.width / 2 - 32 &&
					targetX < canvas.width / 2 + 32 &&
					targetY > canvas.height / 2 - 32 &&
					targetY < canvas.height / 2 + 32) {

					showLog = true;
					showLogTimer = 125;
					logText.unshift({xvalue: '> this is a ' + roomName + ' node'});
					if (logText.length > 10) {
						logText.pop();
					}
					log('> this is a ' + roomName + ' node');

				}
			}

		}, false);

    };

})(nrgame = {});