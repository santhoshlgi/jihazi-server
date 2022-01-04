<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Model;

class WishlistManagement implements \Custom\Api\Api\WishlistManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function addWishlist($userid,$productid)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');

        $collection = $productCollection->addAttributeToSelect('*')
        ->setPageSize(3)
        ->addAttributeToSort('updated_at', 'DESC')
        ->load();

        foreach($collection as $product){

        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);


        $wishList = $objectManager->get('\Magento\Wishlist\Model\WishlistFactory');

        $wishlistAdd = $wishList->create()->loadByCustomerId($userid, true);
        $wishlistAdd->addNewItem($product);
        $wishlistAdd->save();

        }
        if($wishlistAdd->getId()){
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Product added to Wishlist!';
                }else{
                    $msg = 'تمت إضافة المنتج إلى قائمة الرغبات!';
                }
            }else{
                $msg = 'Product added to Wishlist!';   
            }
            $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => "");
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Product not added to Wishlist!';
                }else{
                    $msg = 'لم يتم إضافة المنتج إلى قائمة الرغبات!';
                }
            }else{
                $msg = 'Product not added to Wishlist!';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => "");
        }
        
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }

    public function removeWishlist($userid,$productid){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $wishlist = $objectManager->create('Magento\Wishlist\Model\Wishlist');
        $wish = $wishlist->loadByCustomerId($userid);
        $items = $wish->getItemCollection();
        $chk = 0;
        /** @var \Magento\Wishlist\Model\Item $item */
        foreach ($items as $item) {
            if ($item->getProductId() == $productid) {
                $item->delete();
                $wish->save();
                $chk = 1;
            }
        }
        if($chk == 1){
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Product removed from Wishlist';
                }else{
                    $msg = 'تمت إزالة المنتج من قائمة الرغبات';
                }
            }else{
                $msg = 'Product removed from Wishlist';   
            }
            $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => "");
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Product not removed from Wishlist';
                }else{
                    $msg = 'لم تتم إزالة المنتج من قائمة الرغبات';
                }
            }else{
                $msg = 'Product not removed from Wishlist';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => "");
        }
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();   
    }
}

