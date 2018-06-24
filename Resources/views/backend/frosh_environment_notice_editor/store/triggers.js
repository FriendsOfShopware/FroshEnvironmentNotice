Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.store.Triggers', {
    extend: 'Ext.data.Store',
    remoteFilter: true,
    autoLoad : true,
    model : 'Shopware.apps.FroshEnvironmentNoticeEditor.model.Triggers',
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxTriggersList"}',
        reader: {
            type: 'json',
            root: 'items'
        }
    }
});
