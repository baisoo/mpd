/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Massshipping
 * @version    1.0.3
 * @copyright  Copyright (c) 2012-2013 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

var MpMassShippingColumns = Class.create();
MpMassShippingColumns.prototype = {
    initialize: function (params) {
        for (key in params) {
            this[key] = params[key];
        }

        this.active_id = false;
        this.columns = [];
    },
    registerColumn: function(data){
        if (data.is_active){
            this.active_id = data.id;
        }
        this.columns.push(data);

        $(data.column_id).observe('click', (function(e){
            if ((e.target.nodeName != 'A') && (e.target.nodeName != 'SELECT') && (e.target.nodeName != 'SPAN') && (e.target.nodeName != 'BUTTON')){
                var column = this.getColumn(data.id);
                if (!column.is_active){
                    this.activateColumn(this.getColumn(data.id));
                }
            }
        }).bind(this).bind(data));

        return this;
    },
    deactivateAll: function(){
        for (var i = 0; i < this.columns.length; i++){
            var column = this.columns[i];
            column.is_active = false;
        }
        return this;
    },
    activateColumn: function(column){
        this.deactivateAll();
        if (column){
            column.is_active =  true;
            column.is_resolved = false;
            column.is_deleted = false;

            if (column.match_key){
                $(column).value = column.match_key;
            } else {
                $(column).value = null;
            }
            this.active_id = column.id;
        }
        this.renderColumns();
        return this;
    },
    getColumn: function(id){
        for (var i = 0; i < this.columns.length; i++){
            var column = this.columns[i];
            if (column.id == id){
                return column;
            }
        }
    },
    getFirstUnresolved: function(){
        for (var i = 0; i < this.columns.length; i++){
            var column = this.columns[i];
            if (!column.is_resolved){
                return column;
            }
        }
        return false;
    },
    getNextTo: function(id){
        var wantNext = false;
        for (var i = 0; i < this.columns.length; i++){
            var column = this.columns[i];
            if (wantNext){
                if (!column.is_resolved){
                    return column;
                }
            }
            if (column.id == id){
                wantNext = true;
            }
        }
        return false;
    },
    getPreviousTo: function(id){
        var wantPrev = false;
        for (var i = this.columns.length - 1; i >= 0; i--){
            var column = this.columns[i];
            if (wantPrev){
                return column;
            }
            if (column.id == id){
                wantPrev = true;
            }
        }
        return false;
    },
    renderColumns: function(){

        for (var i = 0; i < this.columns.length; i++){
            var column = this.columns[i];

            if (column.is_active){
                $(column.column_id).addClassName('active');
            } else {
                $(column.column_id).removeClassName('active');
            }

            if (column.is_resolved){
                $(column.status_id).addClassName('resolved');

            } else {
                $(column.status_id).removeClassName('resolved');
            }

            $$('.' + column.column_id).each((function(td){
                if (column.is_active){
                    $(td).addClassName('active');
                } else {
                    $(td).removeClassName('active');
                }
            }).bind(column));

            $(column.form_id).style.display = column.is_active ? 'block' : 'none';
            $(column.status_id).style.display = column.is_active ? 'none' : 'block';

            $(column.select_id).value = column.match_key;
            $(column.label_id).innerHTML = column.match_key ? this.match_data[column.match_key] : this.undefined_label;

            if (column.is_deleted){
                $(column.status_id).addClassName('deleted');
                $(column.select_id).disabled = 'disabled';
            } else {
                $(column.status_id).removeClassName('deleted');
                $(column.select_id).disabled = false;
            }

        }
        return this;
    },
    findColumnByMatch: function(match_key, notId){
        for (var i = this.columns.length - 1; i >= 0; i--){
            var column = this.columns[i];
            if ((column.match_key == match_key) && (column.id != notId)){
                return column;
            }
        }
        return false;
    },
    resolveColumn: function(id){

        var column = this.getColumn(id);
        column.is_resolved = true;
        column.is_deleted = false;
        column.match_key = $(column.select_id).value;

        var other = this.findColumnByMatch(column.match_key, column.id);
        if (other){
            other.match_key = false;
            other.is_resolved = false;
        }

        this.activateColumn(this.getNextTo(id));

        return this;
    },
    deleteColumn: function(id){
        var column = this.getColumn(id);
        column.match_key = false;
        column.is_resolved = true;
        column.is_deleted = true;
        this.activateColumn(this.getNextTo(id));
    },
    stepBackFrom: function(id){
        this.activateColumn(this.getPreviousTo(id));
    },
    deleteUnresolved: function(){
        for (var i = 0; i < this.columns.length; i++){
            var column = this.columns[i];
            if (!column.is_resolved){
                column.match_key = false;
                column.is_resolved = true;
                column.is_deleted = true;
                column.is_active = false;
            }
        }
        this.active_id = false;
        this.renderColumns();
    },
    hasUnresolved: function(){
        var result = false;
        for (var i = 0; i < this.columns.length; i++){
            var column = this.columns[i];
            if (!column.is_resolved){
                result = true;
            }
        }
        return result;
    },
    hasUnmatchedRequiredKeys: function(){
        var result = true;
        var results = [];
        for(var rM = 0; rM < this.required_matches.length; rM++){
            var requiredField = this.required_matches[rM];
            results[rM] = true;
            for (var i = 0; i < this.columns.length; i++){
                var column = this.columns[i];
                if (column.match_key == requiredField){
                    results[rM] = false;
                }
            }
        }

        var ideal = false;
        for (var j = 0; j <= results.length; j++){
            if (results[j]){
                ideal = true;
            }
        }

        result = ideal;
        return result;
    }
};


