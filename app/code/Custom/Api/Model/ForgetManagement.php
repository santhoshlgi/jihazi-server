<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Model;

class ForgetManagement implements \Custom\Api\Api\ForgetManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function forgetPassword($email,$phonenumber)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $BaseUrl = $storeManager->getStore()->getBaseUrl();

        $res = $BaseUrl.'rest/V1/customers/password';

        // $res = 'https://smvatech.in/ecommerce/rest/V1/customers/password';
        // $data = array("a" => $a);
        $postRequest = array(
            'email' => $email,
            'template' => 'email_reset',
            'websiteId' => 1
        );
        $ch = curl_init($res);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postRequest));

        $response = curl_exec($ch);

        $data = json_decode($response, true);
        if($data == 1){
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Please check email address for reset password link';
                }else{
                    $msg = 'الرجاء التحقق من عنوان البريد الإلكتروني لإعادة تعيين  كلمة المرور';
                }
            }else{
                $msg = 'Please check email address for reset password link';   
            }
                $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => "");
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Invalid user credentials';
                }else{
                    $msg = 'بيانات اعتماد المستخدم غير صالحة';
                }
            }else{
                $msg = 'Invalid user credentials';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => "");
        }
        
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }
}

