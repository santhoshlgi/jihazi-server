<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Model;

class OrderManagement implements \Custom\Api\Api\OrderManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function myOrdersList($userid){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
        $customerId = $customerObj->getId();
        $data = array();
        if(isset($customerId)){
            $order_collection = $objectManager->create('\Magento\Sales\Model\ResourceModel\Order\CollectionFactory')->create()->addFieldToFilter('customer_id', [
                'eq' => $userid
            ]);
            // ->get($userid); 
            if(isset($order_collection)){
                foreach ($order_collection as $order) {
                    // print_r($order->getData());
                    $orders = $objectManager->create('Magento\Sales\Api\Data\OrderInterface')->load($order->getId());
                    $orderIcount = count($order->getAllItems());
                    $status = $order->getStatus();
                    $getHeaders = apache_request_headers();
                    if(isset($getHeaders['lang'])){
                        $lang = $getHeaders['lang'];
                        if($lang == 2){
                            if($status == 'pending'){
                                $status ='في إنتظار المُراجعة';
                                $data[] = array('orderId' => $orders->getIncrementId() ,'Orderid' => $orders->getId(), 'deliveryDate' => $orders->getCreatedAt() , 'noOfItems' => $orderIcount , 'Status' => $status);
                            }elseif($status == 'Complete'){
                                $status ='تكملة';
                                $data[] = array('orderId' => $orders->getIncrementId() ,'Orderid' => $orders->getId(), 'deliveryDate' => $orders->getCreatedAt() , 'noOfItems' => $orderIcount , 'Status' => $status);
                            }elseif($status == 'canceled'){
                                $status ='ألغيت';
                                $data[] = array('orderId' => $orders->getIncrementId() ,'Orderid' => $orders->getId(), 'deliveryDate' => $orders->getCreatedAt() , 'noOfItems' => $orderIcount , 'Status' => $status);
                            }else {
                                $data[] = array('orderId' => $orders->getIncrementId() ,'Orderid' => $orders->getId(), 'deliveryDate' => $orders->getCreatedAt() , 'noOfItems' => $orderIcount , 'Status' => $status);
                            }
                        }else{
                            $data[] = array('orderId' => $orders->getIncrementId() ,'Orderid' => $orders->getId(), 'deliveryDate' => $orders->getCreatedAt() , 'noOfItems' => $orderIcount , 'Status' => $status);
                        }
                    }else{
                        $data[] = array('orderId' => $orders->getIncrementId() ,'Orderid' => $orders->getId(), 'deliveryDate' => $orders->getCreatedAt() , 'noOfItems' => $orderIcount , 'Status' => $status);
                    }
                }
                
                if(!empty($data)){
                    $getHeaders = apache_request_headers();
                    if(isset($getHeaders['lang'])){
                        $lang = $getHeaders['lang'];
                        if($lang == 1){
                            $msg = 'Order Found!';
                        }else{
                            $msg = 'تم العثور على الطلب';
                        }
                    }else{
                        $msg = 'Order Found!';   
                    }
                    $notif = array('SuccessCode'=> 200 ,'message' => $msg, 'data' => $data);
                }else{
                    $getHeaders = apache_request_headers();
                    if(isset($getHeaders['lang'])){
                        $lang = $getHeaders['lang'];
                        if($lang == 1){
                            $msg = 'No Order Found!';
                        }else{
                            $msg = 'لم تم العثور على الطلب';
                        }
                    }else{
                        $msg = 'No Order Found!';   
                    }
                    $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());  
                }
                
                header("Content-Type: application/json; charset=utf-8");
                $ns = json_encode($notif);
                print_r($ns,false);
                die();
            }else{
                $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'No Order Found!';
                    }else{
                        $msg = 'لم تم العثور على الطلب';
                    }
                }else{
                    $msg = 'No Order Found!';   
                }
                $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());
                header("Content-Type: application/json; charset=utf-8");
                $ns = json_encode($notif);
                print_r($ns,false);
                die();
            }
        }else{
            $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'User Not Found!';
                    }else{
                        $msg = 'لم يتم العثور على المستخدم';
                    }
                }else{
                    $msg = 'User Not Found!';   
                }
            $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();
        }

    }
    public function detailsOrders($userid,$orderid){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
        $customerId = $customerObj->getId();
        $check = 0;
        if(isset($customerId)){
            $order_collection = $objectManager->create('\Magento\Sales\Model\ResourceModel\Order\CollectionFactory')->create()->addFieldToFilter('customer_id', [
                'eq' => $userid
            ]);
            if(isset($order_collection)){
                foreach ($order_collection as $order) {
                    if($order->getId() == $orderid){
                        $check = 1;
                    }
                }
                if($check == 1){
                    $order = $objectManager->create('Magento\Sales\Api\Data\OrderInterface')->load($orderid);
                    if(isset($order)){
                        $orderItems = $order->getAllVisibleItems();
                        foreach ($orderItems as $item) {
                            $items[] = $item->toArray();
                        }
                        // echo $sku; die();

                        $payment = $order->getPayment();
                        $method = $payment->getMethodInstance();
                        $methodTitle = $method->getTitle();
                        $getHeaders = apache_request_headers();
                        if(isset($getHeaders['lang'])){
                            $lang = $getHeaders['lang'];
                            if($lang == 2 && $methodTitle == "Credit / Debit Card"){
                                $methodTitle = 'بطاقة مدى / بطاقة الائتمان';
                            }
                        }
                        $data = array('orderId' => $order->getIncrementId() ,'Orderid' => $order->getId(),'orderPlaced' => true,'comfirmed' => true,'orderShipped' => false,
                        'shippingDate' => $order->getCreatedAt() ,'outForDelivery' => false,'ExpectedDeliveryDate'=> $order->getCreatedAt(),
                        'Deliveried' => false,'DeliveryDate' => $order->getCreatedAt(), 'paymentMethod' => $methodTitle,
                        'ShippingAddress' => $order->getShippingAddress()->toArray(),'BillingAddress' => $order->getBillingAddress()->toArray(),
                        'products' => $items, 'priceDetails' => 
                        array('tax' => $order->getTaxAmount() ,'deliveryCharge' => $order->getShippingAmount() , 'discount' => $order->getDiscountAmount(), 'total' => $order->getGrandTotal()));
                        $getHeaders = apache_request_headers();
                        if(isset($getHeaders['lang'])){
                            $lang = $getHeaders['lang'];
                            if($lang == 1){
                                $msg = 'Order Found!';
                            }else{
                                $msg = 'تم العثور على الطلب';
                            }
                        }else{
                            $msg = 'Order Found!';   
                        }
                        $notif = array('SuccessCode'=> 200 ,'message' => $msg, 'data' => $data);
                        header("Content-Type: application/json; charset=utf-8");
                        $ns = json_encode($notif);
                        print_r($ns,false);
                        die();
                    }else{
                        $getHeaders = apache_request_headers();
                        if(isset($getHeaders['lang'])){
                            $lang = $getHeaders['lang'];
                            if($lang == 1){
                                $msg = 'No Order Found!';
                            }else{
                                $msg = 'لم تم العثور على الطلب';
                            }
                        }else{
                            $msg = 'No Order Found!';   
                        }
                        $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());
                        header("Content-Type: application/json; charset=utf-8");
                        $ns = json_encode($notif);
                        print_r($ns,false);
                        die();
                    }
                }else{
                    $getHeaders = apache_request_headers();
                        if(isset($getHeaders['lang'])){
                            $lang = $getHeaders['lang'];
                            if($lang == 1){
                                $msg = 'No Order Found!';
                            }else{
                                $msg = 'لم تم العثور على الطلب';
                            }
                        }else{
                            $msg = 'No Order Found!';   
                        }
                        $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());
                    header("Content-Type: application/json; charset=utf-8");
                    $ns = json_encode($notif);
                    print_r($ns,false);
                    die();  
                }
            }else{
                $getHeaders = apache_request_headers();
                        if(isset($getHeaders['lang'])){
                            $lang = $getHeaders['lang'];
                            if($lang == 1){
                                $msg = 'No Order Found!';
                            }else{
                                $msg = 'لم تم العثور على الطلب';
                            }
                        }else{
                            $msg = 'No Order Found!';   
                        }
                        $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());
                header("Content-Type: application/json; charset=utf-8");
                $ns = json_encode($notif);
                print_r($ns,false);
                die();    
            }
            
        }else{
            $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'User Not Found!';
                    }else{
                        $msg = 'لم يتم العثور على المستخدم';
                    }
                }else{
                    $msg = 'User Not Found!';   
                }
            $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();
        }
    }
    public function applycouponOrders($userid,$couponCode){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $Adminusername = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/general/api_admin');
        $Adminpassword = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/general/api_password');
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $BaseUrl = $storeManager->getStore()->getBaseUrl();

        $userData = array("username" => $Adminusername, "password" => $Adminpassword);
        $adminUrl = $BaseUrl.'rest/V1/integration/admin/token';
        $ch = curl_init($adminUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        $token = curl_exec($ch);
        // print_r($token);
        $customerData = [
            'customer_id' => $userid
        ];
        $CartsUrl = $BaseUrl.'rest/V1/carts/mine';
        $ch = curl_init($CartsUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customerData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
        
        $result = curl_exec($ch);
        
        $quote_id = json_decode($result);
        $url = $BaseUrl."rest/default/V1/carts/".$quote_id."/coupons/".$couponCode;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
            $result = curl_exec($ch);
            $result = json_decode($result ,true);
            curl_close($ch);
        if($result == 1){
            $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'Coupon code applied successfully!';
                    }else{
                        $msg = 'تم تطبيق الخصم بنجاح';
                    }
                }else{
                    $msg = 'Coupon code applied successfully';   
                }
            $notif = array('SuccessCode'=> 200 ,'message' => $msg, 'data' => array());
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();  
        }else{
            $getHeaders = apache_request_headers();
                if(isset($getHeaders['lang'])){
                    $lang = $getHeaders['lang'];
                    if($lang == 1){
                        $msg = 'Coupon code not applied';
                    }else{
                        $msg = 'الخصم غير مطبق';
                    }
                }else{
                    $msg = 'Coupon code not applied';   
                }
            $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();  
        }

        
    }

    public function removecouponOrders($userid){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $Adminusername = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/general/api_admin');
        $Adminpassword = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/general/api_password');
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $BaseUrl = $storeManager->getStore()->getBaseUrl();

        $userData = array("username" => $Adminusername, "password" => $Adminpassword);
        $adminUrl = $BaseUrl.'rest/V1/integration/admin/token';
        $ch = curl_init($adminUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        $token = curl_exec($ch);
        // print_r($token);
        $customerData = [
            'customer_id' => $userid
        ];
        $CartsUrl = $BaseUrl.'rest/V1/carts/mine';
        $ch = curl_init($CartsUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customerData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
        
        $result = curl_exec($ch);
        
        $quote_id = json_decode($result);
        $url = $BaseUrl."rest/default/V1/carts/".$quote_id."/coupons";
            // echo $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
        $result = curl_exec($ch);
        $result = json_decode($result,true);
        curl_close($ch);
        if($result == 1){
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Coupon code removed';
                }else{
                    $msg = 'تم ازالة الخصم';
                }
            }else{
                $msg = 'Coupon code removed';   
            }
            $notif = array('SuccessCode'=> 200 ,'message' => $msg, 'data' => array());
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Coupon code not removed';
                }else{
                    $msg = 'لم يتم ازالة الخصم';
                }
            }else{
                $msg = 'Coupon code not removed';   
            }
            $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());
            header("Content-Type: application/json; charset=utf-8");
            $ns = json_encode($notif);
            print_r($ns,false);
            die();
        }    
    }
    public function updatestatusOrders($orderid,$paymentStatus){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $Adminusername = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/general/api_admin');
        $Adminpassword = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/general/api_password');
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $BaseUrl = $storeManager->getStore()->getBaseUrl();

        $userData = array("username" => $Adminusername, "password" => $Adminpassword);
        $adminUrl = $BaseUrl.'rest/V1/integration/admin/token';
        $ch = curl_init($adminUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        $token = curl_exec($ch);

        $url = $BaseUrl."rest/V1/orders";
        
        if($paymentStatus == 'pending'){
            $state = 'new';
        }else{
            $state = $paymentStatus;
        }

        $orderData = [
            'entity' => array(
                'entity_id' => $orderid,
                'state' => $state,
                'status' => $paymentStatus  
            )
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
        
        $result = curl_exec($ch);
        
        $res = json_decode($result,true);
        // print_r($res); 
        if($res['status'] == $paymentStatus){
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Order status updated';
                }else{
                    $msg = 'تم تحديث حالة الطلب';
                }
            }else{
                $msg = 'Order status updated';   
            }
            $notif = array('SuccessCode'=> 200 ,'message' => $msg, 'data' => array());
        }else{
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Order status not updated';
                }else{
                    $msg = 'لم يتم تحديث حالة الطلب';
                }
            }else{
                $msg = 'Order status not updated';   
            }
            $notif = array('SuccessCode'=> 400 ,'message' => $msg, 'data' => array());
        }
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die(); 
    }
    public function createOrders($shippingMethod,$paymentMethod,$userid){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
        $customerAddress = array();
        $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
        $customer = $customerFactory->load($userid);
        $customer = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface')->getById($userid);
        $addressRepository = $objectManager->create('Magento\Customer\Api\AddressRepositoryInterface');
        // $customer = $this->customerRepository->getById($customerId);
        $billingAddressId = $customer->getDefaultBilling();
        $shippingAddressId = $customer->getDefaultShipping();
        foreach ($customerObj->getAddresses() as $address)
        {
            $addressArray = [
                'region' => $address->getRegion(),
                'region_id' => $address->getRegionId(),
                'country_id' => $address->getCountryId(),
                'street' => $address->getStreet(),
                'company' => $address->getCompany(),
                'telephone' => $address->getTelephone(),
                'postcode' => $address->getPostcode(),
                'city' => $address->getCity(),
                'firstname' => $address->getFirstname(),
                'lastname' => $address->getLastname(),
                'email' => $customer->getEmail(),
                'sameAsBilling' => 1
            ];
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $Adminusername = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/general/api_admin');
        $Adminpassword = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('api/general/api_password');
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $BaseUrl = $storeManager->getStore()->getBaseUrl();

        $userData = array("username" => $Adminusername, "password" => $Adminpassword);
        $adminUrl = $BaseUrl.'rest/V1/integration/admin/token';
        $ch = curl_init($adminUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));

        $token = curl_exec($ch);
        $customerData = [
            'customer_id' => $userid
        ];

        $CartsUrl = $BaseUrl.'rest/V1/carts/mine';
        $ch = curl_init($CartsUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customerData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
        
        $result = curl_exec($ch);
        
        $quote_id = json_decode($result);

        $url = $BaseUrl."rest/V1/carts/".$quote_id."/shipping-information";

        $addressData = [
            'addressInformation' => [
                'shippingAddress' => $addressArray,
                'billingAddress' => $addressArray,
                'shipping_method_code' => $shippingMethod,
                'shipping_carrier_code' => $shippingMethod
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($addressData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

        $result = curl_exec($ch);
        
        $res = json_decode($result , true);
        $json = [
            'paymentMethod' =>[
                'method' => $paymentMethod
            ]
        ];
        $Url = $BaseUrl."rest/default/V1/carts/".$quote_id."/order";
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$Url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
            $result = curl_exec($ch);
            $results = json_decode($result ,true);
            curl_close($ch);
            $getHeaders = apache_request_headers();
            if(isset($getHeaders['lang'])){
                $lang = $getHeaders['lang'];
                if($lang == 1){
                    $msg = 'Order placed successfully';
                }else{
                    $msg = 'تم الطلب بنجاح';
                }
            }else{
                $msg = 'Order placed successfully';   
            }
        $notif = array('SuccessCode'=> 200 ,'message' => $msg, 'data' => array($results));
        header("Content-Type: application/json; charset=utf-8");
        $ns = json_encode($notif);
        print_r($ns,false);
        die();
    }
}