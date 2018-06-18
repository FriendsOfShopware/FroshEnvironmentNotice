Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.grid.Messages', {
    extend:'Ext.grid.Panel',
    border: false,
    alias:'widget.env-notice-editor-messages-grid',
    region:'center',
    autoScroll:true,
    title: '{s name="FroshEnvironmentNoticeEditorMessagesGridTitle"}Messages{/s}',
    initComponent:function () {
        var me = this;
        me.columns = me.getColumns();
        me.dockedItems = [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    cls: 'shopware-toolbar',
                    ui: 'shopware-ui',
                    items: me.getButtons()
                }
            ];
        me.callParent(arguments);
    },
    getColumns:function () {
        var me = this;

        return [
            {
                header: '{s name="FroshEnvironmentNoticeEditorMessageNameLabel"}Name{/s}',
                flex: 1,
                dataIndex: 'name'
            },
            {
                header: '{s name="FroshEnvironmentNoticeEditorMessageMessageLabel"}Message{/s}',
                flex: 1,
                dataIndex: 'message'
            },
            {
                header: '{s name="FroshEnvironmentNoticeEditorMessageSlotLabel"}Slot{/s}',
                renderer: me.slotRenderer
            },
            {
                xtype: 'actioncolumn',
                width: 60,
                items: me.getActionColumnItems()
            }
        ];
    },
    slotRenderer: function(value, metaData, record) {
        var data = record.getSlot();

        if (data) {
            return data.get('name');
        } else {
            return 'undefined';
        }
    },
    getActionColumnItems: function () {
        var me = this;

        return [
            {
                iconCls:'x-action-col-icon sprite-pencil',
                tooltip:'{s name="FroshEnvironmentNoticeEditorEdit"}Edit{/s}',
                getClass: function(value, metadata, record) {
                    if (!record.get("id")) {
                        return 'x-hidden';
                    }
                },
                handler:function (view, rowIndex, colIndex, item) {
                    me.fireEvent('openMessageDetail', view, rowIndex, colIndex, item);
                }
            },
            {
                iconCls:'x-action-col-icon sprite-minus-circle-frame',
                tooltip:'{s name="FroshEnvironmentNoticeEditorDelete"}Delete{/s}',
                getClass: function(value, metadata, record) {
                    if (!record.get("id")) {
                        return 'x-hidden';
                    }
                },
                handler:function (view, rowIndex, colIndex, item) {
                    me.fireEvent('deleteMessage', view, rowIndex, colIndex, item);
                }
            }
        ];
    },
    getButtons : function() {
        var me = this;

        return [
            {
                text: '{s name="FroshEnvironmentNoticeEditorAdd"}Add{/s}',
                scope: me,
                iconCls: 'sprite-plus-circle-frame',
                action: 'addMessage'
            }
        ];
    }
});
