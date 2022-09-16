define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/view/shipping-information'
], function ($, $t, messageList, quote, shippingRateRegistry, shippingInformation) {
        'use strict';
        return {
            validate: function () {
                var result = false;

                $.ajax({
                    showLoader: true,
                    url: '/slots/slots/isDeliveryValid',
                    type: "GET",
                    async: false
                }).success(function () {
                    result = true;
                }).error(function () {
                    result = false;
                });
                if (result === false) {
                    messageList.addErrorMessage({ message: $t('Slot is not available anymore. Please select another delivery slot.') });
                    let address = quote.shippingAddress();
                    shippingRateRegistry.set(address.getKey(), null);
                    shippingRateRegistry.set(address.getCacheKey(), null);
                    quote.shippingAddress(address);
                    shippingInformation().backToShippingMethod();
                    return false;
                }

                return result;
            },
        }
    }
);
