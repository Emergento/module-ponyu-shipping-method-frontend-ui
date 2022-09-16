define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/translate'
], function ($, ko, Component, quote, t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Emergento_PonyUShippingMethodFrontendUi/checkout/shipping/form'
        },

        slots: ko.observable(''),
        selectedSlotLabel: ko.observable(''),
        selectedSlotValue: ko.observable(''),

        isVisible: ko.observable(false),

        initialize: function () {
            this._super();
            quote.shippingMethod.subscribe(function () {
                this.isVisible(this.isCarrierSeleted());
                if (this.isCarrierSeleted()) {
                    this.updateSlotInformation(quote.shippingAddress());
                }
            }, this);
        },

        isCarrierSeleted: function(){
            let method = quote.shippingMethod();

            if (!method) {
                return false;
            }
            if (!method.hasOwnProperty('carrier_code')) {
               return false;
            }
            return (method.carrier_code === 'ponyu');
        },

        isAsap: function () {
            return window.checkoutConfig.ponyu.isAsap;
        },

        updateSlotInformation: function (address) {
            let self = this;

            if (!address) {
                return;
            }

            $.ajax({
                showLoader: true,
                url: '/slots/slots/getslots',
                type: "GET",
                dataType: 'json',
                data: {
                    'street' : address.street,
                    'postcode' : address.postcode,
                    'city' : address.city,
                    'country' : address.countryId,
                    'method_code' : quote.shippingMethod().method_code
                }
            }).done(function (response) {
                if (!response) {
                    self.slots('');
                }
                self.slots(response.slot);
                self.selectedSlotLabel(response.slot[0].label);
            });
        },
    });
});
