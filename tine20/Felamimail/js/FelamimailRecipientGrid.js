/*
 * Tine 2.0
 * 
 * @package     Felamimail
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Philipp Schuele <p.schuele@metaways.de>
 * @copyright   Copyright (c) 2009 Metaways Infosystems GmbH (http://www.metaways.de)
 * @version     $Id:MessageEditDialog.js 7170 2009-03-05 10:58:55Z p.schuele@metaways.de $
 *
 */
 
Ext.namespace('Tine.Felamimail');

/**
 * grid panel for to/cc/bcc recipients
 * 
 * @namespace   Tine.Felamimail
 * @class       Tine.Felamimail.RecipientGrid
 * @extends     Ext.grid.EditorGridPanel
 * @param       {Object} config
 * @author      Philipp Schuele <p.schuele@metaways.de>
 * @version     $Id:MessageEditDialog.js 7170 2009-03-05 10:58:55Z p.schuele@metaways.de $
 * @constructor
 * 
 * TODO         add 'x' to remove recipient / grid row
 * TODO         add name to email address for display
 * TODO         disable horizontal scrollbar
 * TODO         use 'standard' template for adb search combo with image and both email addresses
 */
Tine.Felamimail.RecipientGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    
    /**
     * @private
     */
    id: 'felamimail-recipient-grid',
    
    /**
     * the message record
     * @type Tine.Felamimail.Model.Message
     */
    record: null,
    
    /**
     * @cfg {String} autoExpandColumn
     * auto expand column of grid
     */
    autoExpandColumn: 'address',
    
    /**
     * @cfg {Number} clicksToEdit
     * clicks to edit for editor grid panel
     */
    clicksToEdit:1,
    
    /**
     * @cfg {Boolean} header
     * show header
     */
    header: false,
    
    /**
     * @cfg {Boolean} border
     * show border
     */
    border: false,
    
    /**
     * @cfg {Boolean} deferredRender
     * deferred rendering
     */
    deferredRender: false,
    
    /**
     * @private
     */
    initComponent: function() {
        
        //this.view = new Ext.grid.GridView({});
        this.initStore();
        this.initColumnModel();
        
        //console.log(this.record);
        
        Tine.Felamimail.RecipientGrid.superclass.initComponent.call(this);
    },
    
    /**
     * init store
     * @private
     */
    initStore: function() {
        //this.store = new Ext.data.JsonStore({
        this.store = new Ext.data.SimpleStore({
            //id       : 'id',
            fields   : ['type', 'address']
        });
        
        // init recipients (on reply/reply to all)
        this._addRecipients(this.record.get('to'), 'to');
        this._addRecipients(this.record.get('cc'), 'cc');
        
        this.store.add(new Ext.data.Record({type: 'to', 'address': ''}));
        
        this.store.on('update', this.onUpdateStore, this);
    },
    
    /**
     * init cm
     * @private
     */
    initColumnModel: function() {
        this.cm = new Ext.grid.ColumnModel([
            {
                resizable: true,
                id: 'type',
                dataIndex: 'type',
                width: 80,
                menuDisabled: true,
                header: 'type',
                renderer: function(value) {
                    switch(value) {
                        case 'to':
                            return _('To:');
                            break;
                        case 'cc':
                            return _('Cc:');
                            break;
                        case 'bcc':
                            return _('Bcc:');
                            break;
                        default:
                            return '';
                    }
                },
                editor: new Ext.form.ComboBox({
                    typeAhead     : false,
                    triggerAction : 'all',
                    lazyRender    : true,
                    editable      : false,
                    mode          : 'local',
                    value         : null,
                    forceSelection: true,
                    store         : [
                        ['to',  _('To:')],
                        ['cc',  _('Cc:')],
                        ['bcc', _('Bcc:')]
                    ]
                })
            },{
                resizable: true,
                menuDisabled: true,
                id: 'address',
                dataIndex: 'address',
                width: 40,
                header: 'address',
                editor: new Tine.Felamimail.ContactSearchCombo({})
            }
        ]);
    },
    
    /**
     * start editing after render
     * @private
     */
    afterRender: function() {
        Tine.Felamimail.RecipientGrid.superclass.afterRender.call(this);
        
        if (this.store.getCount() == 1) {
            this.startEditing.defer(200, this, [0, 1]);
        }
    },
    
    /**
     * store has been updated
     * -> update record to/cc/bcc (if edit)
     * -> add additional row (if new address has been added)
     * 
     * @param {} store
     * @param {} record
     * @param {} operation
     * @private
     */
    onUpdateStore: function(store, record, operation)
    {
        if (operation == 'edit') {
            this.record.data.to = [];
            this.record.data.cc = [];
            this.record.data.bcc = [];
            
            store.each(function(recipient){
                if (recipient.data.address != '') {
                    this.record.data[recipient.data.type].push(recipient.data.address);
                }
            }, this);

            // add additional row if new address has been added
            if (record.modified.address == '') {
                store.add(new Ext.data.Record({type: 'to', 'address': ''}));
            }
            
            store.commitChanges();
        }
    },
    
    /**
     * add recipients to grid store
     * 
     * @param {Array} recipients
     * @param {String} type
     * @private
     * 
     * TODO get own email address and don't add it to store
     */
    _addRecipients: function(recipients, type) {
        if (recipients) {
            for (var i=0; i<recipients.length; i++) {
                this.store.add(new Ext.data.Record({type: type, 'address': recipients[i]}));
            }
        }
    }
});

