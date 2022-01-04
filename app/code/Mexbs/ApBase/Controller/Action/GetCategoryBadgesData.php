<?php

// @codingStandardsIgnoreFile

namespace Mexbs\ApBase\Controller\Action;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;

class GetCategoryBadgesData extends \Magento\Framework\App\Action\Action
{
    private $cart;
    private $helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Mexbs\ApBase\Helper\Data $helper
    ){
        $this->cart = $cart;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $productIdsInPage = $this->getRequest()->getParam('product_ids_in_page');
        $popupsHtmlData = ['badges_for_category' => $this->helper->getCategoryBadgesToDisplayForQuote($this->cart->getQuote(), $productIdsInPage)];

        echo \Zend_Json::encode($popupsHtmlData);
        die();
    }
}