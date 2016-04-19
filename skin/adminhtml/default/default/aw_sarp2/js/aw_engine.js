var awDependenceItem = Class.create({
    initialize: function (config) {
        this.websiteSelector = $('website');
        this.engineLabel = $('engine_label');
        this.engineValue = $('engine_code');
        this.configureLink = $('configure');
        this.submitButtton = $('submit');
        this.errorClassName = 'error';
        this.config = config;
        this.init();
    },

    init: function () {
        this.process();
        Event.observe(this.websiteSelector, 'change', this.process.bind(this));
        Event.observe(this.submitButtton, 'click', this.beforeSubmit.bind(this));
    },

    process: function () {
        var currentConfig = this.config[this.websiteSelector.value];
        if (currentConfig) {
            if (currentConfig.engine_code) {
                this.engineLabel.removeClassName(this.errorClassName);
            } else {
                this.engineLabel.addClassName(this.errorClassName);
            }
            this.engineLabel.down().update(currentConfig.engine_title);
            this.engineValue.value = currentConfig.engine_code;

            if (this.configureLink.href.indexOf('website') != -1) {
                this.configureLink.href = this.configureLink.href.substr(0, this.configureLink.href.indexOf('website'));
            }
            this.configureLink.href += 'website/' + currentConfig.website_code + '/';
        }
    },

    beforeSubmit: function (e) {
        if (this.engineLabel.hasClassName(this.errorClassName)) {
            alert(AW_SARP2_CONFIG.errorMessage);
            Event.stop(e);
        }
    }
});