/** Mass Shipping Common Class */
var MpMassShipping = Class.create();
MpMassShipping.prototype = {
    initialize: function (params) {
        for (key in params) {
            this[key] = params[key];
        }


    },
    pressButton: function(button){
        if (button){
            // Disable button
            setTimeout((function(e){
                $(button).addClassName(this.pressed_class);
            }).bind(this).bind(button), this.button_timeout);
        }

        this.lockButtons();
        this.cleanMessageBlock();
    },
    wantPage: function(name, button){
        this.pressButton(button);
        this.activatePage(name);

        return false;
    },
    matchColumns: function(name, button){

        if (this.columns.hasUnresolved()){
            this.displayMessage(this.columns.unresolved_message);
        } else if (this.columns.hasUnmatchedRequiredKeys()) {
            this.displayMessage(this.columns.unmatched_message);

        } else {
            this.pressButton(button);
            this.activatePage(name, $(this.columns.form_id).serialize(), this.post_url);
        }
    },
    processData: function(params){
        this.process = [];
        for (key in params) {
            this.process[key] = params[key];
        }
        this.process.active = false;
    },
    processRenderStatus: function(data){
        $(this.process.progress_complete).innerHTML = data.complete;
        $(this.process.progress_total).innerHTML = data.total;
    },
    hasUnmatchedCerriers: function(){
        var result = false;
        $$('.ms-element.carrier .carrier-input').each(function(el){
            if (!el.value || (el.value == '')){
                $(el).addClassName('highlighted');
                result = true;
            } else {
                $(el).removeClassName('highlighted');
            }
        });
        return result;
    },
    processDone: function(name, button){
        if ($('add_number').checked){
            if (this.hasUnmatchedCerriers()){
                this.displayMessage(this.unmatched_carrier_error);
                return false;
            } else {
                this.cleanMessageBlock();
            }
        }

        this.showProcessLoader(true);
        this.process_after_name = name;
        this.processStartNextDataRequest(this.register_url, this.process.form);
    },
    processObserveRequestFinish: function(url, form, data){
        if (data && data.row_id){
            this.processStartNextDataRequest(url.replace('{{row_id}}', data.row_id), form);
        } else {
            this.showProcessLoader(false);
            this.activatePage(this.process_after_name);
        }
        this.processRenderStatus(data);
    },
    processStartNextDataRequest: function(url, form){

        var data = form ? form.serialize() : [];

        // Retrieve content
        new Ajax.Request( this.prepareUrl(url), {
            parameters: data,
            onFailure: (function () {
                this.processObserveRequestFinish(url, form, []);
            }).bind(this),
            onSuccess: (function(transport) {
                try {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        this.processRenderStatus(response);
                        this.processObserveRequestFinish(this.process_url, form, response);
                    } else {
                        this.showProcessLoader(false);
                    }
                }
                catch (e) { console.log(e); }
            }).bind(this).bind(url).bind(form),
            loaderArea: false
        });

    },
    showProcessLoader: function(show){
        if (show){
            $(this.process.progress_loader).addClassName('active');
        } else {
            $(this.process.progress_loader).removeClassName('active');
        }
    },
    prepareUrl: function(url){
        return url.replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, ''));
    },
    activatePage: function(name, data, url){

        if ('undefined' == typeof(data)) {
            var data = {};
        }

        if ('undefined' == typeof(url)){
            url =  this.interface_url;
        }

        // Retrieve content
        new Ajax.Request( this.prepareUrl(url.replace('{{name}}', name)), {
            parameters: data,
            onFailure: (function () {
                ///TODO

            }).bind(this).bind(name),
            onSuccess: (function(transport) {
                try {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();

                        if (response.html) {
                            $(this.container_id).innerHTML = response.html;
                            response.html.evalScripts();
                        }

                        if (response.message){
                            this.displayMessage(response.message);
                        } else {
                            this.cleanMessageBlock();
                        }
                    }
                    this.unlockButtons();
                }
                catch (e) {}
            }).bind(this).bind(name)
        });

        return false;
    },
    displayMessage: function(message){
        $('messages').innerHTML = message;
        Effect.ScrollTo('messages');
    },
    unlockButtons: function(){
        $$(this.button_selector).each((function(el){
            $(el).removeClassName(this.pressed_class);
            $(el).disabled = false;
        }).bind(this));
    },
    lockButtons: function(){
        $$(this.button_selector).each((function(el){
            $(el).disabled = true;
        }).bind(this));
    },
    cleanMessageBlock: function() {
        Effect.Fade('messages', { duration: 0.5, afterFinish: (function(){
            $('messages').innerHTML = "";
            $('messages').style.display = 'block';
        }).bind(this) });
    },
    registerForm: function(form){
        this.form = form;
    },
    registerFormElements: function(display_id){

        this.display_id = display_id;
        this.upload = new AjaxUpload($(display_id),{
            action: this.prepareUrl(this.upload_url),
            name: 'data_file',
            // Submit file as soon as it's selected
            autoSubmit: false,
            responseType: 'text',
            onChange: (function(file, extension){
                $(this.display_id).innerHTML = file;
                this.upload.is_selected = true;
            }).bind(this),
            onSubmit: (function(file, extension){
                this.showLoader(true);
                this.unlockButtons();
            }).bind(this),
            onComplete: (function(file, response){
                this.showLoader(false);
                response = Base64.decode(response);
                if (response) {
                    try {
                        response = eval("(" + response + ")");
                    } catch (e){
                        response = {};
                    }
                } else {
                    response = {};
                }
                if(response.success) {
                    this.activatePage(this.next_name);
                } else if(response.error) {
                    this.displayMessage(response.message);
                    this.unlockButtons();
                }

            }).bind(this)
        });

        this.upload.is_selected = false;

        $(display_id).onclick = (function(e){
            console.log(this.upload._button.click(e));
        }).bind(this);
    },
    postExcelForm: function (name, button){

        this.pressButton(button);
        this.activatePage(name, this.form.serialize(), this.post_excel_url.replace('{{name}}', name));

    },
    isIE: function(){
        return (navigator.userAgent.indexOf('MSIE') !== -1);
    },
    uploadFile: function(name, button){
        if (this.upload.is_selected){
            this.pressButton(button);
            this.next_name = name;
            this.upload.submit();
            this.upload.enable();
        } else {
            this.displayMessage(this.select_file_message);
        }
        return true;
    },
    showLoader: function(show){
        $(this.loader_id).style.display = show ? 'block' : 'none';
    },
    setColums: function(columns){
        this.columns = columns;
    }

};