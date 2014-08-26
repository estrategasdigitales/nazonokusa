Ext.define('TVSA.Tree', {
    extend      :'Ext.tree.Panel',
    title       : 'Tree 1',
    width       : 400,
    height      : 450,
    rootVisible : true,
    url         : null,
    viewConfig: {
        emptyText :"No hay resultados de b&uacute;squeda",
        stripeRows: true
    },

    initComponent: function (){
        
        var me = this;

        var data = {
                //fields     : this.fields,
                data     : me.data,
                //url      : me.url,
                root: {
                    expanded: false,
                    id      : 'tree',
                    text    : 'Feed'
                }
            };

        me.store = Ext.create('Ext.data.TreeStore',data);  



    var Tree = {


        data : [],
        node : function (store,json,isRoot,idNode,newNode)
        {
            var me = this;

            Ext.iterate(json, function(key, value) {
                // console.log(key)

                var id = idNode+"."+key;

                    //console.log(id)

                    //console.log(newNode)
                    // var node = store.getNodeById(idNode);
                    var expanded = isRoot;




                    var child = newNode.appendChild({
                        //expanded :expanded,
                        leaf: Ext.isObject(value) ? false : !Ext.isArray(value),
                        id   : id,
                        text : key
                    },true);



                    if(Ext.isObject(value))
                        me.node(store,value,false,id,child);

                    if(Ext.isArray(value)){

                        var json = value;


                        Ext.Array.each(json, function(key, value) {
                             
                            // console.log(key)

                            // console.log(key);
                            // console.log(idNode)
                            if(Ext.isObject(key))
                                me.node(store,key,false,id,child);

               
                        });
                    }

            }); 
            

        }

    };

    if(!Ext.isEmpty(me.url))
    {
        me.setLoading("Cargando Feed");
        
        Ext.Ajax.request ({
            url: me.url,
            success: function (file) {
                var json     = Ext.decode(file.responseText);

                    var node = me.store.getRootNode();
                    Tree.node(me.store,json,true,"tree",node);

                    me.fireEvent("renderTree",me.store);
                    node.expand();
                    me.setLoading(false);


            }
        }); 
    }


    me.callParent(arguments);

    },

    listeners : {
        'itemclick' : function(_me, record, item, index, e ){

            //panel.getComponent('tree1-panel').getComponent('tree1-label').setText(record.get("id"));
        }
    }


});