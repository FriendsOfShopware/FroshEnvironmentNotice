Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.detail.TriggerWindow', {
    extend: 'Enlight.app.Window',
    title: '{s name="FroshEnvironmentNoticeEditorTriggerTitle"}Trigger{/s}',
    alias: 'widget.env-notice-editor-detail-trigger-window',
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
                xtype: 'env-notice-editor-detail-trigger',
                record: me.record
            }
        ];

        me.callParent(arguments);
    }
});