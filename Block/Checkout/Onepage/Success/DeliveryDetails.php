<?php
declare(strict_types=1);

namespace Emergento\PonyUShippingMethodFrontendUi\Block\Checkout\Onepage\Success;

use Emergento\PonyUShipment\Model\Service\IsPonyUOrder;
use Emergento\PonyUShippingMethod\Model\GenerateSlotLabel;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Display delivery details in checkout success page
 *
 * @api
 */
class DeliveryDetails extends Template
{
    public function __construct(
        Context                            $context,
        private readonly GenerateSlotLabel $generateSlotLabel,
        private readonly CheckoutSession   $checkoutSession,
        private readonly IsPonyUOrder      $isPonyUOrder,
        private readonly Json $json,
        array                              $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _toHtml(): string
    {
        $order = $this->checkoutSession->getLastRealOrder();
        if (!$this->isPonyUOrder->execute($order)) {
            return '';
        }

        if (!$order->getData('ponyu_slot')) {
            return '';
        }

        try {
            $this->setData('delivery_date', $this->getDeliveryDateLabel());
            $this->setData('error_message', $this->checkoutSession->getLastRealOrder()->getData('ponyu_shipment_creation_last_error'));
        } catch (\Exception $e) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * @return Phrase
     * @throws \Exception
     */
    private function getDeliveryDateLabel(): Phrase
    {
        $ponyUSlot = $this->json->unserialize($this->checkoutSession->getLastRealOrder()->getPonyuSlot());
        return $this->generateSlotLabel->execute(new \DateTime($ponyUSlot['deliveryDateStart']), new \DateTime($ponyUSlot['deliveryDateEnd']));
    }
}
