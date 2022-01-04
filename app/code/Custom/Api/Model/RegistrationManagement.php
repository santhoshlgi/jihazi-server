<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Model;

class RegistrationManagement implements \Custom\Api\Api\RegistrationManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerDetails($firstname,$lastname,$email,$password,$phonenumber)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $BaseUrl = $storeManager->getStore()->getBaseUrl();
        $res = $BaseUrl.'rest/V1/customers';
        $postRequest = array(
            'customer' => array(
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname
            ),
            'password' => $password
        );
        
        $cURLConnection = curl_init($res);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($postRequest));
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($cURLConnection);
        $data = json_decode($apiResponse, true);
        curl_close($cURLConnection);
        // print_r($data);
        if(isset($data['id'])){
            if(isset($phonenumber)){
                $customerRepoInterface = $objectManager->get('\Magento\Customer\Api\CustomerRepositoryInterface');
                $customer = $customerRepoInterface->getById($data['id']); 
                // $customerData = $customer->getDataModel();
                $customer->setCustomAttribute('mobilenumber', "+".$phonenumber);
                // $customer->updateData($customerData);
                // $customer->save();
                $customerRepoInterface->save($customer);

            }
            // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            // $addresss = $objectManager->get('\Magento\Customer\Api\CustomerRepositoryInterface');

            // $address = $addresss->create();

            // $address->setCustomerId($data['id'])

            // ->setFirstname('test')

            // ->setLastname('test')

            // ->setCountryId('IN')

            // ->setPostcode(10000)

            // ->setCity('Bangalore')

            // ->setTelephone($phonenumber)

            // ->setCompany('BNG')

            // ->setStreet('test')

            // ->setIsDefaultBilling('1')

            // ->setIsDefaultShipping('1')

            // ->setSaveInAddressBook('1');

            // $address->save();
            $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'Transaction was Successful';
                    }else{
                        $msg = 'المعاملة ناجحه';
                    }
                }else{
                    $msg = 'Transaction was Successful';   
                }
                $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => array('userid' => $data['id'], 'first name' => $data['firstname'], 'last name' => $data['lastname'], 'email' => $data['email'] , 'mobilenumber' => $phonenumber));
        }else{
            $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'Customer Already Exist';
                    }else{
                        $msg = 'المستخدم بالفعل موجود';
                    }
                }else{
                    $msg = 'Customer Already Exist';   
                }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => "");
        }
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }

    public function addShippingAddress($userId,$firstname,$lastname,$postcode,$country,$city,$address,$phone,$isDefaultBilling,$isDefaultShipping,$company,$state,$regionId){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $Addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');

        $Address = $Addresss->create();
        if($state == "null"){
            $Address->setCustomerId($userId)

            ->setFirstname($firstname)

            ->setLastname($lastname)

            ->setCountryId($country)

            ->setPostcode($postcode)

            ->setCity($city)

            ->setCompany($company)

            ->setTelephone($phone)

            ->setStreet($address)

            // ->setRegion($state)

            ->setRegionId($regionId)

            ->setIsDefaultBilling($isDefaultBilling)

            ->setIsDefaultShipping($isDefaultShipping)

            ->setSaveInAddressBook('1');   
        }else{
            
            $Address->setCustomerId($userId)

            ->setFirstname($firstname)

            ->setLastname($lastname)

            ->setCountryId($country)

            ->setPostcode($postcode)

            ->setCity($city)

            ->setCompany($company)

            ->setTelephone($phone)

            ->setStreet($address)

            ->setRegion($state)

            ->setIsDefaultBilling($isDefaultBilling)

            ->setIsDefaultShipping($isDefaultShipping)

            ->setSaveInAddressBook('1');

        }

        $Address->save();
        if($Address->getId()){
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Shipping address updated Successfully';
                }else{
                    $msg = 'تم تحديث عنوان الشحن بنجاح';
                }
            }else{
                $msg = 'Shipping address updated Successfully';   
            }
            $notif = array('SuccessCode'=> 200 , 'message' => $msg , 'data' => 
            array('addressId' =>$Address->getId() , 'firstname' =>$firstname,'lastname' =>$lastname, 'country' =>$country,'postcode' => $postcode,
            'city'=>$city, 'address' => $address,'state' => $state,'regionId' => $regionId,'isDefaultBilling' => $isDefaultBilling,'isDefaultShipping' => $isDefaultShipping,
            'phone' => $phone,'company' => $company)
            );
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Shipping address not uploaded';
                }else{
                    $msg = 'لم يتم تحميل عنوان الشحن';
                }
            }else{
                $msg = 'Shipping address not uploaded';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg , 'data' => '');
        }
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }
    public function deleteShippingAddress($userId,$addressId){
        // $customerID = 10; // your customer-id
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $customerObj = $objectManager->create('Magento\Customer\Model\Customer')
        //             ->load($customerID);
        // $customerFirstName = $customerObj->getFirstname();

        $customerId = $userId;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $customerAddress = array();
        $check = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();

        
        $customer = $customerFactory->load($userId);

        $chkuser =  $customer->getId();
        // echo $chkuser;
        if(isset($chkuser)){
            foreach ($customerObj->getAddresses() as $address)
            {
                // $customerAddress[] = $address->toArray();
                // print_r($address->getId());
                if($address->getId() == $addressId){
                    $check = 1;
                }
            }    
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'User Not Exist!';
                }else{
                    $msg = 'لم يتم العثور على المستخدم';
                }
            }else{
                $msg = 'User Not Exist!';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg);
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();
        }
        if($check == 1){
            $objectManager->create('Magento\Customer\Api\AddressRepositoryInterface')->deleteById($addressId);
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Address Deleted!';
                }else{
                    $msg = 'تم حذف العنوان!';
                }
            }else{
                $msg = 'Address Deleted!';   
            }
            $notif = array('SuccessCode'=> 200 , 'message' => $msg);
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Address Not Found!';
                }else{
                    $msg = 'لم يتم العثور على العنوان!';
                }
            }else{
                $msg = 'Address Not Found!';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg); 
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die(); 
        }
        // print_r($customerAddress-);
        // foreach ($customerAddress as $customerAddres) {
        //     echo $customerAddres;
        // }
        // $notif = array('SuccessCode'=> 400);
    }

    public function listShippingAddress($userId){
        
        $customerId = $userId;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $customerAddress = array();
        $check = 0 ;
        $customer = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface')->getById($userId);
        $addressRepository = $objectManager->create('Magento\Customer\Api\AddressRepositoryInterface');
        // $customer = $this->customerRepository->getById($customerId);
        $billingAddressId = $customer->getDefaultBilling();
        $shippingAddressId = $customer->getDefaultShipping();

        // foreach ($customerObj->getAddresses() as $address)
        // {
        //     $customerAddress[] = $address->toArray();
        // }
        // foreach ($customerAddress as $customerAddres) {

        //     echo $customerAddres['street'];
        //     echo $customerAddres['isDefaultBilling'];
        // }
        // // print_r($customerAddress['isDefaultBilling']);
        //  exit();
        foreach ($customerObj->getAddresses() as $address)
        {
            if($address->getId() == $billingAddressId && $address->getId() == $shippingAddressId){
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
                $customerAddress['isDefaultBilling'] = '1';
                $customerAddress['isDefaultShipping'] = '1';
            }elseif($address->getId() == $billingAddressId && $address->getId() != $shippingAddressId){
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
                $customerAddress['isDefaultBilling'] = '1';
                $customerAddress['isDefaultShipping'] = '0';   
            }elseif($address->getId() == $shippingAddressId && $address->getId() != $billingAddressId){
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
                $customerAddress['isDefaultBilling'] = '0';
                $customerAddress['isDefaultShipping'] = '1';
            }else{
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
                $customerAddress['isDefaultBilling'] = '0';
                $customerAddress['isDefaultShipping'] = '0';
            }
            $check = 1;
            $data[] = array('addressId' => $customerAddress['id'] , 'firstname' => $customerAddress['first_name'],'lastname' => $customerAddress['last_name'],
            'country' => $customerAddress['country'],'postcode' => $customerAddress['postcode'],
            'city'=> $customerAddress['city'], 'location' => $customerAddress['address'], 'phone' => $customerAddress['phone'],
            'company' => $customerAddress['company'] , 'state' => $customerAddress['state'] , 'isDefaultBilling' => $customerAddress['isDefaultBilling'],
            'isDefaultShipping' => $customerAddress['isDefaultShipping']);
        }
        if($check == 1){
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Address Found!';
                }else{
                    $msg = 'تم العثور على العنوان!';
                }
            }else{
                $msg = 'Address Found!';   
            }
            $notif = array('SuccessCode'=> 200 ,'message' => $msg,'data' => $data);
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Address Not Found!';
                }else{
                    $msg = 'لم يتم العثور على العنوان!';
                }
            }else{
                $msg = 'Address Not Found!';   
            }
            $notif = array('SuccessCode'=> 400 ,'message' => $msg,'data' => array());
        }
        // foreach ($customerAddress as $customerAddres) {

        //     // echo $customerAddres['street'];
        //     // echo $customerAddres['city'];
            
        // }
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }
    public function editShippingAddress($userId,$addressId,$firstname,$lastname,$postcode,$country,$city,$address,$phone,$isDefaultBilling,$isDefaultShipping,$company,$state,$regionId){
        $adddress_id = $addressId;
        $obj = \Magento\Framework\App\ObjectManager::getInstance();
        $customerObj = $obj->create('Magento\Customer\Model\Customer')->load($userId);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();

        
        $customer = $customerFactory->load($userId);
        $customerAddress = array();
        $checks = 0;
        $chkuser =  $customer->getId();
        // echo $chkuser;
        if(isset($chkuser)){
            foreach ($customerObj->getAddresses() as $adddress)
            {
                // $customerAddress[] = $address->toArray();
                // print_r($address->getId());
                if($adddress->getId() == $addressId){
                    $checks = 1;
                }
            }    
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'User Not Exist!';
                }else{
                    $msg = 'لم يتم العثور على المستخدم';
                }
            }else{
                $msg = 'User Not Exist!';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg);
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();
        }
        if($checks == 1){
            $Address = $obj->create('\Magento\Customer\Model\Address')->load($adddress_id);
            if($state == "null"){
                $Address->setTelephone($phone)
                ->setCountryId($country)
                ->setPostcode($postcode)
                ->setCity($city)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setStreet($address) //you can also pass array for street
                ->setSaveInAddressBook('1')
                ->setRegionId($regionId)
                ->setCompany($company)
                ->setIsDefaultBilling($isDefaultBilling)
                ->setIsDefaultShipping($isDefaultShipping)
                ->save();
            }else{
                $Address->setTelephone($phone)
                ->setCountryId($country)
                ->setPostcode($postcode)
                ->setCity($city)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setStreet($address) //you can also pass array for street
                ->setSaveInAddressBook('1')
                ->setRegion($state)
                ->setCompany($company)
                ->setIsDefaultBilling($isDefaultBilling)
                ->setIsDefaultShipping($isDefaultShipping)
                ->save();
            }
                    $getHeaders = apache_request_headers();
                    if(isset($getHeaders['lang'])){
                        $lang = $getHeaders['lang'];
                        if($lang == 1){
                            $msg = 'Address Updated!';
                        }else{
                            $msg = 'تم تحديث العنوان!';
                        }
                    }else{
                        $msg = 'Address Updated!';   
                    }
                    $notif = array('SuccessCode'=> 200 ,'message' => $msg);
                    header("Content-Type: application/json; charset=utf-8");
                    $ns = json_encode($notif);
                    print_r($ns,false);
                    die();
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Address Not Found!';
                }else{
                    $msg = 'لم يتم العثور على العنوان!';
                }
            }else{
                $msg = 'Address Not Found!';   
            }
            $notif = array('SuccessCode'=> 400 , 'message' => $msg); 
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();  
        }
        // if($address->getId()){
        //     $notif = array('SuccessCode'=> 200 ,'message' => 'Address Updated!');
        // }else{
        //     $notif = array('SuccessCode'=> 400 ,'message' => 'Address Not Updated!');
        // }
        // header("Content-Type: application/json; charset=utf-8");
        // $ns = json_encode($notif);
        // print_r($ns,false);
        // die();
    }
    public function countryListShippingAddress(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        // $countryCollectionFactory = $objectManager->get('Magento\Directory\Model\ResourceModel\Country\CollectionFactory');

        // // Get country collection
        // $countryCollection = $countryCollectionFactory->create()->loadByStore();

        // echo "<pre>";
        // print_r($countryCollection->getData());
        // echo "</pre>";

        $countryHelper = $objectManager->get('Magento\Directory\Model\Config\Source\Country'); 
        $countryFactory = $objectManager->get('Magento\Directory\Model\CountryFactory');

        $countries = $countryHelper->toOptionArray(); //Load an array of countries

            foreach ( $countries as $countryKey => $country ) {

                if ( $country['value'] != '' ) { //Ignore the first (empty) value

                    $stateArray = $countryFactory->create()->setId(
                        $country['value']
                    )->getLoadedRegionCollection()->toOptionArray(); //Get all regions for the given ISO country code

                    if ( count($stateArray) > 0 ) { //Again ignore empty values
                        $countries[$countryKey]['states'] = $stateArray;
                    }

                }
            }

        // var_dump($countries);
        $getHeaders = apache_request_headers();
        if(isset($getHeaders['lang'])){
            $lang = $getHeaders['lang'];
            if($lang == 1){
                $msg = 'City Found!';
                $notif = array('SuccessCode'=> 200 , 'message' => $msg, 'data' => $countries);
            }else{
                $msg = 'تم العثور على المدينة!';
                $len = sizeof($countries);
                // echo $len;
                $countries[0]['label'] = 'الإمارات العربية المتحدة';
                for ($i=1; $i < $len; $i++) { 
                    $countryCollectionFactory = $objectManager->get('Magento\Directory\Model\CountryFactory')->create()->loadByCode($countries[$i]['value']);
                    $countries[$i]['label'] = $countryCollectionFactory->getName('ar_SA');
                    if(array_key_exists('states', $countries[$i])){
                        $countries[$i]['states'][0]['label'] = 'من فضلك اختر منطقة، ولاية أو محافظة.';
                    //     $lens = sizeof($countries[$i]['states']);
                    //     for ($j=1; $j < $lens; $j++) {
                    //         $countryCollectionFactory = $objectManager->get('Magento\Directory\Model\CountryFactory')->create()->loadByCode($countries[$i]['states'][$j]['country_id']);
                    //         $countries[$i]['states'][$j]['label'] = $countryCollectionFactory->getName('ar_SA');
                    //         $countries[$i]['states'][$j]['title'] = $countryCollectionFactory->getName('ar_SA');
                    //     }
                        // print_r($countries[$i]['states']); exit();
                    }
                }
                // $countries['label'] = $names;
                // print_r($countries);


                $notif = array('SuccessCode'=> 200 , 'message' => $msg, 'data' => $countries);
            }
        }else{
            $msg = 'City Found!';   
            $notif = array('SuccessCode'=> 200 , 'message' => $msg, 'data' => $countries);
        } 
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
         die();
    }
}

