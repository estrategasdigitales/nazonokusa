Ext.application({
    name        : 'Televisa',
    appFolder   : "./js/tvsa/",
    
    launch: function() {
        var me = this;

        Ext.create('TVSA.Viewport');

    }
});