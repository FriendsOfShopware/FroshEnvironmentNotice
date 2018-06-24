//{namespace name=backend/plugins/frosh_environment_notice_editor}
//
Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.detail.Trigger', {
    extend:'Ext.form.Panel',
    alias:'widget.env-notice-editor-detail-trigger',
    collapsible: false,
    bodyPadding: 10,
    split: false,
    region: 'center',
    defaultType: 'textfield',
    autoScroll: true,
    layout: {
        type: 'vbox',
        align : 'stretch',
        pack  : 'start'
    },
    items : [],
    initComponent: function() {
        var me = this;
        
        me.dockedItems = [
            {
                xtype: 'toolbar',
                dock: 'bottom',
                cls: 'shopware-toolbar',
                ui: 'shopware-ui',
                items: me.getButtons()
            }
        ];
        
        me.items = me.getItems();
        
        me.callParent(arguments);
        me.loadRecord(me.record);
    },  
    getItems:function () {
        var me = this;
        return [
            {
                fieldLabel: '{s name="FroshEnvironmentNoticeEditorTriggerConditionTypeLabel"}Type of condition{/s}',
                labelWidth: 150,
                anchor: '100%',
                name: 'conditionType',
                allowBlank: false
            },
            {
                fieldLabel: '{s name="FroshEnvironmentNoticeEditorTriggerConditionConfigurationLabel"}Condition configuration{/s}',
                labelWidth: 150,
                anchor: '100%',
                name: 'conditionConfiguration',
                allowBlank: false
            },
            {
                fieldLabel: '{s name="FroshEnvironmentNoticeEditorTriggerActionTypeLabel"}Type of action{/s}',
                labelWidth: 150,
                anchor: '100%',
                name: 'actionType',
                allowBlank: false
            },
            {
                fieldLabel: '{s name="FroshEnvironmentNoticeEditorTriggerActionConfigurationLabel"}Action configuration{/s}',
                labelWidth: 150,
                anchor: '100%',
                name: 'actionConfiguration',
                allowBlank: false
            }
        ];
    },
    getButtons : function()
    {
        var me = this;
        return [
            '->',
            {
                text: '{s name="FroshEnvironmentNoticeEditorCancel"}Cancel{/s}',
                scope: me,
                cls: 'secondary',
                handler: function() {
                    var me = this,
                        win = me.up('window');

                    win.destroy();
                }
            },
            {
                text: '{s name="FroshEnvironmentNoticeEditorSave"}Save{/s}',
                action: 'save',
                cls: 'primary',
                formBind: true
            }
        ];
    }
});