/**
 * contact email search combo
 * 
 * @namespace   Tine.Felamimail
 * @class       Tine.Felamimail.ContactSearchCombo
 * @extends     Tine.Addressbook.SearchCombo
 * @param       {Object} config
 * @author      Philipp Schuele <p.schuele@metaways.de>
 * @version     $Id:MessageEditDialog.js 7170 2009-03-05 10:58:55Z p.schuele@metaways.de $
 * @constructor
 * 
 */
Tine.Felamimail.ContactSearchCombo = Ext.extend(Tine.Addressbook.SearchCombo, {

    /**
     * @cfg {Boolean} forceSelection
     */
    forceSelection: false,
    
    //private
    initComponent: function() {
        this.tpl = new Ext.XTemplate(
            '<tpl for="."><div class="search-item">',
                '{[this.encode(values.n_fileas)]}',
                ' (<b>{[this.encode(values.email, values.email_home)]}</b>)',
                /*
                '<table cellspacing="0" cellpadding="2" border="0" style="font-size: 11px;" width="100%">',
                    '<tr>',
                        //'<td width="50%"><b>{[this.encode(values.n_fileas)]}</b></td>',
                        //'<td width="50%"><b>{[this.encode(values.email)]}</b></td>',
                        '<td width="40%"><b>{[this.encode(values.n_fileas)]}</b><br/>{[this.encode(values.org_name)]}</td>',
                        '<td width="40%">{[this.encode(values.email)]}<br/>',
                            '{[this.encode(values.email_home)]}</td>',
                        '<td width="20%">',
                            '<img width="45px" height="39px" src="{jpegphoto}" />',
                        '</td>',
                    '</tr>',
                '</table>',
                */
            '</div></tpl>',
            {
                encode: function(email, email_home) {
                    if (email) {
                        return Ext.util.Format.htmlEncode(email);
                    } else if (email_home) {
                        return Ext.util.Format.htmlEncode(email_home);
                    } else {
                        return '';
                    }
                }
            }
        );
        
        Tine.Felamimail.ContactSearchCombo.superclass.initComponent.call(this);
    },
    
    /**
     * override default onSelect
     * - set email/name as value
     * 
     * @param {} record
     * @private
     * 
     * TODO add name
     * TODO make it possible to choose between office/home email addresses
     */
    onSelect: function(record) {
        if (record.get('email') != '') {
            this.setValue(record.get('email'));
        } else {
            this.setValue(record.get('email_home'));
        } 
        this.collapse();
        this.fireEvent('blur', this);
    }    
});

