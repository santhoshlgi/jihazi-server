<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Api;

interface WishlistManagementInterface
{

    /**
     * POST for Login api
     * @param string $userid
     *  @param string $productid
     * @return string
     */
    
    public function addWishlist($userid,$productid);

    /**
     * POST for Login api
     * @param string $userid
     *  @param string $productid
     * @return string
     */


    public function removeWishlist($userid,$productid);
}

