Ext.define('TVSA.Viewport', {
    extend : 'Ext.panel.Panel',
    layout : 'border',
    height : 600,
    border : false,
    initComponent: function (){
        var me = this;
        var tree1 = Ext.create("TVSA.Tree",{
            region      : 'west',
            id          : 'tvsafeed',
            collapsible : true,
            split       : true,
            title       : 'FEED DE ENTRADA',
            width       : 350,
            listeners : {
                'itemclick' : function(_me, record, item, index, e ){

                    label_tree1.setText(record.get("id"));
                }
            }
        });

        var tree2 = Ext.create("TVSA.Tree",{
            region      : 'east',
            id          : 'tvsatemplate',
            title       : 'TEMPLATE FEED',
            collapsible : true,
            split       : true,
            width       : 350,
            listeners : {
                'itemclick' : function(_me, record, item, index, e ){

                    label_tree2.setText(record.get("id"));
                }
            }
        });

        var grid = Ext.create('TVSA.Grid',{
            title  : 'RELACIÓNES',
            id     : 'tvsagrid',
            autoHeight : true
        });

        var label_tree1 = Ext.create('Ext.form.Label',{
            text    : '?',
            style   : {
                "font-size" : '1.5em',
                "margin" : ".2em"
            }
        });

        var label_tree2 = Ext.create('Ext.form.Label',{
            text    : '?',
            style   : {
                "font-size" : '1.5em',
                "margin" : ".2em"
            }
        });

        var add = Ext.create('Ext.Button',{
            text : 'AGREGAR RELACIÓN',
            margin: '20 50 40 50',
            handler : function(){

                var node_tree1 = tree1.getSelectionModel().getSelection();
                var node_tree2 = tree2.getSelectionModel().getSelection();

                if(node_tree1.length == 0 || node_tree2.length == 0)
                {
                    Ext.Msg.alert('Advertencia', 'Seleciona un nodo del FEED.');
                }else
                {
                    var feed1 = node_tree1[0].data.id;
                    var feed2 = node_tree2[0].data.id;
                    var id = node_tree1[0].data.id+"="+node_tree2[0].data.id;

                    if(!grid.store.findRecord("id",id) && !grid.store.findRecord("feed1",feed1) && !grid.store.findRecord("feed2",feed2))
                    {
                        grid.store.insert(0,{
                            id    : id,
                            feed1 : node_tree1[0].data.id,
                            feed2 : node_tree2[0].data.id
                        });

                        if(Ext.isDefined(TVSA._inputHidden))
                            Ext.get(TVSA._inputHidden).dom.value = TVSA.getData();
                    }
                }
            }
        });


        var center = Ext.create('Ext.panel.Panel',{
            region : 'center',
            layout : {
                type    : 'vbox',       // Arrange child items vertically
                align   : 'stretch',  
            },
            title  : 'RELACIÓNES',
            items  : [
                        {
                            xtype   : 'panel',
                            layout  : {
                                type    : 'hbox',       // Arrange child items vertically
                                align   : 'stretch',    // Each takes up full width
                                padding : 5
                            },
                            items : [
                                    label_tree1,
                                    {
                                        xtype: 'label',
                                        text : '=',
                                        style   : {
                                            "font-size" : '1.5em',
                                            "margin" : ".2em"
                                        }
                                    },
                                    label_tree2
                            ]
                        },
                        add,
                        grid
                    ]
        });

        me.items =  [
        {
            region: 'north',
            autoHeight: true,
            border: false,
            margins: '0 0 5 0'
        },
            tree1,
            tree2,
            center
        ];
        me.callParent(arguments);
    }
});