/**
 * Modulo de definciión de punto de entrada
 * @param require
 * @param module 
 * @param exports
 * @return
 */
define(function (require, module, exports) {
	require('datastore');
	require('fuelux-tree');
	require('checklist-model');
	// Cargamos el árbol
	/*var $tree = $('#campos-feed').ace_tree({
		dataSource: treeDataSource,
		multiSelect:true,
		loadingHTML:'<div class="tree-loading"><i class="icon-refresh icon-spin blue"></i></div>',
		'open-icon' : 'icon-minus',
		'close-icon' : 'icon-plus',
		'selectable' : true,
		'selected-icon' : 'icon-ok',
		'unselected-icon' : 'icon-remove'
	});

	$('#tree1').on('selected', function (evt, data) {
		console.log("Items" + JSON.stringify(data));
	});*/


	TelevisaFeed = (function () {
		var Feed = function () {

		};

		Feed.prototype.tree = function (dataSource) {
			var treeDataSource = new DataSourceTree({data: dataSource});

			// Cargamos el árbol
			var $tree = $('#tree-feed').ace_tree({
				dataSource: treeDataSource,
				multiSelect:true,
				loadingHTML:'<div class="tree-loading"><i class="icon-refresh icon-spin blue"></i></div>',
				'open-icon' : 'icon-minus',
				'close-icon' : 'icon-plus',
				'selectable' : true,
				'selected-icon' : 'icon-ok',
				'unselected-icon' : 'icon-remove'
			});

			$('#tree-feed').on('selected', function (evt, data) {
				console.log("Items" + JSON.stringify(data));
			});
		};

		return {
			Feed: new Feed
		};
	})();
});