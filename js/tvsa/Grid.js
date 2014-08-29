Ext.define('TVSA.Grid', {
    extend: 'Ext.grid.Panel',
    columns: [
        { header: 'Nodo Entrada',  dataIndex: 'feed1', flex: 1 },
        { header: 'Nodo Template', dataIndex: 'feed2', flex: 1 },
        {
           xtype:'actioncolumn',
            width:50,
            items: [
            {
                icon: './img/delete.png',
                tooltip: 'Delete',
                handler: function(grid, rowIndex, colIndex) {

                    Ext.MessageBox.confirm('Eliminar', 'Se eliminara &#191;Esta seguro?', 
                        function(btn) {
                           if(btn=='yes') {
                            grid.getStore().removeAt(rowIndex);

                            if(Ext.isDefined(TVSA._inputHidden))
                                Ext.get(TVSA._inputHidden).dom.value = TVSA.getData();
                        }
                    });   
                }
            }] 
        }
    ],
    
    initComponent: function() {
        var me = this;
        var data = {
                fields:['id','feed1', 'feed2'],
                data: me.data,
                proxy: {
                    type: 'memory',
                    reader: {
                        type: 'json',
                        root: 'items'
                    }
                }
            };

        me.store = Ext.create('Ext.data.Store',data);  
        me.callParent(arguments);
    }
});