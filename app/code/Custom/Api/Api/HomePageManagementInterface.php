<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Custom\Api\Api;

interface HomePageManagementInterface
{

    /**
     * POST for Home Page api
     * @param string $identifier
     * @return string
     */
    
    public function homePageBanner($identifier);

    /**
     * POST for Home Page api
     * @param string $name
     * @return string
     */

    public function homePageSearch($name);

    /**
     * POST for Home Page api
     * @return string
     */

    public function homePageCategories();

    /**
     * POST for Home Page api
     *  @param string $token
     * @return string
     */

    public function homePageTopCategories($token);

    /**
     * POST for Home Page api
     * @return string
     */
    public function homePageProductBrand();
    /**
     * POST for Home Page api
     * @return string
     */
    public function homePageProductBrandOptions();


    /*changes start*/
    
    /**
     * POST for productList api
     *  @param string $token
     *  @param string $categoryId
     *  @param string $categoryName
     *  @param string $pageNo
     *  @param string $pageSize
     *  @param string $key
     *  @param string $value
     *  @param string $skey
     *  @param string $sorder
     * @return string
     */
    public function productList($token,$categoryId,$subcategoryId,$pageNo,$pageSize,$key,$value,$skey,$sorder);

    /**
     * POST for categorySearch api
     *  @param string $token
     *  @param string $categoryName
     *  @param string $pageNo
     *  @param string $pageSize
     *  @param string $key
     *  @param string $value
     *  @param string $skey
     *  @param string $sorder
     * @return string
     */

    public function categorySearch($token,$categoryName,$pageNo,$pageSize,$key,$value,$skey,$sorder);


    /**
     * POST for getSubCategoryList api
     *  @param string $token
     *  @param string $categoryId
     * @return string
     */
    public function getSubCategoryList($token,$categoryId);

    /*changes end*/
    
}

