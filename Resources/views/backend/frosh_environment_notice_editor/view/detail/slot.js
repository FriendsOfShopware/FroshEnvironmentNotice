//{namespace name=backend/plugins/frosh_environment_notice_editor}
//
Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.view.detail.Slot', {
    extend:'Ext.form.Panel',
    alias:'widget.env-notice-editor-detail-slot',
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
    
        me.editorField = Ext.create('Shopware.form.field.CodeMirror', {
            fieldLabel: '{s name="FroshEnvironmentNoticeEditorSlotStyleLabel"}Style{/s}',
            xtype: 'codemirrorfield',
            mode: 'css',
            labelAlign: 'top',
            anchor: '100%',
            name: 'style',
            flex: 1,
            allowBlank: true
        });
        
        me.items = me.getItems();
        
        me.callParent(arguments);
        me.loadRecord(me.record);
    },  
    getItems:function () {
        var me = this;
        return [
            {
                fieldLabel: '{s name="FroshEnvironmentNoticeEditorSlotNameLabel"}Name{/s}',
                labelWidth: 50,
                anchor: '100%',
                name: 'name',
                allowBlank: false
            },
            me.editorField
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