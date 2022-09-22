<?php
declare(strict_types=1);

namespace Emergento\PonyUShippingMethodFrontendUi\Controller\Slots;

use Emergento\PonyUShippingMethod\Model\Config;
use Emergento\PonyUShippingMethod\Model\IsCartSlotValid;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Validate delivery slot before order submission
 */
class IsDeliveryValid implements HttpGetActionInterface
{

    public function __construct(
        private readonly JsonFactory $resultJsonFactory,
        private readonly Session $session,
        private readonly IsCartSlotValid $isCartSlotValid,
        private readonly Config $config
    ) {
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultJsonFactory->create();

        if ($this->config->isNextAvailableSlotEnabled((int) $this->session->getQuote()->getStoreId())) {
            $result->setHttpResponseCode(200);
            $result->setData([]);
            return $result;
        }

        try {
            $this->isCartSlotValid->execute($this->session->getQuote());
        } catch (LocalizedException | NoSuchEntityException $e) {
            return $result->setHttpResponseCode(400);
        }

        $result->setHttpResponseCode(200);
        $result->setData([]);
        return $result;
    }
}
