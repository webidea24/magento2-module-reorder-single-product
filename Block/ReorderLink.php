<?php declare(strict_types=1);


namespace Webidea24\ReorderSingleProduct\Block;


use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

/**
 * @method self setItem(Item $item)
 * @method Item getItem()
 */
class ReorderLink extends Template
{


    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    private $postHelper;

    protected $_template = 'Webidea24_ReorderSingleProduct::reorder-link.phtml';

    public function __construct(Template\Context $context, PostHelper $postHelper, array $data = [])
    {
        parent::__construct($context, $data);
        $this->postHelper = $postHelper;
    }

    public function getOrder(): Order
    {
        return $this->getItem()->getOrder();
    }

    public function getReorderPostParams(): string
    {
        return $this->postHelper->getPostData(
            'reorder/order/single', [
            'order_id' => $this->getOrder()->getId(),
            'itemToAdd' => $this->getItem()->getId()
        ]);
    }
}
