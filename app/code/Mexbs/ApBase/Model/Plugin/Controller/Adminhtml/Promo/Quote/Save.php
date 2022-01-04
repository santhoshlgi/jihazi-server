<?php
namespace Mexbs\ApBase\Model\Plugin\Controller\Adminhtml\Promo\Quote;

class Save
{
    protected $apHelper;

    public function __construct(
        \Mexbs\ApBase\Helper\Data $apHelper
    ) {
        $this->apHelper = $apHelper;
    }

    public function beforeExecute(\Magento\SalesRule\Controller\Adminhtml\Promo\Quote\Save $subject){
        $requestData = $subject->getRequest()->getPostValue();

        if(isset($requestData['rule']['action_details'])){
            $requestData['action_details'] = $requestData['rule']['action_details'];
        }

        if(isset($requestData['simple_action'])
            && $this->apHelper->isSimpleActionAp($requestData['simple_action'])){
            $requestData['rule']['actions'] = [];
            $requestData['actions'] = [];
            $requestData['actions_serialized'] = "";
        }

        $imageKeys = ['popup_on_first_visit_image', 'banner_in_promo_trigger_products_image', 'badge_in_promo_trigger_products_image', 'badge_in_promo_trigger_products_category_image',
            'banner_in_get_products_image', 'badge_in_get_products_image', 'badge_in_get_products_category_image'];

        foreach($imageKeys as $imageKey){
            if(empty($requestData[$imageKey])){
                unset($requestData[$imageKey]);
            }

            if(!isset($requestData[$imageKey])){
                $requestData[$imageKey] = [];
                $requestData[$imageKey]['delete'] = true;
            }

            if(isset($requestData[$imageKey])
                && is_array($requestData[$imageKey])){

                if(!empty($requestData[$imageKey]['delete'])){
                    $requestData[$imageKey] = '';
                }

                if(isset($requestData[$imageKey][0]['name'])){
                    $uploadedNewImage = false;
                    if(isset($requestData[$imageKey][0]['tmp_name'])){
                        $uploadedNewImage = true;
                    }
                    $requestData[$imageKey] = $requestData[$imageKey][0]['name'];
                    if($uploadedNewImage){
                        $requestData[$imageKey.'_uploaded'] = true;
                    }
                }
            }
        }

        $subject->getRequest()->setPostValue($requestData);
    }
}