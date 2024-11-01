<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 07/11/2017
 * Time: 11:33
 */

/**
 * Class FavizoneApi
 */
class FavizoneApi
{
    /**
     * FAVIZONE_HOST
     */
    const FAVIZONE_HOST = "https://api.favizone.com";
    
    /**
     * FAVIZONE_PLUGIN_NAME
     */
    const FAVIZONE_PLUGIN_NAME = "woo-favizone-extension";

    /**
     * FAVIZONE_API_VERSION
     */
    const FAVIZONE_API_VERSION = "v2";

    /**
     * Get Favizone Host
     * @return string
     */
    public function getFavizoneHost()
    {
        return self::FAVIZONE_HOST;
    }

    /**
     * Get Favizone Plugin Name
     * @return string
     */
    public function getFavizonePluginName()
    {
        return self::FAVIZONE_PLUGIN_NAME;
    }

    /**
     * Get Favizone Api Vesion
     * @return string
     */
    public function getFavizoneApiVesion()
    {
        return self::FAVIZONE_API_VERSION;
    }

    /**
     * Get Add Account Favizone Url
     * @return string
     */
    public function getAddAccountFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/user/add-account";
    }

    /**
     * Get Check Init Favizone Url
     * @return string
     */
    public function getCheckInitFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/product/check-init";
    }

    /**
     * Get Init Order Favizone Url
     * @return string
     */
    public function getInitOrderFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/order/init";
    }

    /**
     * Get Init Profile Favizone Url
     * @return string
     */
    public function getInitProfileFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/profile/init";
    }

    /**
     * Get Add Category Favizone Url
     * Retruns Favizone's Api path for adding category .
     * @return string
     */
    public function getAddCategoryFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/category/add";
    }

    /**
     * Get Update Category Favizone Url
     * Returns Favizone's Api path for updating category data .
     * @return string
     */
    public function getUpdateCategoryFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/category/update";
    }

    /**
     * Get Delete Category Favizone Url
     * Retruns Favizone's Api path for deleting category .
     * @return string
     */
    public function getDeleteCategoryFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/category/delete";
    }

    /**
     * Get Add Product Favizone Url
     * Retruns Favizone's Api path for adding product .
     * @return string
     */
    public function getAddProductFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/product/add";
    }

    /**
     * Get Update Product Favizone Url
     * Retruns Favizone's Api path for updating product .
     * @return string
     */
    public function getUpdateProductFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/product/update";
    }

    /**
     * Get Delete Product Favizone Url
     * Returns Favizone's Api path for deleting a product .
     * @return string
     */
    public function getDeleteProductFavizoneUrl()
    {
        return $this->getFavizoneApiVesion() . "/product/delete";
    }

}