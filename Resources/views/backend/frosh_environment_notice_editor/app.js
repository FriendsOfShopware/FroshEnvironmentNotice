Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor', {
    extend:'Enlight.app.SubApplication',
    name:'Shopware.apps.FroshEnvironmentNoticeEditor',
    bulkLoad: true,
    loadPath: '{url action=load}',
    controllers: ['Main'],
    models: [ 'Slots', 'Messages' ],
    views: [ 'Window', 'grid.Slots', 'grid.Messages' ],
    stores: [ 'Slots', 'Messages' ],

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