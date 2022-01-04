<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Api;

interface LoginManagementInterface
{

    /**
     * POST for Login api
     * @param string $email
     * @param string $password
     * @param string $os
     * @return string
     */
    
    public function postLogin($email,$password,$os);
}

