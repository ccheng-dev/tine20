/**
 * Tine 2.0
 * 
 * @package     Felamimail
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Cornelius Weiss <c.weiss@metaways.de>
 * @copyright   Copyright (c) 2007-2008 Metaways Infosystems GmbH (http://www.metaways.de)
 * @version     $Id$
 *
 */
 
Ext.ns('Tine.Felamimail');

// default mainscreen
Tine.Felamimail.MainScreen = Ext.extend(Tine.Tinebase.widgets.app.MainScreen, {
    
    activeContentType: 'Message',
    
    /*
    show: function() {
        if(this.fireEvent("beforeshow", this) !== false){
            this.setTreePanel();
            this.setContentPanel();
            this.setToolbar();
            this.updateMainToolbar();
            
            this.fireEvent('show', this);
        }
        return this;
    },*/
    
    setContentPanel: function() {
        
        // which content panel?
        var type = this.activeContentType;
        
        if (! this[type + 'GridPanel']) {
            this[type + 'GridPanel'] = new Tine[this.app.appName][type + 'GridPanel']({
                app: this.app,
                plugins: [this.treePanel.getFilterPlugin()]
            });
            
        }
        
        Tine.Tinebase.MainScreen.setActiveContentPanel(this[type + 'GridPanel'], true);
        this[type + 'GridPanel'].store.load();
    },
    
    getContentPanel: function() {
        // which content panel?
        //var type = this.activeContentType;
        
        // we always return record grid panel as a quick hack for saving filters
        return this['Message' + 'GridPanel'];
    },
    
    /**
     * sets toolbar in mainscreen
     */
    setToolbar: function() {
        var type = this.activeContentType;
        
        if (! this[type + 'ActionToolbar']) {
            this[type + 'ActionToolbar'] = this[type + 'GridPanel'].actionToolbar;
        }
        
        Tine.Tinebase.MainScreen.setActiveToolbar(this[type + 'ActionToolbar'], true);
    }
});
