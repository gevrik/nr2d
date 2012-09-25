// Background image
var bgReady = false;
var bgImage = new Image();
bgImage.onload = function () {
	bgReady = true;
};
bgImage.src = "../../images/loading.png";

// Loading image
var loadingReady = false;
var loadingImage = new Image();
loadingImage.onload = function () {
	loadingReady = true;
};
loadingImage.src = "../../images/loading.png";

// Hero image
var heroReady = false;
var heroImage = new Image();
heroImage.onload = function () {
	heroReady = true;
};
heroImage.src = "../../images/hero.png";

// Other image
var otherReady = false;
var otherImage = new Image();
otherImage.onload = function () {
	otherReady = true;
};
otherImage.src = "../../images/other.png";

// Monster image
var monsterReady = false;
var monsterImage = new Image();
monsterImage.onload = function () {
	monsterReady = true;
};
//monsterImage.src = "../../images/bouncer.png";

// roomType image
var roomTypeReady = false;
var roomTypeImage = new Image();
roomTypeImage.onload = function () {
	roomTypeReady = true;
};
roomTypeImage.src = "../../images/ph.png";

// bullet image
var bulletReady = false;
var bulletImage = new Image();
bulletImage.onload = function () {
	bulletReady = true;
};
bulletImage.src = "../../images/bullet.png";

// bomb image
var bombReady = false;
var bombImage = new Image();
bombImage.onload = function () {
	bombReady = true;
};
bombImage.src = "../../images/logicbomb.png";

var upgradeItemSound = new Audio("../../sounds/collectammo.ogg");
var baseBlastSound = new Audio("../../sounds/baseblast.ogg");
var virusBlastSound = new Audio("../../sounds/virusblast.ogg");
var executingSound = new Audio("../../sounds/executing.ogg");
var bgmSound = new Audio("../../sounds/bgm.ogg");
var ioSound = new Audio("../../sounds/io.ogg");
