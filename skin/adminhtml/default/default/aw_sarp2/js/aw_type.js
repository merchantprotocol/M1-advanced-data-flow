Array.prototype.diff = function (a) {
    return this.filter(function (i) {
        return !(a.indexOf(i) > -1);
    });
};

var awTypeItemManager = Class.create({
    initialize: function (baseFieldsetId, fieldsetIds, config, tabId) {
        this.baseFieldsetId = baseFieldsetId;
        this.fieldsetIds = fieldsetIds;
        this.config = config;
        this.tabContentId = tabId + '_content';
        this._baseInstance = null;
        this._instances = [];
        this._options = [];
        this._notAvailableOptions = [];
        this.init();
    },

    init: function () {
        this._baseInstance = new awTypeItem(this.baseFieldsetId, this.config, true);
        this.moveBaseFieldsetToBodyEnd();
        this.initOptions();
        if (this.fieldsetIds.length) {
            for (var index = 0; index < this.fieldsetIds.length; ++index) {
                var fieldsetId = this.fieldsetIds[index];
                var newTypeItem = new awTypeItem(fieldsetId, this.config);
                this.addChangeHadler(newTypeItem);
                this.addButtonHandlers(newTypeItem);
                this._instances.push(newTypeItem);
            }
        } else {
            this.addFieldsetHandler();
        }
        this.updateSubsctiptionTypeSelectors();
        this.checkButtonsStatus();
    },

    deleteBaseFieldset: function () {
        this._baseInstance.getFieldset().remove();
    },

    moveBaseFieldsetToBodyEnd: function () {
        document.body.appendChild(this._baseInstance.getFieldset());
    },

    initOptions: function () {
        var options = $(this.baseFieldsetId).select('.subscription_type_id').first().options;
        for (var i = 0; i < options.length; i++) {
            this._options.push(options[i].getAttribute('value'));
        }
    },

    getBaseInstance: function () {
        return this._baseInstance;
    },

    getBaseFieldset: function () {
        return this.getBaseInstance().getFieldset();
    },

    getInstance: function (index) {
        return this._instances[index];
    },

    getFieldset: function (index) {
        return this.getInstance(index).getFieldset();
    },

    registerFieldsetInstance: function (newFieldsetId, index) {
        var newTypeItem = new awTypeItem(newFieldsetId, this.config);
        this._instances.push(newTypeItem);
        return newTypeItem;
    },

    unregisterFieldsetInstance: function (instance) {
        for (var i = 0; i < this._instances.length; i++) {
            if (this._instances[i] == instance) {
                this._instances.splice(i, 1);
                break;
            }
        }
    },

    addFieldsetHandler: function (afterElement) {
        if (afterElement) {
            var newFieldset = this.insertNewFieldsetAfter(afterElement.getFieldset());
        } else {
            var newFieldset = this.insertNewFieldset();
        }
        var uniqueId = 'new_' + new Date().getTime();
        newFieldset.id += uniqueId;
        var fieldsetInstance = this.registerFieldsetInstance(newFieldset.id, uniqueId);
        fieldsetInstance.generateFieldNames(uniqueId);
        fieldsetInstance.setOption(this._options.diff(this._notAvailableOptions).first());
        this._notAvailableOptions.push(this._options.diff(this._notAvailableOptions).first());
        this.updateSubsctiptionTypeSelectors();
        fieldsetInstance.process();
        this.addChangeHadler(fieldsetInstance);
        this.addButtonHandlers(fieldsetInstance);
        fieldsetInstance.runAddProgress();
        this.checkButtonsStatus();
    },

    insertNewFieldsetAfter: function (afterElement) {
        var newFieldset = this.getBaseFieldset().cloneNode(true);
        afterElement.insert({
            after: newFieldset
        });
        return newFieldset;
    },

    insertNewFieldset: function () {
        var newFieldset = this.getBaseFieldset().cloneNode(true);
        $(this.tabContentId).down().insert(newFieldset);
        return newFieldset;
    },

    removeFieldsetHandler: function (typeItem) {
        this.unregisterFieldsetInstance(typeItem);
        typeItem.runRemoveProgress();
        this.updateSubsctiptionTypeSelectors();
        this.checkButtonsStatus();
    },

    addButtonHandlers: function (typeItem) {
        var itemManager = this;
        // addHandler
        Event.observe(typeItem.getAddButton(), 'click', function (e) {
            Event.stop(e);
            itemManager.addFieldsetHandler(typeItem);
        });

        // removeHandler
        Event.observe(typeItem.getRemoveButton(), 'click', function (e) {
            Event.stop(e);
            itemManager.removeFieldsetHandler(typeItem);
        });
    },

    checkButtonsStatus: function () {
        if (this._instances.length == 1) {
            this._instances[0].hideRemoveButton();
        } else {
            this._instances[0].showRemoveButton();
        }
        if (this._options.diff(this._notAvailableOptions).length == 0) {
            this._instances.each(function (item) {
                item.hideAddButton()
            });
        } else {
            this._instances.each(function (item) {
                item.showAddButton()
            });
        }
    },

    addChangeHadler: function (typeItem) {
        var itemManager = this;
        Event.observe(typeItem.getChildElement('subscription_type_id'), 'change', function (e) {
            itemManager.updateSubsctiptionTypeSelectors();
        });
    },

    updateSubsctiptionTypeSelectors: function () {
        this._notAvailableOptions = [];
        var me = this;
        this._instances.each(function (item) {
            me._notAvailableOptions.push(item.getChildElement('subscription_type_id').getValue())
        });
        this._instances.each(function (item) {
            var options = item.getChildElement('subscription_type_id').options;
            for (var i = 0; i < options.length; i++) {
                if (me._notAvailableOptions.indexOf(options[i].getAttribute('value')) > -1) {
                    if (item.getChildElement('subscription_type_id').getValue() != options[i].getAttribute('value')) {
                        options[i].disabled = true;
                    }
                } else {
                    options[i].disabled = false;
                }
            }
        });
    }
});

