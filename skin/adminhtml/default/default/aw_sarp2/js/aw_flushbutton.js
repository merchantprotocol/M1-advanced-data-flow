var AWSarp2FlushButton = Class.create({
    initialize: function () {
        document.observe("dom:loaded", this.init.bind(this));
    },

    init: function () {
        this._button = $('aw_sarp2_flush');
        if (this._button) {
            this._button.observe('click', this.submitFlush.bind(this));
            this._loadingMask = $('loading-mask');
            this._msgContainer = $$('.aw_sarp2_message').first();
            this._msgContainer.hide();
        }
    },

    submitFlush: function () {
        if (confirm(AW_SARP2_CONFIG.msgConfirm)) {
            this._loadingMask.show();
            new Ajax.Request(AW_SARP2_CONFIG.flushActionUrl, {
                parameters: {},
                onSuccess: function (response) {
                    this._loadingMask.hide();
                    try {
                        var resp = response.responseText.evalJSON();
                        if (typeof(resp.result) != 'undefined') {
                            if (resp.result) {
                                this.showSuccess(AW_SARP2_CONFIG.msgSuccess);
                            } else {
                                this.showError((typeof(resp.message) != 'undefined' && resp.message) ? resp.message : AW_SARP2_CONFIG.msgFailure);
                            }
                        }
                    } catch (ex) {
                        console.log(ex.getMessage());
                    }
                }.bind(this),
                onFailure: function () {
                    this._loadingMask.hide();
                    this.showError(AW_CSMTP_CONFIG.msgFailure);
                }.bind(this)
            });
        }
    },

    showSuccess: function (msg) {
        this._msgContainer.removeClassName('error');
        this._msgContainer.addClassName('success');
        this._msgContainer.innerHTML = msg;
        this._msgContainer.show();
        setTimeout(function () {
            this._msgContainer.hide()
        }.bind(this), 5000);
    },

    showError: function (msg) {
        this._msgContainer.removeClassName('success');
        this._msgContainer.addClassName('error');
        this._msgContainer.innerHTML = msg;
        this._msgContainer.show();
        setTimeout(function () {
            this._msgContainer.hide()
        }.bind(this), 5000);
    }
});