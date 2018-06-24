//{namespace name=backend/plugins/frosh_environment_notice_editor}
//
Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.store.Slots', {
    extend: 'Ext.data.Store',
    remoteFilter: true,
    autoLoad : true,
    model : 'Shopware.apps.FroshEnvironmentNoticeEditor.model.Slots',
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxSlotsList"}',
        reader: {
            type: 'json',
            root: 'items'
        }
    }
});
