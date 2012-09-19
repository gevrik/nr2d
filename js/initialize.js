var Server;
var username;
var holygrail;
var gameReady = false;

var storagePrograms = [];
var otherUsers = [];
var otherEntities = [];
var memoryPrograms = [];
var bullets = [];

var showMemProgId = 0;

var blockControls = false;
var usedSlots = 0;
var canExecute = false;

var currentPage = 1;
var maxPage = 1;
var pageArray = [];

var showUrl = '/index.php/world/xhrShow';

// Create the canvas
var canvas = document.createElement("canvas");
var ctx = canvas.getContext("2d");


var canvasEffects = document.createElement("canvas");
var ctxEffects = canvasEffects.getContext("2d");

// Game objects
var hero = {
	maxMemory: 0,
	maxStorage: 0,
	slots: 0
};

var roomName = '';
var roomType = '';
var roomOwner = '';
var roomLevel = '';
var roomId = 0;
var northExit = 0;
var eastExit = 0;
var southExit = 0;
var westExit = 0;

var showMenu = false;
var showNodeMenu = false;
var showCharMenu = false;
var showProgramMenu = false;
var showMemoryMenu = false;
var showStorageMenu = false;
var showInventoryMenu = false;
var showVirusMenu = false;
var showItemMenu = false;

var progressBar = 0;
var barOriginal = 0;
var barCommand = '';
var barParam = '';

var availableChoices = [];

//console.log(hero);

var monster = {};
var monstersCaught = 0;

// Handle keyboard controls
var keysDown = {};