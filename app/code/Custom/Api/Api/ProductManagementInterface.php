<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Api;

interface ProductManagementInterface
{

    /**
     * POST for getCartProduct api
     *  @param string $userid
     * @return string
     */
    
    public function getCartProduct($userid);

    /**
     * POST for getWishlistProduct api
     *  @param string $userid
     * @return string
     */
    
    public function getWishlistProduct($userid);

    /**
     * POST for saveReview api
     *  @param string $userid
     *  @param string $productid
     *  @param string $rating
     *  @param string $headline
     *  @param string $review
     *  @param string $rating
     * @return string
     */

    public function saveReview($userid,$productid,$nickname,$headline,$review,$rating);

    /**
     * POST for getProductDetails api
     *  @param string $token
     *  @param string $productid
     * @return string
     */

    public function getProductDetails($token,$productid);

    /**
     * POST for getProductDetails api
     *  @param string $userid
     *  @param string $productid
     * @return string
     */

    public function addtocartProduct($userid,$productid);

    /**
     * POST for getProductDetails api
     *  @param string $userid
     *  @param string $productid
     * @return string
     */


    public function removeCartProduct($userid,$productid);

    /**
     * POST for getProductDetails api
     *  @param string $userid
     *  @param string $productid
     *  @param string $qty
     *  @return string
     */


    public function updateCartProduct($userid,$productid,$qty);

}

