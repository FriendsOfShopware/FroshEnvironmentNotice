Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.grid.Slots', {
    extend:'Ext.grid.Panel',
    border: false,
    alias:'widget.env-notice-editor-slots-grid',
    region:'center',
    autoScroll:true,
    title: '{s name="FroshEnvironmentNoticeEditorSlotsGridTitle"}Slots{/s}',
    initComponent:function () {
        var me = this;
        me.columns = me.getColumns();
        me.pagingbar = me.getPagingBar();
        me.dockedItems = [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    cls: 'shopware-toolbar',
                    ui: 'shopware-ui',
                    items: me.getButtons()
                },
                me.pagingbar
            ];
        me.callParent(arguments);
    },
    getColumns:function () {
        var me = this;

        return [
            {
                header: '{s name="FroshEnvironmentNoticeEditorSlotNameLabel"}Name{/s}',
                flex: 1,
                dataIndex: 'name'
            },
            {
                xtype: 'actioncolumn',
                width: 60,
                items: me.getActionColumnItems()
            }
        ];
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
                    me.fireEvent('openSlotDetail', view, rowIndex, colIndex, item);
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
                    me.fireEvent('deleteSlot', view, rowIndex, colIndex, item);
                }
            }
        ];
    },
    getPagingBar: function () {
        var me = this;

        return Ext.create('Ext.toolbar.Paging', {
            store: me.store,
            dock: 'bottom',
            displayInfo: true
        });
    },
    getButtons : function() {
        var me = this;

        return [
            {
                text: '{s name="FroshEnvironmentNoticeEditorAdd"}Add{/s}',
                scope: me,
                iconCls: 'sprite-plus-circle-frame',
                action: 'addSlot'
            }
        ];
    }
});
