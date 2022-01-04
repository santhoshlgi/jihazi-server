<?php

use Magento\Framework\App\Bootstrap;
 
/**
 * If your external file is in root folder
 */
require __DIR__ . '/app/bootstrap.php';
 
/**
 * If your external file is NOT in root folder
 * Let's suppose, your file is inside a folder named 'xyz'
 *
 * And, let's suppose, your root directory path is
 * /var/www/html/magento2
 */
// $rootDirectoryPath = '/var/www/html/magento2';
// require $rootDirectoryPath . '/app/bootstrap.php';

$params = $_SERVER;
 
$bootstrap = Bootstrap::create(BP, $params);
 
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
 
// $om = \Magento\Framework\App\ObjectManager::getInstance();

$emailValidator = $objectManager->create('Magento\Framework\Validator\EmailAddress');

$len = 10570;
for ($i=1; $i < $len; $i++) { 
    $ch = curl_init();
    $surl = "https://jihazi.com/api/rest/json/order/show/type/erp/?consumerkey=3a9cb9272fd0f2371f1e5ff69c8afc55&limit=10&page=".$i;
    curl_setopt($ch, CURLOPT_URL,$surl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $response = curl_exec($ch);
    $sresult = json_decode($response,true);
    $lens = sizeof($sresult); 
    // print_r($sresult); exit();
    for ($j=0; $j < $lens; $j++) {
        if($sresult[$j]['customer_email'] == ""){
            // echo '1';
            break;
        } 
        if(!$emailValidator->isValid($sresult[$j]['customer_email'])) {
            break;
        }
        $ch1 = curl_init();
        $surl1 = "https://jihazi.com/api/rest/json/order/show/type/erp/id/".$sresult[$j]['orderNumber']."/?consumerkey=3a9cb9272fd0f2371f1e5ff69c8afc55";
        curl_setopt($ch1, CURLOPT_URL,$surl1);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $response1 = curl_exec($ch1);
        $result1 = json_decode($response1,true);
        $storeManager       = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
        $websiteId  = $storeManager->getWebsite()->getWebsiteId();
        // print_r($result1); exit();
        $CustomerModel = $objectManager->create('Magento\Customer\Model\Customer');
        $CustomerModel->setWebsiteId($websiteId); //Here 1 means Store ID**
        $CustomerModel->loadByEmail($result1[0]['customer_email']);
        $userid = $CustomerModel->getId();
        // print_r($customes);
        // exit();
        // ->create()->getCollection()->addAttributeToSelect("*")->addAttributeToFilter("email", array("eq" => $result1[$j]['customer_email']));
        if($userid){
            // break;
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
            $customerAddress = array();
            $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
            $customer = $customerFactory->load($userid);
            $customer = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface')->getById($userid);
            $addressRepository = $objectManager->create('Magento\Customer\Api\AddressRepositoryInterface');
            // $customer = $this->customerRepository->getById($customerId);
            // $billingAddressId = $customer->getDefaultBilling();
            // $shippingAddressId = $customer->getDefaultShipping();
            // foreach ($customerObj->getAddresses() as $address)
            // {
                // echo $result1[0]['billingAddress']['billingPhone'];
                $shiptel = $result1[0]['shippingAddress']['shippingZip'] ? $result1[0]['shippingAddress']['shippingZip'] : 1234567890;
                $billtel = $result1[0]['billingAddress']['billingPhone'] ? $result1[0]['billingAddress']['billingPhone'] : 123457890;
                $shippos = $result1[0]['shippingAddress']['shippingPhone'] ? $result1[0]['shippingAddress']['shippingPhone'] : 12345;
                $billpos = $result1[0]['billingAddress']['billingZip'] ? $result1[0]['billingAddress']['billingZip'] : 12345;
                $shipstre = $result1[0]['shippingAddress']['shippingAddress1'] ? $result1[0]['shippingAddress']['shippingAddress1'] : 'test';
                $billstre = $result1[0]['billingAddress']['billingAddress1'] ? $result1[0]['billingAddress']['billingAddress1'] : 'test';
                $shipcit = $result1[0]['shippingAddress']['shippingCity'] ? $result1[0]['shippingAddress']['shippingCity'] : 'test';
                $billcit = $result1[0]['billingAddress']['billingCity'] ? $result1[0]['billingAddress']['billingCity'] : 'test';
                $shipfname = $result1[0]['shippingAddress']['shippingFname'] ? $result1[0]['shippingAddress']['shippingFname'] : 'test';
                $shiplname = $result1[0]['shippingAddress']['shippingLname'] ? $result1[0]['shippingAddress']['shippingLname'] : 'test';
                $billfname = $result1[0]['billingAddress']['billingFirstName'] ? $result1[0]['billingAddress']['billingFirstName'] : 'test';
                $billlname = $result1[0]['billingAddress']['billingLastName'] ? $result1[0]['billingAddress']['billingLastName'] : 'test';
                $shippingaddressArray = [
                    'country_id' => 'SA',
                    'street' => [
                        $shipstre
                    ],
                    'telephone' => $shiptel,
                    'postcode' => $shippos,
                    'city' => $shipcit,
                    'firstname' => $shipfname,
                    'lastname' => $shiplname,
                    'email' => $result1[0]['customer_email']
                ];
                $billingaddressArray = [
                    'country_id' => 'SA',
                    'street' => [
                        $billstre
                    ],
                    'telephone' => $billtel,
                    'postcode' => $billpos,
                    'city' => $billcit,
                    'firstname' => $billfname,
                    'lastname' => $billlname,
                    'email' => $result1[0]['customer_email']
                ];
            // }

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
            $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
            $quoteFactory = $objectManager->get('\Magento\Quote\Model\QuoteFactory');

            $quote = $quoteFactory->create()->loadByCustomer($customerObj);
        
            $allItems = $quote->getAllItems();
            // $allItems = $checkoutSession->getQuote()->getAllVisibleItems();//returns all teh items in session
            foreach ($allItems as $item) {
                $itemId = $item->getItemId();//item id of particular item
                $quoteItem= $objectManager->create('Magento\Quote\Model\Quote\Item')->load($itemId);//load particular item which you want to delete by his item id
                $quoteItem->delete();//deletes the item
            }
            // echo $quote_id;
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $objectManager->get('Magento\Catalog\Model\Product');
            $productRepo = $objectManager->get('Magento\Catalog\Model\ProductRepository');

            $lks = sizeof($result1[0]['orderitems']);
            for ($l=0; $l < $lks; $l++) { 
                if($product->getIdBySku($result1[0]['orderitems'][$l]['ItemCode'])) {
                    // echo 'exit';  
                    $productData = [
                        'cart_item' => [
                            'quote_id' => $quote_id,
                            'sku' => $result1[0]['orderitems'][$l]['ItemCode'],
                            'qty' => $result1[0]['orderitems'][$l]['orderItemQuantity']
                        ]
                    ];  
                }else {
                    $productData = [
                        'cart_item' => [
                            'quote_id' => $quote_id,
                            'sku' => 'test-2',
                            'qty' => $result1[0]['orderitems'][$l]['orderItemQuantity']
                        ]
                    ];
                }
                $CartsUrl = $BaseUrl.'rest/V1/carts/mine/items';
                $ch = curl_init($CartsUrl);
                // $ch = curl_init("https://smvatech.in/ecommerce/rest/V1/carts/mine/items");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
                
                $result = curl_exec($ch);
                
                $result = json_decode($result , true);
                // print_r($result);
                
            }
        
            $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
        $quoteFactory = $objectManager->get('\Magento\Quote\Model\QuoteFactory');

        $quote = $quoteFactory->create()->loadByCustomer($customerObj);
    
        $items = $quote->getAllItems();
        $helperImport = $objectManager->get('\Magento\Catalog\Helper\Image');

        $cart_data = array();
        $total = 0;

        foreach($items as $item)
        {
            $price = $item['price'] * $item['qty'];
            $total = $total + $price;
        }
        // print_r($total);
        if($total >= 200){
            $addressData = [
                'addressInformation' => [
                    'shippingAddress' => $shippingaddressArray,
                    'billingAddress' => $billingaddressArray,
                    'shipping_method_code' => 'freeshipping',
                    'shipping_carrier_code' => 'freeshipping'
                ]
            ];
        }else {
            $addressData = [
                'addressInformation' => [
                    'shippingAddress' => $shippingaddressArray,
                    'billingAddress' => $billingaddressArray,
                    'shipping_method_code' => 'flatrate',
                    'shipping_carrier_code' => 'flatrate'
                ]
            ]; 
        }
            

            $url = $BaseUrl."rest/V1/carts/".$quote_id."/shipping-information";


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($addressData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

            $result = curl_exec($ch);
            
            $res = json_decode($result , true);
            // print_r($res); 
            $json = [
                'paymentMethod' =>[
                    'method' => 'cashondelivery'
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
                // $dat = $results." ";
                // if(is_array($results)) {
                // }else{
                    echo "order :- ";
                    print_r($results);
                    echo " ";
                // }
                // exit();
        }else {
            if($result1[0]['customer_email']){
                $names = explode(" ", $result1[0]['customer_name']);
                $storeManager       = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
                    $customerFactory    = $objectManager->create('Magento\Customer\Model\CustomerFactory');
            
                    $websiteId  = $storeManager->getWebsite()->getWebsiteId();
                    $customer   = $customerFactory->create();
                    $customer->setWebsiteId($websiteId);
                    $customer->setEmail($result1[0]['customer_email']);
                    $customer->setFirstname($names[0]);
                    $customer->setLastname($names[1]);
                    $customer->setPassword("password");
                    $customer->save();
                    // print_r($customer->getId());
                    // exit();
                    $userid = $customer->getId();
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
                    $customerAddress = array();
                    $customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
                    $customer = $customerFactory->load($userid);
                    $customer = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface')->getById($userid);
                    $addressRepository = $objectManager->create('Magento\Customer\Api\AddressRepositoryInterface');
                    // $customer = $this->customerRepository->getById($customerId);
                    // $billingAddressId = $customer->getDefaultBilling();
                    // $shippingAddressId = $customer->getDefaultShipping();
                    // foreach ($customerObj->getAddresses() as $address)
                    // {
                        // echo $result1[0]['shippingAddress']['shippingAddress1'];
                        $shiptel = $result1[0]['shippingAddress']['shippingZip'] ? $result1[0]['shippingAddress']['shippingZip'] : 1234567890;
                        $billtel = $result1[0]['billingAddress']['billingPhone'] ? $result1[0]['billingAddress']['billingPhone'] : 123457890;
                        $shippos = $result1[0]['shippingAddress']['shippingPhone'] ? $result1[0]['shippingAddress']['shippingPhone'] : 12345;
                        $billpos = $result1[0]['billingAddress']['billingZip'] ? $result1[0]['billingAddress']['billingZip'] : 12345;
                        $shipstre = $result1[0]['shippingAddress']['shippingAddress1'] ? $result1[0]['shippingAddress']['shippingAddress1'] : 'test';
                        $billstre = $result1[0]['billingAddress']['billingAddress1'] ? $result1[0]['billingAddress']['billingAddress1'] : 'test';
                        $shipcit = $result1[0]['shippingAddress']['shippingCity'] ? $result1[0]['shippingAddress']['shippingCity'] : 'test';
                        $billcit = $result1[0]['billingAddress']['billingCity'] ? $result1[0]['billingAddress']['billingCity'] : 'test';
                        $shipfname = $result1[0]['shippingAddress']['shippingFname'] ? $result1[0]['shippingAddress']['shippingFname'] : 'test';
                        $shiplname = $result1[0]['shippingAddress']['shippingLname'] ? $result1[0]['shippingAddress']['shippingLname'] : 'test';
                        $billfname = $result1[0]['billingAddress']['billingFirstName'] ? $result1[0]['billingAddress']['billingFirstName'] : 'test';
                        $billlname = $result1[0]['billingAddress']['billingLastName'] ? $result1[0]['billingAddress']['billingLastName'] : 'test';
                        $shippingaddressArray = [
                            'country_id' => 'SA',
                            'street' => [
                                $shipstre
                            ],
                            'telephone' => $shiptel,
                            'postcode' => $shippos,
                            'city' => $shipcit,
                            'firstname' => $shipfname,
                            'lastname' => $shiplname,
                            'email' => $result1[0]['customer_email']
                        ];
                        $billingaddressArray = [
                            'country_id' => 'SA',
                            'street' => [
                                $billstre
                            ],
                            'telephone' => $billtel,
                            'postcode' => $billpos,
                            'city' => $billcit,
                            'firstname' => $billfname,
                            'lastname' => $billlname,
                            'email' => $result1[0]['customer_email']
                        ];
                    // }

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
                    $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
                    $quoteFactory = $objectManager->get('\Magento\Quote\Model\QuoteFactory');

                    $quote = $quoteFactory->create()->loadByCustomer($customerObj);
                
                    $allItems = $quote->getAllItems();
                    // $allItems = $checkoutSession->getQuote()->getAllVisibleItems();//returns all teh items in session
                    foreach ($allItems as $item) {
                        $itemId = $item->getItemId();//item id of particular item
                        $quoteItem= $objectManager->create('Magento\Quote\Model\Quote\Item')->load($itemId);//load particular item which you want to delete by his item id
                        $quoteItem->delete();//deletes the item
                    }
                    // echo $quote_id;
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $product = $objectManager->get('Magento\Catalog\Model\Product');
                    $productRepo = $objectManager->get('Magento\Catalog\Model\ProductRepository');

                    $lks = sizeof($result1[0]['orderitems']);
                    for ($l=0; $l < $lks; $l++) { 
                        if($product->getIdBySku($result1[0]['orderitems'][$l]['ItemCode'])) {
                            // echo 'exit';  
                            $productData = [
                                'cart_item' => [
                                    'quote_id' => $quote_id,
                                    'sku' => $result1[0]['orderitems'][$l]['ItemCode'],
                                    'qty' => $result1[0]['orderitems'][$l]['orderItemQuantity']
                                ]
                            ];  
                        }else {
                            $productData = [
                                'cart_item' => [
                                    'quote_id' => $quote_id,
                                    'sku' => 'test-2',
                                    'qty' => $result1[0]['orderitems'][$l]['orderItemQuantity']
                                ]
                            ];
                        }
                        $CartsUrl = $BaseUrl.'rest/V1/carts/mine/items';
                        $ch = curl_init($CartsUrl);
                        // $ch = curl_init("https://smvatech.in/ecommerce/rest/V1/carts/mine/items");
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($productData));
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
                        
                        $result = curl_exec($ch);
                        
                        $result = json_decode($result , true);
                        // print_r($result);
                        
                    }
                
                    $customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($userid);
                $quoteFactory = $objectManager->get('\Magento\Quote\Model\QuoteFactory');

                $quote = $quoteFactory->create()->loadByCustomer($customerObj);
            
                $items = $quote->getAllItems();
                $helperImport = $objectManager->get('\Magento\Catalog\Helper\Image');

                $cart_data = array();
                $total = 0;

                foreach($items as $item)
                {
                    $price = $item['price'] * $item['qty'];
                    $total = $total + $price;
                }
                // print_r($total);
                if($total >= 200){
                    $addressData = [
                        'addressInformation' => [
                            'shippingAddress' => $shippingaddressArray,
                            'billingAddress' => $billingaddressArray,
                            'shipping_method_code' => 'freeshipping',
                            'shipping_carrier_code' => 'freeshipping'
                        ]
                    ];
                }else {
                    $addressData = [
                        'addressInformation' => [
                            'shippingAddress' => $shippingaddressArray,
                            'billingAddress' => $billingaddressArray,
                            'shipping_method_code' => 'flatrate',
                            'shipping_carrier_code' => 'flatrate'
                        ]
                    ]; 
                }
                    

                    $url = $BaseUrl."rest/V1/carts/".$quote_id."/shipping-information";


                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($addressData));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

                    $result = curl_exec($ch);
                    
                    $res = json_decode($result , true);
                    // print_r($res); 
                    $json = [
                        'paymentMethod' =>[
                            'method' => 'cashondelivery'
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
                        // if(is_array($results)) {
                        // }else{
                            echo "order :- ";
                            print_r($results);
                            echo " ";
                        // }
            }
            // print_r(" ".$sresult[$j]['customer_email']." ");
            // print_r($sresult[$j]['orderNumber']." "); 
            // break;
            // $names = explode(" ", $result1[0]['customer_name']);
            // echo $pieces[0]; // piece1
            // echo $pieces[1];
                    
        } 
        // if($j == 3){
        //     exit();
        // }
        // exit();
    }
    // exit();
}
