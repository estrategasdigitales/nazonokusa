Ext.application({
    name        : 'Televisa',
    appFolder   : "./js/tvsa/",
    
    launch: function() {
        var me = this;



        var feed = Ext.create("TVSA.Tree",{
            url         : './data.js',
            listeners	: {
            	"renderTree" : function(storeFeed){

            		var recordFeed = storeFeed.getRootNode().findChild("id","tree.total");
            		// console.log(recordFeed.getData())

			        template = Ext.create("TVSA.Tree",{
			            url         : './template.js',
			            listeners	: {
			            	"renderTree" : function(storeTemplate){
			            		var recordTemplate = storeTemplate.getRootNode().findChild("id","tree.records");
			            				recordFeed.set("id","tree.records");

			            				// console.log(recordFeed.getData())

			            				recordTemplate.set(recordFeed.getData())
			            				recordTemplate.commit();
			            				// console.log(recordTemplate)
			            			}
			        			}
			        });


            	}
            }
        });




        

        //tree.total=tree.records
    }
});