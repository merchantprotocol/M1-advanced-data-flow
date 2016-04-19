var awFieldDependence = Class.create({
    initialize: function (config) {
        this.mainFiled = $(config.mainFieldId);
        this.dependenceField = $(config.dependenceFieldId);
        this.astericsSpanField = this.dependenceField.up().up().select('span.required').first();
        this.config = {};
        this.config.message = config.message;
        this.config.available = config.available;
        this.init();
    },

    init: function () {
        this.messageBlock = new Element('p');
        this.messageBlock.update(this.config.message);
        this.messageBlock.setStyle({fontSize: '13px', height: '15px'});
        this.messageBlock.hide();
        this.dependenceField.up().appendChild(this.messageBlock);
        this.process();
        Event.observe(this.mainFiled, 'change', this.process.bind(this));
    },

    process: function () {
        if (this.config.available.indexOf(parseInt(this.mainFiled.value))) {
            this.dependenceField.hide();
            this.astericsSpanField.hide();
            this.hideAdvices();
            this.messageBlock.show();
            this.dependenceField.removeClassName('required-entry');
            this.dependenceField.removeClassName('validate-digit');
        } else {
            this.messageBlock.hide();
            this.astericsSpanField.show();
            this.dependenceField.show();
            this.dependenceField.addClassName('required-entry');
            this.dependenceField.addClassName('validate-digit');
        }
    },

    hideAdvices: function () {
        this.dependenceField.up().select('.validation-advice').each(function (element) {
            element.hide()
        });
    }
});