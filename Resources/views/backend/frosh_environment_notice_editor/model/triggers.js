//{namespace name=backend/plugins/frosh_environment_notice_editor}
//
Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.model.Triggers', {
    extend : 'Ext.data.Model', 
    fields : [
        {
            name: 'id',
            type: 'int',
            useNull: true
        },
        {
            name: 'conditionType',
            type: 'string'
        },
        {
            name: 'conditionConfiguration',
            type: 'string'
        },
        {
            name: 'actionType',
            type: 'string'
        },
        {
            name: 'actionConfiguration',
            type: 'string'
        }
    ],
    idProperty: 'id',
    proxy: {
        type : 'ajax',
        api:{
            create : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxTriggersInsert"}',
            update : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxTriggersUpdate"}',
            destroy : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxTriggersDelete"}'
        },
        reader : {
            type : 'json',
            root : 'data',
            totalProperty: 'totalCount'
        }
    }
});
