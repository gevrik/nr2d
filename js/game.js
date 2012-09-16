
(function($p)
{

	$p.init = function(passedHolygrail, passedUsername) {

		holygrail = passedHolygrail;
		username = passedUsername;

		canvas.width = 512;
		canvas.height = 512;
		$('.mainContent').prepend(canvas);

		canvasEffects.width = canvas.width ;
		canvasEffects.height = canvas.height;

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
		Server = new FancyWebSocket('ws://totalmadownage.de:9300');

		$('#message').keypress(function(e) {
			if ( e.keyCode == 13 && this.value ) {
				send( 'CHAT ' + username + ': ' + this.value );

				$(this).val('');
			}
		});

		//Let the user know we're connected
		Server.bind('open', function() {
			log( "> connected" );
			// Let's play this game!
			reset();
			then = Date.now();
			setInterval(main, 40);
			send('INITP ' + holygrail);
			send('ROOMUPDATE');
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

    };

})(nrgame = {});