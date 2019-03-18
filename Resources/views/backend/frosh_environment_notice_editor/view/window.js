//{namespace name=backend/plugins/frosh_environment_notice_editor}
//
Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.Window', {
    extend: 'Enlight.app.Window',
    title: '{s name="FroshEnvironmentNoticeEditorTitle"}Environment Notices{/s}',
    alias: 'widget.env-notice-editor',
    border: false,
    autoShow: true,
    height: 650,
    width: 925,
    layout: 'fit',
 
    initComponent: function() {
        var me = this;

        me.items = [
            Ext.create('Ext.tab.Panel', {
                flex: 1,
                items: [
                    {
                        title: '{s name="FroshEnvironmentNoticeEditorMessagesGridTitle"}Messages{/s}',
                        xtype: 'env-notice-editor-messages-grid',
                        store: me.messagesStore,
                        flex: 1
                    },
                    {
                        title: '{s name="FroshEnvironmentNoticeEditorTriggersGridTitle"}Triggers{/s}',
                        xtype: 'env-notice-editor-triggers-grid',
                        store: me.triggersStore,
                        flex: 1
                    },
                    {
                        title: '{s name="FroshEnvironmentNoticeEditorSlotsGridTitle"}Slots{/s}',
                        xtype: 'env-notice-editor-slots-grid',
                        store: me.slotsStore,
                        flex: 1
                    },
                ]
            })
        ];
    
        me.callParent(arguments);
    }

});
 
