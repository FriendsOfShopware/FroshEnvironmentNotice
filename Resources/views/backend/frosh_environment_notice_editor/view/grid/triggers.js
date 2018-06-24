//{namespace name=backend/plugins/frosh_environment_notice_editor}
//
Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.grid.Triggers', {
    extend:'Ext.grid.Panel',
    border: false,
    alias:'widget.env-notice-editor-triggers-grid',
    region:'center',
    autoScroll:true,
    title: '{s name="FroshEnvironmentNoticeEditorTriggersGridTitle"}Triggers{/s}',
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
                header: '{s name="FroshEnvironmentNoticeEditorTriggerConditionTypeLabel"}Type of condition{/s}',
                flex: 1,
                dataIndex: 'conditionType'
            },
            {
                header: '{s name="FroshEnvironmentNoticeEditorTriggerConditionConfigurationLabel"}Condition configuration{/s}',
                flex: 1,
                dataIndex: 'conditionConfiguration'
            },
            {
                header: '{s name="FroshEnvironmentNoticeEditorTriggerActionTypeLabel"}Type of action{/s}',
                flex: 1,
                dataIndex: 'actionType'
            },
            {
                header: '{s name="FroshEnvironmentNoticeEditorTriggerActionConfigurationLabel"}Action configuration{/s}',
                flex: 1,
                dataIndex: 'actionConfiguration'
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
                    me.fireEvent('openTriggerDetail', view, rowIndex, colIndex, item);
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
                    me.fireEvent('deleteTrigger', view, rowIndex, colIndex, item);
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
                action: 'addTrigger'
            }
        ];
    }
});
