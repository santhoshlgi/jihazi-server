<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Api;

interface ResetManagementInterface
{

    /**
     * POST for Login api
     * @param int $userid
     *  @param string $password
     * @return string
     */
    
    public function resetPassword($userid,$password);

    /**
     * POST for Login api
     * @param int $userid
     * @return string
     */

    public function myaccountusers($userid);

    /**
     * POST for Login api
     * @param int $userid
     * @return string
     */

    public function profilepageusers($userid);

    /**
     * POST for Login api
     * @param int $userid
     * @param string $firstname
     * @param string $lastname
     * @param string $notification
     * @param string $gender
     * @param string $profilepic
     * @return string
     */

    public function editprofileusers($userid,$firstname,$lastname,$notification,$gender,$profilepic);

    /**
     * POST for Login api
     * @param int $userid
     * @param string $message
     * @param string $email
     * @return string
     */

    public function contactususers($userid,$message,$email);

    /**
     * POST for Login api
     * @return string
     */

    public function faqusers();

    /**
     * POST for Login api
     * @param int $userid
     * @param string $currentpwd
     * @param string $newpwd
     * @return string
     */

    public function changepasswordusers($userid,$currentpwd,$newpwd);
}

