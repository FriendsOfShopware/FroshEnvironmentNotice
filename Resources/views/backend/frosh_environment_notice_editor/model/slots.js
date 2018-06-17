Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.model.Slots', {
    extend : 'Ext.data.Model', 
    fields : [ 'id', 'name', 'style' ],
    proxy: {
        type : 'ajax',
        api:{
            read:   '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxSlotsGet"}',
            create : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxSlotsInsert"}',
            update : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxSlotsUpdate"}',
            destroy : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxSlotsDelete"}'
        },
        reader : {
            type : 'json',
            root : 'data',
            totalProperty: 'totalCount'
        }
    }
});