var awTypeItem = Class.create({
    initialize: function (id, config, isBaseFieldset) {
        this.typeSelectorId = 'subscription_type_id';
        this.fieldsetId = id;
        this.isBaseFieldset = isBaseFieldset;
        this.fieldset = $(this.fieldsetId);
        this.config = config;
        this.init();
    },

    init: function () {
        this.process();
        if (!this.isBaseFieldset) {
            this.render();
        }
        Event.observe(this.getChildElement(this.typeSelectorId), 'change', this.process.bind(this));
    },

    process: function () {
        var currentConfig = this.getCurrentConfig();
        for (var i in currentConfig) {
            var element = null;
            if (element = this.getChildElement(i)) {
                this.toggleElementDisplay(element, currentConfig[i]);
            }
        }
    },

    render: function () {
        this._buttonArea = this.createButtonArea();
        this.createRemoveButton();
        this.createAddButton();
    },

    createButtonArea: function () {
        var tr = new Element('tr');
        var tdLabel = new Element('td', {'class': 'label'});
        var tdValue = new Element('td', {'class': 'value', 'style': 'text-align: right;'});

        tr.insert(tdLabel);
        tr.insert(tdValue);
        this.getFieldset().down().down().down().insert(tr);

        return tr;
    },

    createAddButton: function () {
        this._addButton = new Element('button', {
            'class': 'add',
            'id': this.fieldsetId + '_add',
            'title': 'Add New Type',
            'style': 'margin-right: 20px;'
        }).update('<span>Add</span>');

        this._buttonArea.down().next().insert(this._addButton);
        return this._addButton;
    },

    getAddButton: function () {
        return this._addButton;
    },

    hideAddButton: function () {
        this._addButton.disabled = true;
        this._addButton.addClassName('disabled');
    },

    showAddButton: function () {
        this._addButton.disabled = false;
        this._addButton.removeClassName('disabled');
    },

    createRemoveButton: function () {
        this._removeButton = new Element('button', {
            'class': 'delete',
            'id': this.fieldsetId + '_delete',
            'title': 'Delete Type',
            'style': 'margin-right: 5px;'
        }).update('<span>Remove</span>');

        this._buttonArea.down().next().insert(this._removeButton);
        return this._removeButton;
    },

    getRemoveButton: function () {
        return this._removeButton;
    },

    hideRemoveButton: function () {
        this._removeButton.disabled = true;
        this._removeButton.addClassName('disabled');
    },

    showRemoveButton: function () {
        this._removeButton.disabled = false;
        this._removeButton.removeClassName('disabled');
    },

    getFieldset: function () {
        return this.fieldset;
    },

    getTypeId: function () {
        return this.getChildElement(this.typeSelectorId).value;
    },

    getChildElement: function (selector) {
        selector = '.' + selector;
        var element = this.fieldset.select(selector).first();
        if (element) {
            return element;
        }
        return false;
    },

    getCurrentConfig: function () {
        return this.config[this.getTypeId()];
    },

    getConfigByType: function (typeId) {
        return this.config[typeId];
    },

    toggleElementDisplay: function (element, status) {
        if (status) {
            this.showElementRow(element);
            this.enableElement(element);
        } else {
            this.hideElementRow(element);
            this.disableElement(element);
        }
    },

    hideElementRow: function (element) {
        // up to parent TR element
        element.up().up().hide();
        element.removeClassName('required-entry');
    },

    showElementRow: function (element) {
        // up to parent TR element
        element.up().up().show();
        element.addClassName('required-entry');
    },

    disableElement: function (element) {
        element.disable();
    },

    enableElement: function (element) {
        element.enable();
    },

    generateFieldNames: function (uniqueId) {
        var currentConfig = this.getCurrentConfig();
        for (var i in currentConfig) {
            var element = this.getChildElement(i);
            if (element) {
                var search = 'hidden';
                element.id = element.id.gsub(search, uniqueId);
                element.name = element.name.gsub(search, uniqueId);
                // change for label
                element.up().previous().down().setAttribute('for', element.id);
            }
        }
    },

    runAddProgress: function () {
        this.currentHeight = 0;
        this.realHeight = this.getFieldset().getHeight();
        this.getFieldset().setStyle({overflow: 'hidden', height: '0px'});
        this.getFieldset().removeClassName('fieldset-hidden');
        this._riseHandler = function (instance) {
            return function () {
                instance.currentHeight += 25;
                if (instance.currentHeight < instance.realHeight) {
                    instance.getFieldset().setStyle({height: instance.currentHeight + 'px'});
                    setTimeout(instance._riseHandler, 1);
                } else {
                    instance.getFieldset().removeAttribute('style');
                    instance.currentHeight = instance.realHeight;
                }
            }
        }(this);

        setTimeout(this._riseHandler, 1);
    },

    runRemoveProgress: function () {
        this.realHeight = this.getFieldset().getHeight();
        this.currentHeight = this.realHeight;

        this.getFieldset().setStyle({overflow: 'hidden', height: this.currentHeight + 'px'});
        this._hideHandler = function (instance) {
            return function () {
                instance.currentHeight -= 25;
                if (instance.currentHeight > 0) {
                    instance.getFieldset().setStyle({height: instance.currentHeight + 'px'});
                    setTimeout(instance._hideHandler, 1);
                } else {
                    instance.getFieldset().removeAttribute('style');
                    instance.currentHeight = instance.realHeight;
                    instance.getFieldset().remove();
                }
            }
        }(this);

        setTimeout(this._hideHandler, 1);
    },

    setOption: function (index) {
        var options = this.getChildElement('subscription_type_id').options;
        for (var i = 0; i < options.length; i++) {
            if (options[i].getAttribute('value') == index) {
                this.getChildElement('subscription_type_id').selectedIndex = i;
                break;
            }
        }
    }
});