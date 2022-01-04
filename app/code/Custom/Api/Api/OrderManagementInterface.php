<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Api;

interface OrderManagementInterface
{
    /**
     * POST for getCartProduct api
     *  @param string $userid
     * @return string
     */
    
    public function myOrdersList($userid);

    /**
     * POST for getCartProduct api
     *  @param string $userid
     *  @param string $orderid
     * @return string
     */
    
    public function detailsOrders($userid,$orderid);

    /**
     * POST for getCartProduct api
     *  @param string $userid
     *  @param string $couponCode
     * @return string
     */

    public function applycouponOrders($userid,$couponCode);
    
    /**
     * POST for getCartProduct api
     *  @param string $userid
     * @return string
     */
    
    public function removecouponOrders($userid);


    /**
     * POST for getCartProduct api
     *  @param string $orderid
     *  @param string $paymentStatus
     * @return string
     */

    public function updatestatusOrders($orderid,$paymentStatus);

    /**
     * POST for getCartProduct api
     *  @param string $shippingMethod
     *  @param string $paymentMethod
     * @param string $userid
     * @return string
     */

    public function createOrders($shippingMethod,$paymentMethod,$userid);

}