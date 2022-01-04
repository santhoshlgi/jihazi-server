<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Model;

use Magento\Framework\DataObject;

class ResetManagement implements \Custom\Api\Api\ResetManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function resetPassword($userid,$password)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer_check = $objectManager->get('Magento\Customer\Model\Customer');
        $customer_check->load($userid);
        
        if ( $customer_check->getId() ) {
        $customerRepositoryInterface = $objectManager->get('\Magento\Customer\Api\CustomerRepositoryInterface');
        $customerRegistry = $objectManager->get('\Magento\Customer\Model\CustomerRegistry');
        $encryptor = $objectManager->get('\Magento\Framework\Encryption\EncryptorInterface');
        $customer = $customerRepositoryInterface->getById($userid); // _customerRepositoryInterface is an instance of \Magento\Customer\Api\CustomerRepositoryInterface
        if($customer){
            $customerSecure = $customerRegistry->retrieveSecureData($userid); // _customerRegistry is an instance of \Magento\Customer\Model\CustomerRegistry
            $customerSecure->setRpToken(null);
            $customerSecure->setRpTokenCreatedAt(null);
            $customerSecure->setPasswordHash($encryptor->getHash($password, true)); // here _encryptor is an instance of \Magento\Framework\Encryption\EncryptorInterface
            $customerRepositoryInterface->save($customer);
            $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'Your password reset successfully';
                    }else{
                        $msg = 'تم اضافة التقييم بنجاح';
                    }
                }else{
                    $msg = 'Your password reset successfully';   
                }
            $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => '');
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Your password reset Failure';
                }else{
                    $msg = 'تم إعادة تعيين كلمة المرور الخاصة بك بنجاح';
                }
            }else{
                $msg = 'Your password reset Failure';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => '');
        }
        
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
        }
    }
    public function myaccountusers($userid){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();

        $customer = $customerFactory->load($userid);
        if(!empty($customer->getFirstname())){
            $name = $customer->getFirstname().' '.$customer->getLastname();
            $data = [
                'profileImage' => 'https://image.shutterstock.com/image-photo/frontal-portrait-serious-looking-businessman-600w-1706382364.jpg',
                'name' => $name,
                'email' => $customer->getEmail(),
                'walletBalance' => '13 SAR'
            ];
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'My Account';
                }else{
                    $msg = 'حسابي';
                }
            }else{
                $msg = 'My Account';   
            }
            $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => $data);
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'User Not Found';
                }else{
                    $msg = 'لم يتم العثور على المستخدم';
                }
            }else{
                $msg = 'User Not Found';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg, 'data' => []);
        }
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }

    public function profilepageusers($userid){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
        $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
        $customerAddress = array();
        $check = 0 ;
        foreach ($customerObj->getAddresses() as $address)
        {
            $customerAddress['id'] = $address->getId();
            $customerAddress['first_name'] = $address->getFirstname();
            $customerAddress['last_name'] = $address->getLastname();
            $customerAddress['country'] = $address->getCountryId();
            $customerAddress['postcode'] = $address->getPostcode();
            $customerAddress['city'] = $address->getCity();
            $customerAddress['address'] = $address->getStreet();
            $customerAddress['phone'] = $address->getTelephone();
            $customerAddress['company'] = $address->getCompany();
            $customerAddress['state'] = $address->getRegion();
            $check = 1;
            $data[] = array('addressId' => $customerAddress['id'] , 'firstname' => $customerAddress['first_name'],'lastname' => $customerAddress['last_name'],
            'country' => $customerAddress['country'],'postcode' => $customerAddress['postcode'],
            'city'=> $customerAddress['city'], 'location' => $customerAddress['address'], 'phone' => $customerAddress['phone'],
            'company' => $customerAddress['company'] , 'state' => $customerAddress['state']);
        }
        $customer = $customerFactory->load($userid);
        if(!empty($customer->getFirstname())){
            if($check == 1){
                $name = $customer->getFirstname().' '.$customer->getLastname();
                $data = [
                    'profileImage' => 'https://image.shutterstock.com/image-photo/frontal-portrait-serious-looking-businessman-600w-1706382364.jpg',
                    'displayName' => $name,
                    'email' => $customer->getEmail(),
                    'firstname' => $customer->getFirstname(),
                    'lastname' => $customer->getLastname(),
                    'Gender' => 'Male',
                    'notification' => 'enabled',
                    'billingAddress' => $customerAddress
                ];
                $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'Profile Detail';
                    }else{
                        $msg = 'تفاصيل الملف الشخصي';
                    }
                }else{
                    $msg = 'Profile Detail';   
                }
                $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => $data);
            }else{
                $name = $customer->getFirstname().' '.$customer->getLastname();
                $data = [
                    'profileImage' => 'https://image.shutterstock.com/image-photo/frontal-portrait-serious-looking-businessman-600w-1706382364.jpg',
                    'displayName' => $name,
                    'email' => $customer->getEmail(),
                    'firstname' => $customer->getFirstname(),
                    'lastname' => $customer->getLastname(),
                    'Gender' => 'Male',
                    'notification' => 'enabled',
                    'billingAddress' => []
                ];
                $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'Profile Detail';
                    }else{
                        $msg = 'تفاصيل الملف الشخصي';
                    }
                }else{
                    $msg = 'Profile Detail';   
                }
                $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => $data);
            }
            
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'User Not Found';
                }else{
                    $msg = 'لم يتم العثور على المستخدم';
                }
            }else{
                $msg = 'User Not Found';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => []);
        }
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }

    public function faqusers(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $content = $objectManager->create('\Magento\Cms\Model\Page');
        // $content->load('store_id',2);
        // echo $content->getContent();
        $getHeaders = apache_request_headers();
        if(isset($getHeaders['lang'])){
            $lang = $getHeaders['lang'];
            if($lang == 1){
                $msg = 'Data Found';
                $content->load('faqs', 'identifier');
            }else{
                $msg = 'بيانات';
                $content->load(27);
            }
            
        }else{
            $msg = 'Data Found';  
            $content->load('faqs', 'identifier'); 
        }
        $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => $content->getContent());
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }

    public function contactususers($userid,$message,$email){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $mail = $objectManager->get('Magento\Contact\Model\MailInterface');
        $obj = $objectManager->get('Magento\Framework\DataObject');
        $transportBuilder = $objectManager->get('Magento\Framework\Mail\Template\TransportBuilder');
        $contactForm['name'] =$userid;
        $contactForm['email'] =$email;
        $contactForm['comment'] =$message;
        $contactForm['telephone'] = '8530443561';
        // $res = $mail->send(
        //     $contactForm['email'],
        //     ['data' => new DataObject($contactForm)]
        // );
        // $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        //     $transport = $transportBuilder
        //         ->setTemplateIdentifier('contact_email_email_template')
        //         ->setTemplateOptions(
        //             [
        //                 'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
        //                 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
        //             ]
        //         );
        //    $transport->setTemplateVars(['data' => 'nonprende'])
        //         ->setFrom('multanizaid4@gmail.com','admin')
        //         ->addTo($email);
        //         // ->getTransport();
        //         $transport = $transport->getTransport();
        //         $transport->sendMessage(); 
        $to = [$email,'smvatech@gmail.com'];
        $email = new \Zend_Mail();
        $email->setSubject("Contact Us");
        $email->setBodyText($message);
        $email->setFrom('smvatech@gmail.com', 'admin');
        $email->addTo($to, 'demo');
        $email->send();
        $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Email Send';
                }else{
                    $msg = 'إرسال البريد الإلكتروني';
                }
            }else{
                $msg = 'Email Send';   
            }
        $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => []);
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }

    public function editprofileusers($userid,$firstname,$lastname,$notification,$gender,$profilepic){
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerRepository = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
        
        // load customer info by email
        // $customer = $customerRepository->get('John.Smith@example.com', $websiteId);
        
        // load customer info by id    
        $customer = $customerRepository->getById($userid);
        if(!empty($customer->getFirstname())){
            $customer->setFirstname($firstname);
            $customer->setLastname($lastname);
            $customerRepository->save($customer);
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Profile updated Successfully';
                }else{
                    $msg = 'تم تحديث الملف الشخصي بنجاح';
                }
            }else{
                $msg = 'Profile updated Successfully';   
            }
            $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => []);
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'User Not Found';
                }else{
                    $msg = 'لم يتم العثور على المستخدم';
                }
            }else{
                $msg = 'User Not Found';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => []);
        }
        // Update customer Info
        // $customer->setFirstname($firstname);
        // $customer->setLastname($lastname);
        // $customer->setEmail('John.Smith@example.com');
        
        // $customer->setCustomAttribute('attribute_code', 'attribute value');
        
        // $customerRepository->save($customer);

        
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die(); 
    }
    public function changepasswordusers($userid,$currentpwd,$newpwd){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerAccountManagement = $objectManager->get('Magento\Customer\Api\AccountManagementInterface');
        $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
        $customerRepositoryInterface = $objectManager->get('\Magento\Customer\Api\CustomerRepositoryInterface');
        $customerRegistry = $objectManager->get('\Magento\Customer\Model\CustomerRegistry');
        $encryptor = $objectManager->get('\Magento\Framework\Encryption\EncryptorInterface');
        try {
            $customer = $customerFactory->load($userid);
            if(empty($customer->getEmail())){
                $notif = array('SuccessCode'=> 400 , 'message' => 'User Not Found' , 'data' => []);
                header("Content-Type: application/json; charset=utf-8");
                $ns = json_encode($notif);
                print_r($ns,false);
                die();
            }
            // $customers = $customerAccountManagement->authenticate($customer->getEmail(), $currentpwd);
            // echo $customers;
            if(!empty($customerAccountManagement->authenticate($customer->getEmail(), $currentpwd))){
                $customerpass = $customerRepositoryInterface->getById($userid); 
                $customerSecure = $customerRegistry->retrieveSecureData($userid);
                $customerSecure->setRpToken(null);
                $customerSecure->setRpTokenCreatedAt(null);
                $customerSecure->setPasswordHash($encryptor->getHash($newpwd, true));
                $customerRepositoryInterface->save($customerpass);
                $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Password Updated';
                }else{
                    $msg = 'تم تحديث كلمة المرور';
                }
            }else{
                $msg = 'Password Updated';   
            }
                $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => []);
            }
        } catch (\Exception $e) {
            //Authentication Failed
        }
        if(empty($notif)){
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Password Not Match';
                }else{
                    $msg = 'كلمة المرور غير متطابقة';
                }
            }else{
                $msg = 'Password Not Match';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => []);
        }
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();  
    }
}

