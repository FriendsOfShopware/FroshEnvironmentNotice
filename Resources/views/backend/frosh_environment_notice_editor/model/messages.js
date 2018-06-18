Ext.define('Shopware.apps.FroshEnvironmentNoticeEditor.model.Messages', {
    extend : 'Ext.data.Model', 
    fields : [
        {
            name: 'id',
            type: 'int',
            useNull: true
        },
        {
            name: 'name',
            type: 'string'
        },
        {
            name: 'message',
            type: 'string'
        },
        {
            name: 'slotID',
            type: 'int'
        }
    ],
    idProperty: 'id',
    associations: [
        {
            type: 'hasOne',
            model: 'Shopware.apps.FroshEnvironmentNoticeEditor.model.Slots',
            associationKey: 'slot',
            getterName: 'getSlot',
            setterName: 'setSlot',
            foreignKey: 'slot'
        }
    ],
    proxy: {
        type : 'ajax',
        api:{
            create : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxMessagesInsert"}',
            update : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxMessagesUpdate"}',
            destroy : '{url controller="FroshEnvironmentNoticeEditorApi" action="ajaxMessagesDelete"}'
        },
        reader : {
            type : 'json',
            root : 'data',
            totalProperty: 'totalCount'
        },
        doRequest: function(operation, callback, scope) {
            var writer  = this.getWriter(),
                request = this.buildRequest(operation, callback, scope);


            if (operation.allowWrite()) {
                request = writer.write(request);
            }

            if (request.jsonData && request.jsonData.slot && request.jsonData.slotID)  {
                request.jsonData.slot = {
                    'id': request.jsonData.slotID
                };
            }

            Ext.apply(request, {
                headers       : this.headers,
                params        : request.jsonData,
                timeout       : this.timeout,
                scope         : this,
                callback      : this.createRequestCallback(request, operation, callback, scope),
                method        : this.getMethod(request),
                disableCaching: false
            });

            Ext.Ajax.request(request);

            return request;
        }
    }
});
