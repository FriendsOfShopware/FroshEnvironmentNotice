Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.model.Messages', {
    extend : 'Ext.data.Model', 
    fields : [ 'id', 'name', 'message' ],
    associations: [
        {
            type:'hasOne',
            model:'Shopware.apps.FroshEnvironmentNoticeEditor.model.Slots',
            associationKey:'slot',
            getterName: 'getSlot'
        }
    ],
    proxy: {
        type : 'ajax',
        api:{
            read:   '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxMessagesGet"}',
            create : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxMessagesInsert"}',
            update : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxMessagesUpdate"}',
            destroy : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxMessagesDelete"}'
        },
        reader : {
            type : 'json',
            root : 'data',
            totalProperty: 'totalCount'
        }
    }
});
