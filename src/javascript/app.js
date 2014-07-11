require.config({
	// Configuramos la url base partiendo de las
	// librerias del proyecto
	paths: {
		app: "../app",
		//jquery: "jquery/dist/jquery.min",
		jquery: "//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min",
		bootstrap: "bootstrap/dist/js/bootstrap",
		appglobal: "../app/televisa",
		angular: "angular/angular.min",
		"angular-bootstrap": 'angular-bootstrap/ui-bootstrap.min',
		datastore: 'datastore',
		'ace-elements': "ace-elements.min",
		"fuelux-tree": "fuelux.tree.min",
		"checklist-model": "checklist-model/checklist-model",
		"bootstrap-listTree": "bootstrap-listTree",
		"underscore": "underscore/underscore"
	},
	shim: {
		jquery: {
			exports: "jQuery"
		},
		bootstrap: ["jquery"],
		appglobal: ["angular", 'angular-bootstrap'],
		"ace-elements": ['jquery'],
		"fuelux-tree": ['jquery', 'ace-elements'],
		"checklist-model": ['angular'],
		"bootstrap-listTree": ['bootstrap', 'underscore']
	},
    appDir: 		"../javascript/",
    dir: "../../js/dist/",
    mainConfigFile: "../javascript/app.js",
    optimize:"none",
	modules: [{
		"name": "app"
	}]
});


requirejs(["appglobal"]);