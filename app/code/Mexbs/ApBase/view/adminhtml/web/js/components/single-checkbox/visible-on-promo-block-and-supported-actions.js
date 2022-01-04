define([
    'Magento_Ui/js/form/element/single-checkbox',
    'Mexbs_ApBase/js/model/ap-simple-actions'
], function (Element, ApSimpleActions) {
    'use strict';
    return Element.extend({
        isInSupportedActionArray: function(action){
            return (typeof ApSimpleActions.getConfig() != 'undefined')
                && (typeof ApSimpleActions.getConfig().promoBlockSupportingActions != 'undefined')
                && (ApSimpleActions.getConfig().promoBlockSupportingActions.indexOf(action) > -1);
        },
        setPromoBlockChecked: function (value){
            if (value == 0){
                this.promoBlockChecked = false;
            }else{
                this.promoBlockChecked = true;
            }
            this.visible(this.promoBlockChecked && this.isInSupportedActionArray(this.simpleAction));
        },
        setSimpleAction: function (value){
            this.simpleAction = value;
            this.visible(this.promoBlockChecked && this.isInSupportedActionArray(this.simpleAction));
        },

        initConfig: function (config) {
            this._super();
            return this;
        }
    });
});
