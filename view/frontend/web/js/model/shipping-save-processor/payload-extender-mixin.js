define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
], function ($, wrapper, quote) {
    'use strict';

    return function (payloadExtender) {
        return wrapper.wrap(payloadExtender, function (originalAction, payload) {
            if (!quote.shippingMethod().hasOwnProperty('carrier_code')) {
                return payload;
            }

            if (quote.shippingMethod().carrier_code !== 'ponyu') {
                payload = originalAction(payload);
                payload.addressInformation['extension_attributes'].ponyu_slot = '';
                return payload;
            }
            payload = originalAction(payload);
            payload.addressInformation['extension_attributes'].ponyu_slot = $('#carrier-slot').val();
            return payload;
        });
    };
});
