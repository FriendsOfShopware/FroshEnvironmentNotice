//{namespace name=backend/plugins/frosh_environment_notice_editor}
//
Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.model.Slots', {
    extend : 'Ext.data.Model', 
    fields : [
        {
            name: 'id',
            type: 'int',
            useNull: true
        },
        {
            name: 'name',
            type: 'string'
        },
        {
            name: 'style',
            type: 'string'
        }
    ],
    idProperty: 'id',
    proxy: {
        type : 'ajax',
        api:{
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
