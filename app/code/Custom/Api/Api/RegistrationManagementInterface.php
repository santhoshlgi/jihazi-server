<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Api;

interface RegistrationManagementInterface
{

    /**
     * POST for Login api
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     *  @param string $password
     *  @param string $phonenumber
     * @return string
     */
    
    public function registerDetails($firstname,$lastname,$email,$password,$phonenumber);

    /**
     * POST for Login api
     * @param string $userId
     * @param string $firstname
     * @param string $lastname
     * @param string $country
     * @param string $postcode
     * @param string $address
     * @param string $city
     * @param string $phone
     * @param string $isDefaultBilling
     * @param string $isDefaultShipping
     * @param string $company
     * @param string $state
     * @param string $regionId
     * @return string
     */

    public function addShippingAddress($userId,$firstname,$lastname,$postcode,$country,$city,$address,$phone,$isDefaultBilling,$isDefaultShipping,$company,$state,$regionId);


    /**
     * POST for Login api
     * @param string $userId
     * @param string $addressId
     * @return string
     */

    public function deleteShippingAddress($userId,$addressId);

    /**
     * POST for Login api
     * @param string $userId
     * @return string
     */

    public function listShippingAddress($userId);

    /**
     * POST for Login api
     * @param string $userId
     * @param string $addressId
     * @param string $firstname
     * @param string $lastname
     * @param string $country
     * @param string $postcode
     * @param string $address
     * @param string $city
     * @param string $phone
     * @param string $isDefaultBilling
     * @param string $isDefaultShipping
     * @param string $company
     * @param string $state
     * @param string $regionId
     * @return string
     */

    public function editShippingAddress($userId,$addressId,$firstname,$lastname,$postcode,$country,$city,$address,$phone,$isDefaultBilling,$isDefaultShipping,$company,$state,$regionId);

    /**
     * POST for Login api
     * @return string
     */

    public function countryListShippingAddress();
}

