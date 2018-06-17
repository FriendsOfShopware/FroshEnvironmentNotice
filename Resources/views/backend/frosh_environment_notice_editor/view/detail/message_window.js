Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.detail.MessageWindow', {
    extend: 'Enlight.app.Window',
    title: '{s name="FroshEnvironmentNoticeEditorMessageTitle"}Message{/s}',
    alias: 'widget.env-notice-editor-detail-message-window',
    border: false,
    autoShow: true,
    layout: 'fit',
    height: 600,
    width: 800,
    modal: true,
    initComponent: function() {
        var me = this;

        me.items = [
            {
                xtype: 'env-notice-editor-detail-message',
                record: me.record,
                slotStore: me.slotStore
            }
        ];

        me.callParent(arguments);
    }
});