<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Model;

class LoginManagement implements \Custom\Api\Api\LoginManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function postLogin($email,$password,$os)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $BaseUrl = $storeManager->getStore()->getBaseUrl();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerAccountManagement = $objectManager->get('Magento\Customer\Api\AccountManagementInterface');
        $chk = 0;
        try {
            $cusstomer = $customerAccountManagement->authenticate($email, $password);
            $chk = '1';
        } catch (\Exception $e) {
            // $cusstomer = "";
            $chk = '2';
        }

        // if(!empty($customerAccountManagement->authenticate($email, $password))){
        //     $chk = '1';
        // }else{
        //     $chk = '2';
        // }

        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        // $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory'); 
        // $customer = $customerFactory->create();

       
        // $customer->loadByEmail($email);

        // print_r($customer->getData());

        $res = $BaseUrl.'rest/V1/integration/customer/token';
        $postRequest = array(
            'username' => $email,
            'password' => $password
        );
        
        $cURLConnection = curl_init($res);
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        
        $apiResponse = curl_exec($cURLConnection);
        $data = json_decode($apiResponse, true);
        curl_close($cURLConnection);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $CustomerModel = $objectManager->create('Magento\Customer\Model\Customer');
        $CustomerRepositoryInterface = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
        $AddressRepositoryInterface = $objectManager->create('Magento\Customer\Api\AddressRepositoryInterface');

        $CustomerModel->setWebsiteId(1);
        $CustomerModel->loadByEmail($email);
        $userId = $CustomerModel->getId();

        
        if($userId){
            $customer = $CustomerRepositoryInterface->getById($userId);
            // $billingAddressId = $customer->getDefaultBilling();
            // $shippingAddressId = $customer->getDefaultShipping();

            // $billingAddress = $AddressRepositoryInterface->getById($billingAddressId);
            // $telephone = $billingAddress->getTelephone();
            if(!isset($data['message']) || $chk == 1){
                if(isset($data['message'])){
                    $data ='';
                }
                $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'Login Successfully!';
                    }else{
                        $msg = 'تم تسجيل الخروج بنجاح';
                    }
                }else{
                    $msg = 'Login Successfully!';   
                }
                // $notif = array('SuccessCode'=> 200 , 'message' => $msg ,'token' => $data,'os' => $os, 'data' => array('userid' => $userId, 'first name' => $CustomerModel->getFirstname(), 'last name' => $CustomerModel->getLastname(), 'email' => $email, 'phone' => $telephone));
                $notif = array('SuccessCode'=> 200 , 'message' => $msg ,'token' => $data,'os' => $os, 'data' => array('userid' => $userId, 'first name' => $CustomerModel->getFirstname(), 'last name' => $CustomerModel->getLastname(), 'email' => $email, 'phone' =>  $CustomerModel->getMobilenumber()));
            }else{
                $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'Invalid Password';
                    }else{
                        $msg = 'كلمة المرور غير صالحه';
                    }
                }else{
                    $msg = 'Invalid Password';   
                }
                $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => "");  
            }
            
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Invalid Email';
                }else{
                    $msg = 'البريد الالكتروني غير صالح';
                }
            }else{
                $msg = 'Invalid Email';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg, 'data' => "");
        }
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }
}

