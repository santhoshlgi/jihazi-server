<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Api;

interface ForgetManagementInterface
{

    /**
     * POST for Login api
     * @param string $email
     *  @param string $phonenumber
     * @return string
     */
    
    public function forgetPassword($email,$phonenumber);
}

