Ext.application({
    name        : 'Televisa',
    appFolder   : "./js/tvsa/",
    
    launch: function() {
        var me = this;


         function getJsonOfStore(store){
                var datar = new Array();
                var jsonDataEncode = "";
                var records = store.getRange();

                for (var i = 0; i < records.length; i++)
                    datar.push(records[i].data);
                
                return Ext.encode(datar);
            }

        TVSA = {
            _view    : null,
            _inputHidden : null,
            resetTemplate : function(){
                var me       = this,
                    tree     = Ext.getCmp('tvsatemplate');

                    if(tree)
                    {
                        var store    = tree.store,
                        childrens = store.getRootNode().childNodes;

                        if(childrens.length > 0)
                            store.getRootNode().collapse();

                        store.getRootNode().removeAll();
                    }

            },
            resetAll     : function(){
                var me = this;
                me.resetTemplate();
                me.resetFeed();
                me.resetGrid();
            },

            resetFeed    : function(){
                var me       = this,
                    tree     = Ext.getCmp('tvsafeed');

                    if(tree)
                    {
                        var store    = tree.store,
                        childrens = store.getRootNode().childNodes;

                        if(childrens.length > 0)
                            store.getRootNode().collapse();

                        store.getRootNode().removeAll();
                    }

            },
            resetGrid    : function(){
                var me      = this,
                    grid    = Ext.getCmp('tvsagrid');

                    if(grid)
                        grid.store.removeAll();

            },
            save     : function(){
                var me          = this,
                    data        = [];

                    if(me.save.arguments.length > 0)
                    {
                        var data = me.save.arguments[0];
                        me._inputHidden = data;
                        Ext.get(data).dom.value = me.getData();
                    }

            },

            getData  : function(){
                var me      = this,
                    grid    = Ext.getCmp('tvsagrid'),
                    store   = grid.store;

                    return base64_encode(getJsonOfStore(store));

            },
            create   : function(){
                var me          = this,
                    data        = [];

                    if(me.create.arguments.length > 0)
                        var data = me.create.arguments[0];

                    me._view = Ext.create('TVSA.Viewport',Ext.applyIf(data,{
                    }));
            },
            show    : function(){
                var me   = this,
                    id   = Ext.getBody();

                    if(me.show.arguments.length > 0)
                        var id = me.show.arguments[0];

                if(me._view)
                   me._view.destroy();
                
                    me.create({renderTo:id});
                    me._view.show();


            },

            hide: function () {
                var me   = this;

                if(me._view)
                    me._view.hide();

            },
            setFeed : function(url){
                var me       = this,
                    tvsafeed = Ext.getCmp('tvsafeed');

                me.resetFeed();    
                
                setTimeout(function(){
                    tvsafeed.loadData(url);
                }, 500);
                
            },
            setTemplate : function(url){
                var me       = this,
                    tvsafeed = Ext.getCmp('tvsatemplate');

                me.resetTemplate();

                setTimeout(function(){
                    tvsafeed.loadData(url);
                }, 500);
            }
        };

        TVSA.standar = {
            id            : "tvsatreestandar",
            hiddenInput   : "campos_seleccionados",
            checkeds      : null,
            save     : function(id){
                var me          = this;

                Ext.get(id).dom.value = me.getData();

            },

            getData  : function(){
                var me      = this,
                    tree    = Ext.getCmp(me.id);


                var records = [];
                Ext.each(tree.getChecked(),function(record,i){
                    var data = record.data;
                    if(data.leaf)
                        records[i]= { "id": data.id, "feed1":data.id, "feed2":data.id };
                });

                records = Ext.encode( records );
                records = base64_encode(records);

                return records;

            },
            show    : function(id){
                var me   = this;
                var tree = Ext.getCmp(me.id);

                if(tree)
                    tree.destroy();

                me.create(id);

            },
            create : function(id){

                var me = this;

                return Ext.create("TVSA.Tree",{
                    id          : me.id,
                    renderTo    : id,
                    title       : 'TEMPLATE FEED',
                    collapsible : true,
                    split       : true,
                    width       : 350,
                    //url         : './data.js',
                    node        : {checked :false}
                });
            },
            hide: function () {
                var me   = this;
                var tree = Ext.getCmp(me.id);

                if(tree)
                    tree.hide();

            },
            set : function(url){
                var me       = this,
                    tree     = Ext.getCmp(me.id);

                me.reset();

                setTimeout(function(){
                    tree.loadData(url);
                }, 500);
            },
            reset   : function(){
                var me       = this,
                    tree     = Ext.getCmp(me.id);

                    if(tree)
                    {
                        var store    = tree.store,
                        childrens = store.getRootNode().childNodes;

                        if(childrens.length > 0)
                            store.getRootNode().collapse();

                        store.getRootNode().removeAll();
                    }

            },
            setChecked : function(){
                var me       = this;

                if(typeof checkeds != 'undefined')
                {
                    var tree     = Ext.getCmp(me.id);
                    var root     = tree.store.getRootNode();

                    tree.on("renderTree",function(){
                        me.save("campos_seleccionados");
                    });

                    checkeds = Ext.decode(checkeds);

                    root.cascadeBy(function(node) {
                        var data = node.data;
                      if(checkeds.indexOf(data.id) != -1)
                        node.set('checked', true);
                    });
                }

            }
        }



    }
});