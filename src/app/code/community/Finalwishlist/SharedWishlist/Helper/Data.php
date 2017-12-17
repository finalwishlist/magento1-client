<?php

/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10/12/16
 * Time: 14.52
 */
class Finalwishlist_SharedWishlist_Helper_Data extends Mage_Core_Helper_Abstract
{

    const SANDBOX_WISHLIST_URL = 'http://sandbox.finalwishlist.com';
    const PRODUCTION_WISHLIST_URL = 'https://finalwishlist.com/';
    protected $logFile = "sharedwishlist.log";


    public function log($method, $message, $level = LOG_INFO)
    {
        Mage::log($method . " | " . $message, $level, $this->logFile);
    }


    public function getFinalWishlistSite(){
        if (Mage::getStoreConfig('finalwishlist_sharedwishlist/credentials/sandbox_flag')) {
            return self::SANDBOX_WISHLIST_URL;
        } else {
            return self::PRODUCTION_WISHLIST_URL;
        }
    }
    public function getControllerFileName($realModule, $controller)
    {
        $parts = explode('_', $realModule);
        $realModule = implode('_', array_splice($parts, 0, 2));
        $file = Mage::getModuleDir('controllers', $realModule);
        if (count($parts)) {
            $file .= DS . implode(DS, $parts);
        }
        $file .= DS . uc_words($controller, DS) . 'Controller.php';
        return $file;
    }

    public function getControllerClassName($realModule, $controller)
    {
        $class = $realModule . '_' . uc_words($controller) . 'Controller';
        return $class;
    }

    public function includeControllerClass($realModule, $controller)
    {
        $controllerFileName = $this->getControllerFileName($realModule, $controller);
        $controllerClassName = $this->getControllerClassName($realModule, $controller);
        if (!class_exists($controllerClassName, false)) {
            if (!file_exists($controllerFileName)) {
                return false;
            }
            include $controllerFileName;

            if (!class_exists($controllerClassName, false)) {
                throw Mage::exception('Mage_Core', Mage::helper('core')->__('Controller file was loaded but class does not exist'));
            }
        }
        return true;
    }


}