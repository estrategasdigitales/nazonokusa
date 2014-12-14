/**
 * @class Ext.ux.data.DynamicReader
 * @extends Ext.data.reader.Json
 * <p>Dynamic reader, allow to get working grid with auto generated columns and without setting a model in store</p>
 */

/**
 * floatOrString data type provide proper sorting in grid for string and float
 * if you don't now what data type of that two whould be in column
 */
Ext.apply(Ext.data.Types, {
    FLOATORSTRING: {
        convert: function(v, n) {
            v = Ext.isNumeric(v) ? Number(v) : v;
            return v;
        },
        sortType: function(v) {
            v = Ext.isNumeric(v) ? Number(v) : v;
            return v;
        },
        type: 'textfield'
    }
});

Ext.define('Base.data.reader.DynamicReader', {
    extend: 'Ext.data.reader.Json',
    alias: 'reader.dynamicReader',
    alternateClassName: 'Base.data.reader.DynamicReader',


    readRecords: function(json) {
        

            var data    = json.data;
            var item    = json.columns;
            var fields  = new Array();
            var columns = new Array();
            var p;

            Ext.Array.each(item,function(column,i){
               

                var editor = {
                    xtype       : 'textfield',
                    allowBlank  : false
                };

                if(Ext.isObject(column))
                {

                    fields.push(Ext.applyIf(column,{
                                                name: column.text, 
                                                type: "textfield"
                                            }));

                    columns.push(Ext.applyIf(column,{
                                                    text: column.text, 
                                                    dataIndex: column.text,
                                                    editor : editor
                                                })); 

                }else
                {


                    fields.push({name: column, type: 'textfield'});
                    columns.push({text: column, dataIndex: column,editor : editor});
                }


            });
            // for (p in item) {
            //     if (p && p != undefined) {

            //         console.log(p)
            //         // floatOrString type is only an option
            //         // You can make your own data type for more complex situations
            //         // or set it just to 'string'
            //         fields.push({name: p, type: 'floatOrString'});
            //         columns.push({text: p, dataIndex: p});
            //     }
            // }

            data.metaData = { fields: fields, columns: columns };


        // if (data.length > 0) {
        //     var item = data[0];
        //     var fields = new Array();
        //     var columns = new Array();
        //     var p;

        //     for (p in item) {
        //         if (p && p != undefined) {
        //             // floatOrString type is only an option
        //             // You can make your own data type for more complex situations
        //             // or set it just to 'string'
        //             fields.push({name: p, type: 'floatOrString'});
        //             columns.push({text: p, dataIndex: p});
        //         }
        //     }

        //     data.metaData = { fields: fields, columns: columns };
        // }


        return this.callParent([data]);
    }
});