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
            resetAll     : function(){
                var me = this;
                me.resetTemplate();
                me.resetFeed();
                me.resetGrid();
            },

            resetTemplate : function(){
                var me       = this,
                    tree     = Ext.getCmp('tvsatemplate'),
                    store    = tree.store,
                    childrens = store.getRootNode().childNodes;

                    if(childrens.length > 0)
                        store.getRootNode().collapse();

                    store.getRootNode().removeAll();
            },

            resetFeed    : function(){
                var me       = this,
                    tree     = Ext.getCmp('tvsafeed'),
                    store    = tree.store,
                    childrens = store.getRootNode().childNodes;

                    if(childrens.length > 0)
                        store.getRootNode().collapse();

                    store.getRootNode().removeAll();
            },

            resetGrid    : function(){
                var me      = this,
                    grid    = Ext.getCmp('tvsagrid'),
                    store   = grid.store;

                    store.removeAll();
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

                    return getJsonOfStore(store);

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

                if(!me._view)
                    me.create({renderTo:id});
                else
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
    }
});