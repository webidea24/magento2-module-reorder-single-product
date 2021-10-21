<?php declare(strict_types=1);

namespace Webidea24\ReorderSingleProduct\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;

class Single extends Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    protected $orderLoader;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        Registry $registry
    )
    {
        $this->orderLoader = $orderLoader;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Action for reorder
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $itemId = $this->_request->getParam('itemToAdd');
        $result = $this->orderLoader->load($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_coreRegistry->registry('current_order');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        /* @var $cart \Magento\Checkout\Model\Cart */
        $cart = $this->_objectManager->get(\Magento\Checkout\Model\Cart::class);
        $items = $order->getItemsCollection();

        $added = false;

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($items as $item) {
            if ($item->getId() !== $itemId) {
                continue;
            }

            try {
                $cart->addOrderItem($item);
                $added = true;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getUseNotice(true)) {
                    $this->messageManager->addNoticeMessage($e->getMessage());
                } else {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }

                return $resultRedirect->setPath('sales/order/view', [
                    'order_id' => $order->getId()
                ]);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t add this item to your shopping cart right now.')
                );
                return $resultRedirect->setPath('checkout/cart');
            }
        }

        if (!$added) {
            $this->messageManager->addErrorMessage(
                __('We can\'t add this item to your shopping cart right now.')
            );

            return $resultRedirect->setPath('sales/order/view', [
                'order_id' => $order->getId()
            ]);
        }

        $cart->save();
        return $resultRedirect->setPath('checkout/cart');
    }
}
