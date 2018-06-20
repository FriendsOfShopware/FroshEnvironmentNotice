Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.detail.SlotWindow', {
    extend: 'Enlight.app.Window',
    title: '{s name="FroshEnvironmentNoticeEditorSlotTitle"}Slot{/s}',
    alias: 'widget.env-notice-editor-detail-message-window',
    border: false,
    autoShow: true,
    layout: 'fit',
    height: 500,
    width: 800,
    modal: true,
    initComponent: function() {
        var me = this;
        
        me.items = [
            {
                xtype: 'env-notice-editor-detail-slot',
                record: me.record
            }
        ];

        me.callParent(arguments);
    }
});