//{namespace name=backend/plugins/frosh_environment_notice_editor}
//
Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor', {
    extend:'Enlight.app.SubApplication',
    name:'Shopware.apps.FroshEnvironmentNoticeEditor',
    bulkLoad: true,
    loadPath: '{url action=load}',
    controllers: ['Main'],
    models: [ 'Slots', 'Messages', 'Triggers' ],
    views: [
        'Window',
        'grid.Slots',
        'grid.Messages',
        'grid.Triggers',
        'detail.Slot',
        'detail.Message',
        'detail.Trigger',
        'detail.SlotWindow',
        'detail.MessageWindow',
        'detail.TriggerWindow'
    ],
    stores: [ 'Slots', 'Messages', 'Triggers' ],

    /** Main Function
     * @private
     * @return [object] mainWindow - the main application window based on Enlight.app.Window
     */
    launch: function() {
        var me = this;
        var mainController = me.getController('Main');

        return mainController.mainWindow;
    }
});