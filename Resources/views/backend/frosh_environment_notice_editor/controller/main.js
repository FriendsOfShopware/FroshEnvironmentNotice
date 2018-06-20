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

        me.control({
            'env-notice-editor-detail-message button[action=save]': {
                'click': function(btn) {
                    this.onMessageSave(btn);
                }
            },
            'env-notice-editor-detail-slot button[action=save]': {
                'click': function(btn) {
                    this.onSlotSave(btn);
                }
            },
            'env-notice-editor-messages-grid button[action=addMessage]': {
                'click': function (btn) {
                    this.addMessage(btn);
                }
            },
            'env-notice-editor-slots-grid button[action=addSlot]': {
                'click': function (btn) {
                    this.addSlot(btn);
                }
            },
            'env-notice-editor-messages-grid': {
                openMessageDetail: me.openMessageDetail,
                deleteMessage: me.deleteMessage
            },
            'env-notice-editor-slots-grid': {
                openSlotDetail: me.openSlotDetail,
                deleteSlot: me.deleteSlot
            }
        });

    },

    onMessageSave: function (btn) {
        var me = this,
            win = btn.up('window'),
            form = win.down('form'),
            formBasis = form.getForm(),
            store = me.getStore('Messages'),
            record = formBasis.getRecord();

        formBasis.updateRecord(record);

        if (formBasis.isValid()) {
            record.save({
                success: function() {
                    store.load();
                    win.close();
                    Shopware.Msg.createGrowlMessage('','{s name="FroshEnvironmentNoticeEditorMessageSave"}Message saved{/s}', '');
                },
                failure: function() {
                    store.load();
                    win.close();
                    Shopware.Msg.createGrowlMessage('',
                        '{s name="FroshEnvironmentNoticeEditorMessageError"}Error saving message{/s}',
                        '');
                }
            });
        }
    },

    onSlotSave: function (btn) {
        var me = this,
            win = btn.up('window'),
            form = win.down('form'),
            formBasis = form.getForm(),
            store = me.getStore('Slots'),
            record = formBasis.getRecord();

        formBasis.updateRecord(record);

        if (formBasis.isValid()) {
            record.save({
                success: function() {
                    store.load();
                    win.close();
                    Shopware.Msg.createGrowlMessage('','{s name="FroshEnvironmentNoticeEditorSlotSave"}Slot saved{/s}', '');
                },
                failure: function() {
                    store.load();
                    win.close();
                    Shopware.Msg.createGrowlMessage('',
                        '{s name="FroshEnvironmentNoticeEditorMessageError"}Error saving slot{/s}',
                        '');
                }
            });
        }
    },

    addMessage: function () {
        var me = this;

        me.record = Ext.create('Shopware.apps.FroshEnvironmentNoticeEditor.model.Messages');

        me.getView('detail.MessageWindow').create({
            record: me.record,
            slotStore: me.getStore('Slots')
        }).show();
    },

    addSlot: function () {
        var me = this;

        me.record = Ext.create('Shopware.apps.FroshEnvironmentNoticeEditor.model.Slots');

        me.getView('detail.SlotWindow').create({
            record: me.record
        }).show();
    },

    openMessageDetail: function (view, rowIndex) {
        var me = this;

        me.record = me.getStore('Messages').getAt(rowIndex);

        me.getView('detail.MessageWindow').create({
            record: me.record,
            slotStore : me.getStore('Slots')
        }).show();
    },

    deleteMessage: function (view, rowIndex) {
        var me = this,
            store = me.getStore('Messages');

        me.record = store.getAt(rowIndex);

        if (me.record instanceof Ext.data.Model && me.record.get('id') > 0) {
            Ext.MessageBox.confirm('', '{s name="FroshEnvironmentNoticeEditorDeleteWarning"}Are you sure you want to delete?{/s}' , function (response) {
                if ( response !== 'yes' ) {
                    return;
                }
                me.record.destroy({
                    callback: function() {
                        Shopware.Msg.createGrowlMessage('','{s name="FroshEnvironmentNoticeEditorDeleteSuccess"}Entry was deleted{/s}', '');
                        store.load();
                    }
                });
            });
        }
    },

    openSlotDetail: function (view, rowIndex) {
        var me = this;

        me.record = me.getStore('Slots').getAt(rowIndex);

        me.getView('detail.SlotWindow').create({
            record: me.record
        }).show();
    },

    deleteSlot: function (view, rowIndex) {
        var me = this,
            store = me.getStore('Slots');

        me.record = store.getAt(rowIndex);

        if (me.record instanceof Ext.data.Model && me.record.get('id') > 0) {
            Ext.MessageBox.confirm('', '{s name="FroshEnvironmentNoticeEditorDeleteWarning"}Are you sure you want to delete?{/s}' , function (response) {
                if ( response !== 'yes' ) {
                    return;
                }
                me.record.destroy({
                    callback: function() {
                        Shopware.Msg.createGrowlMessage('','{s name="FroshEnvironmentNoticeEditorDeleteSuccess"}Entry was deleted{/s}', '');
                        store.load();
                    }
                });
            });
        }
    }
   
});
 
