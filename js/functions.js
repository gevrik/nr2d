function log( text ) {
	$log = $('#log');
	$log.append(($log.val()?"\n":'')+text);
	//Autoscroll
	$log[0].scrollTop = $log[0].scrollHeight - $log[0].clientHeight;
}

function send( text ) {
	Server.send( 'message', text );
}

function getStorageUsed()
{
	var storageUsed = 0;

	jQuery.each(storagePrograms, function(i, val) {
		if (storagePrograms[i]) {
			storageUsed += storagePrograms[i].rating;
		}
	});

	return storageUsed;
}

function getMemoryUsed()
{
	var memoryUsed = 0;

	jQuery.each(memoryPrograms, function(i, val) {
		if (memoryPrograms[i]) {
			memoryUsed += memoryPrograms[i].rating;
		}
	});

	return memoryUsed;
}

function getVirusDamage()
{
	var virusDamage = 1;

	jQuery.each(memoryPrograms, function(i, val) {
		if (memoryPrograms[i]) {
			if (memoryPrograms[i].type == 'antivirus') {
				virusDamage += memoryPrograms[i].rating;
			}
		}
	});

	return virusDamage;
}

function renderMenuBG()
{
	ctxEffects.clearRect ( 0 , 0 , canvas.width , canvas.height );
	ctxEffects.strokeStyle = 'rgba(80,80,80,0.9)';
	ctxEffects.fillStyle = 'rgba(0,0,0,0.9)';
	ctxEffects.lineWidth = 5;
	ctxEffects.beginPath();
	ctxEffects.rect(16,16,canvas.width - 32,canvas.height - 32);
	//ctxEffects.globalCompositeOperation = 'destination-atop';
	ctxEffects.fill();
	ctxEffects.stroke();

	ctxEffects.fillStyle = "rgb(250, 250, 250)";
	ctxEffects.font = "14px monospace";
	ctxEffects.textAlign = "left";
	ctxEffects.textBaseline = "top";
}

Math.rand = function(min, max) {
	return Math.floor(Math.random() * (max - min) + min);
};