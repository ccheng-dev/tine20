/*
 * Tine 2.0
 * 
 * @package     Tinebase
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Philipp Schüle <p.schuele@metaways.de>
 * @copyright   Copyright (c) 2010 Metaways Infosystems GmbH (http://www.metaways.de)
 */

Ext.ns('Tine.Filemanager');

/**
 * @namespace Tine.Filemanager
 * @class Tine.Filemanager.TreePanel
 * @extends Tine.widgets.container.TreePanel
 * 
 * @author Martin Jatho <m.jatho@metaways.de>
 */

//Tine.Filemanager.TreePanel = function() {
//   
//    Tine.Filemanager.TreePanel.superclass.constructor.call(this);
//};


//Ext.extend(Tine.Filemanager.TreePanel, Tine.widgets.container.TreePanel, {

Tine.Filemanager.TreePanel = Ext.extend(Tine.widgets.container.TreePanel, {
//  filterMode : 'filterToolbar',
    recordClass : Tine.Filemanager.Model.Node,
    allowMultiSelection : false, 
    plugins : [ {
        ptype : 'ux.browseplugin',
        enableFileDialog: false,
        multiple : true,
        handler : function() {
            alert("tree drop");
        }
    } ],
    
    /**Tine.widgets.tree.FilterPlugin
     * returns a filter plugin to be used in a grid
     */
    getFilterPlugin: function() {
        if (!this.filterPlugin) {
            this.filterPlugin = new Tine.Filemanager.PathFilterPlugin({
                treePanel: this
            });
        }
        
        return this.filterPlugin;
    },
    
    initComponent: function() {
              
        var containerName = this.recordClass ? this.recordClass.getMeta('containerName') : 'container';
        var containersName = this.recordClass ? this.recordClass.getMeta('containersName') : 'containers';
        this.containerName = this.containerName || this.app.i18n.n_hidden(containerName, containersName, 1);
        this.containersName = this.containersName || this.app.i18n._hidden(containersName);
        
        
        this.loader = this.loader || new Tine.widgets.tree.Loader({
            getParams: this.onBeforeLoad.createDelegate(this),
            inspectCreateNode: this.onBeforeCreateNode.createDelegate(this)
        });
        
        this.root = {
                path: '/',
                cls: 'tinebase-tree-hide-collapsetool',
                expanded: true,
                children: [{
                    path: 'personal/' + Tine.Tinebase.registry.get('currentAccount').accountLoginName,
                    id: 'personal'
                }, {
                    path: '/shared',
                    id: 'shared'
                }, {
                    path: '/personal',
                    id: 'otherUsers'
                }].concat(this.getExtraItems())
        };
                 
        this.on('click', this.onClick);
        this.on('contextmenu', this.onContextMenu, this);
               
        this.initContextMenu();
        Tine.widgets.container.TreePanel.superclass.initComponent.call(this);

    },
    
    /**
     * returns params for async request
     * 
     * @param {Ext.tree.TreeNode} node
     * @return {Object}
     */
    onBeforeLoad: function(node) {
        
        var path = node.attributes.path;
        var type = Tine.Tinebase.container.path2type(path);
        var owner = Tine.Tinebase.container.pathIsPersonalNode(path);
        var loginName = Tine.Tinebase.registry.get('currentAccount').accountLoginName;
        
        if (type === 'personal' && ! owner) {
            type = 'otherUsers';
        }
        
        var newPath = path;
        
        if (type === 'personal' && owner) {
            var pathParts = path.toString().split('/');
            newPath = '/' + pathParts[1] + '/' + loginName;
            if(pathParts[3]) {
                newPath += '/' + pathParts[3];
            } 
        }
        
        console.log(newPath);
        
        var params = {
            method: 'Filemanager.searchNodes',
            application: this.app.appName,
            owner: owner,
            filter: [
                     {field: 'path', operator:'equals', value: newPath},
                     {field: 'type', operator:'equals', value: 'folder'}
                     ]
        };
        
        return params;
    },    
    
    /**
     * adopt attr
     * 
     * @param {Object} attr
     */
    onBeforeCreateNode: function(attr) {
        if (attr.accountDisplayName) {
            attr.name = attr.accountDisplayName;
            attr.path = '/personal/' + attr.accountId;
            attr.id = attr.accountId;
        }
        
        if (! attr.name && attr.path) {
            attr.name = Tine.Tinebase.container.path2name(attr.path, this.containerName, this.containersName);
        }
        
        if(attr.name && attr.name.name) {
            Ext.applyIf(attr, {
                text: Ext.util.Format.htmlEncode(attr.name.name),
                qtip: Ext.util.Format.htmlEncode(attr.name.name),
                leaf: !(attr.type == 'folder'),
                allowDrop: (attr.type == 'folder')
            });
        }
        else {

            Ext.applyIf(attr, {
                text: Ext.util.Format.htmlEncode(attr.name),
                qtip: Ext.util.Format.htmlEncode(attr.name),
                leaf: !!attr.account_grants,
                allowDrop: !!attr.account_grants && attr.account_grants.addGrant
            });
        }
        
        
        // copy 'real' data to container space
        attr.container = Ext.copyTo({}, attr, Tine.Tinebase.Model.Container.getFieldNames());
    },
    
    
    onClick: function(node, e) {
        
        console.log("onclick");
        if(node && node.reload) {
            node.reload();
        }

        var actionToolbar = this.app.mainScreen.ActionToolbar;
        var items = actionToolbar.get(0).items.items;
        
        if(node.attributes.account_grants) {
            if(node.attributes.account_grants.addGrant) {
                items[0].enable();
            }
            else items[0].disable();
            
            if(node.attributes.account_grants.deleteGrant) {
                items[1].enable();
            }
            else items[1].disable();
            
            if(node.attributes.account_grants.addGrant) {
                items[2].enable();
            }
            else items[2].disable();
            
            if(node.attributes.account_grants.exportGrant || node.attributes.account_grants.readGrant) {
                items[4].enable();
            }
            else items[4].disable();
        }
        else {
            items[0].disable();
            items[1].disable();
            items[4].disable();
            items[2].enable();
            items[3].enable();
            
            if(node.isRoot) {
                items[2].disable();
                items[3].disable();
            }
        }
        
        Tine.Filemanager.TreePanel.superclass.onClick.call(this, node, e);

    },
    
    /**
     * @private
     */
    initContextMenu: function() {
        
        this.contextMenuUserFolder = Tine.widgets.tree.ContextMenu.getMenu({
            nodeName: this.containerName,
            actions: ['add', 'delete', 'rename'],
            scope: this,
            backend: 'Filemanager',
            backendModel: 'Node'
        });
    
//        this.contextMenuSingleContainer = Tine.widgets.tree.ContextMenu.getMenu({
//            nodeName: this.containerName,
//            actions: ['delete', 'rename', 'grants'].concat(this.useContainerColor ? ['changecolor'] : []),
//            scope: this,
//            backend: 'Tinebase_Container',
//            backendModel: 'Container'
//        });
        
        
        this.contextMenuContainerFolder = Tine.widgets.tree.ContextMenu.getMenu({
            nodeName: this.containerName,
            actions: ['add', 'grants'],
            scope: this,
            backend: 'Filemanager',
            backendModel: 'Node'
        });
    },
    
    /**
     * show context menu
     * 
     * @param {} node
     * @param {} event
     */
    onContextMenu: function(node, event) {
        
        var currentAccount = Tine.Tinebase.registry.get('currentAccount');
        
        this.ctxNode = node;
        var container = node.attributes.container,
            path = container.path,
            owner;
        
        if (! Ext.isString(path)) {
            return;
        }
        
//        if (Tine.Tinebase.container.pathIsContainer(path)) {
//            if (container.account_grants && container.account_grants.adminGrant) {
//                this.contextMenuSingleContainer.showAt(event.getXY());
//            }
//        } else 
//            
        
        if (!Tine.Tinebase.container.pathIsContainer(path)) {
            this.contextMenuContainerFolder.showAt(event.getXY());
        }
        else if (path.match(/^\/shared$/) && (Tine.Tinebase.common.hasRight('admin', this.app.appName) 
                || Tine.Tinebase.common.hasRight('manage_shared_folders', this.app.appName))){
            this.contextMenuUserFolder.showAt(event.getXY());
        } 
        // TODO: check auf richtigen user !!!
        else if (path.match(/^\/personal/) && path.match(currentAccount.accountLoginName)) {
            this.contextMenuUserFolder.showAt(event.getXY());
        }
    }
    
    
});
