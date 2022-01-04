<?php


use Magento\Framework\AppInterface;

try {
    require __DIR__ . '/app/bootstrap.php';

} catch (\Exception $e) {
    echo 'Autoload error: ' . $e->getMessage();
    exit(1);
}
    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();

    // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

$countryCollectionFactory = $objectManager->get('Magento\Directory\Model\CountryFactory')->create()->loadByCode('AD');
// $data = $countryCollectionFactory->getCountryInfo('AD');
echo $countryCollectionFactory->getName('ar_SA');
// Get country collection
// $countryCollection = $countryCollectionFactory->create()->loadByStore();

// echo "<pre>";
// print_r($data);
// echo "</pre>";