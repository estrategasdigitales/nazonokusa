Ext.define('Base.grid.EditorGrid', {
	extend		: 'Base.grid.DynamicGrid',
	selType		: 'cellmodel',
	onEditar	: function(record,column){
		var me = this;
		me.CellEditing.cancelEdit();
		
		me.CellEditing.startEdit(record,column);
	},
	initComponent   : function (){
	
		var me = this;
		
		me.CellEditing = Ext.create('Ext.grid.plugin.CellEditing', {clicksToEdit:2});
		me.plugins =[me.CellEditing];
		
		Base.grid.EditorGrid.superclass.initComponent.apply(this,arguments);	
	},
	onAgregar : function(record){
		var me = this;
		
		me.getStore().insert(0, record);
		me.fireEvent("onAgregar",me,record,0);

	}
});
