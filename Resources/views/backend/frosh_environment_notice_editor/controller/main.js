Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.controller.Main', {

    extend: 'Ext.app.Controller',

    mainWindow: null,

    init: function() {

        var me = this;
        me.getStore('Messages').load({
            scope: this,
            callback: function() {
                me.getStore('Slots').load({
                    scope: this,
                    callback: function() {
                        me.mainWindow = me.getView('Window').create({
                            messagesStore: me.getStore('Messages'),
                            slotsStore: me.getStore('Slots')
                        });
                    }
                });
            }
        });

        me.callParent(arguments);

    }
   
});
 
