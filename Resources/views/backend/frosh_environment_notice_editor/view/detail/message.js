//{namespace name=backend/plugins/frosh_environment_notice_editor}
//
Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.detail.Message', {
    extend:'Ext.form.Panel',
    alias:'widget.env-notice-editor-detail-message',
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

        if (me.record.get('id')) {
            me.record.set('slotID', me.record.getSlot().get('id'));
        }

        me.loadRecord(me.record);
    },  
    getItems:function () {
        var me = this;

        return [
            {
                fieldLabel: '{s name="FroshEnvironmentNoticeEditorMessageNameLabel"}Name{/s}',
                labelWidth: 50,
                anchor: '100%',
                name: 'name',
                allowBlank: false
            },
            {
                fieldLabel: '{s name="FroshEnvironmentNoticeEditorMessageMessageLabel"}Message{/s}',
                xtype: 'textareafield',
                labelWidth: 50,
                anchor: '100%',
                name: 'message',
                allowBlank: false
            },
            {
                fieldLabel: '{s name="FroshEnvironmentNoticeEditorMessageSlotLabel"}Slot{/s}',
                xtype: 'combobox',
                labelWidth: 50,
                anchor: '100%',
                name: 'slotID',
                editable: false,
                store: me.slotStore,
                valueField: 'id',
                displayField: 'name',
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