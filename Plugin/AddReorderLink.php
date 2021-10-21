<?php declare(strict_types=1);


namespace Webidea24\ReorderSingleProduct\Plugin;


use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;
use Webidea24\ReorderSingleProduct\Block\ReorderLink;

class AddReorderLink
{

    /**
     * @param \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject
     * @param $result
     * @param \Magento\Sales\Model\Order\Item|null $item
     */
    public function afterGetItemRowTotalHtml(DefaultRenderer $subject, $result, $item = null)
    {
        /** @var ReorderLink $block */
        $block = $subject->getLayout()->createBlock(ReorderLink::class);
        $block->setItem($item ?? $subject->getItem());
        return $result . $block->toHtml();
    }
}
