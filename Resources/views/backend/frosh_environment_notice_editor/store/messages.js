Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.store.Messages', {
    extend: 'Ext.data.Store',
    remoteFilter: true,
    autoLoad : false,
    model : 'Shopware.apps.FroshEnvironmentNoticeEditor.model.Messages',
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxMessagesList"}',
        reader: {
            type: 'json',
            root: 'items'
        }
    }
});
