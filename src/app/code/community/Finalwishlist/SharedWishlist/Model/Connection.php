<?php

/**
 * Class Finalwishlist_SharedWishlist_Model_Connection
 */
class Finalwishlist_SharedWishlist_Model_Connection extends Mage_Core_Model_Abstract
{
    protected $_client;
    private $_credentials;

    private $_currentToken;

    protected function _construct()
    {
        try {
            if(!$this->getHelper()){
                $this->setHelper(Mage::helper('finalwishlist_sharedwishlist'));
            }

            $this->_credentials = array();
            $this->_credentials['api_id'] = Mage::getStoreConfig('finalwishlist_sharedwishlist/credentials/api_id');
            $this->_credentials['api_key'] = Mage::getStoreConfig('finalwishlist_sharedwishlist/credentials/api_key');
            if(!($this->_client instanceof Varien_Http_Client)){
                $this->_client = new Varien_Http_Client();
                $this->_client->setMethod('POST')
                    ->setConfig(array(
                        'maxredirects' => 0,
                        'timeout' => 30,
                    ));
            }
            if (!($this->_currentToken = Mage::getSingleton('finalwishlist_sharedwishlist/session')->getToken())) {
                    $this->_login();

            }
        } catch (Mage_Core_Exception $e) {
            $this->log(__METHOD__, $e->getMessage(), Zend_Log::ERR);
        }

    }

    /**
     * @param $data
     * @return bool|mixed
     */
    private function _login()
    {
        $response = $this->_callAPI($this->getHelper()->getFinalWishlistSite() . '/api/login', $this->_credentials);
        if($response->isSuccessful()){
            Mage::getSingleton('finalwishlist_sharedwishlist/session')->setToken(json_decode($response->getBody())->token);
            $this->_currentToken = Mage::getSingleton('finalwishlist_sharedwishlist/session')->getToken();
            $this->_client->setHeaders('Authorization', 'Bearer '.$this->_currentToken);
            return true;
        }
        throw new Mage_Core_Exception('Wrong API credentials');
    }

    /**
     * @param mixed|false $data data to send
     * @return bool|mixed
     */
    private function _callAPI($url, $data = false)
    {
        if (!isset($this->_client)) {
            return false;

        }
            $client = $this->_client;
        if(!$client->getUri() ||
            strpos($url,$client->getUri()->getHost().$client->getUri()->getPath()) == false ){
            $client->setUri($url);
        }
        $client->setRawData(json_encode($data), "application/json;charset=UTF-8");
        /**
         * @var $response Zend_Http_Response
         */
        $response = $client->request();
        return $response;
    }

    protected function log($method, $message, $level = Zend_Log::INFO)
    {
        $this->getHelper()->log($method, $message, $level);
    }

    public function addItem($data){

       $response = $this->_callAPI($this->getHelper()->getFinalWishlistSite().'/api/wishlist/add',$data , 'additem');
        if($response->isSuccessful()){
            return $response;
        }
        $this->_login();
        $response = $this->_callAPI($this->getHelper()->getFinalWishlistSite().'/api/wishlist/add',$data, 'additem');
         if($response->isSuccessful()){
             return $response;
         }
        $this->log(__METHOD__, 'Error in remove Item'.json_decode($response->getBody()), Zend_Log::ERR);
        return false;
    }
    public function removeItem($data){
       $response = $this->_callAPI($this->getHelper()->getFinalWishlistSite().'/api/wishlist/remove',$data , 'removeitem');
        if($response->isSuccessful()){
            return $response;
        }
        $this->_login();
        $response = $this->_callAPI($this->getHelper()->getFinalWishlistSite().'/api/wishlist/remove',$data, 'removeitem');
         if($response->isSuccessful()){
             return $response;
         }
        $this->log(__METHOD__, 'Error removing Item'.json_decode($response->getBody()), Zend_Log::ERR);
        return false;
    }

    public function boughtItem($data){
       $response = $this->_callAPI($this->getHelper()->getFinalWishlistSite().'/api/wishlist/bought',$data , 'boughtitem');
        if($response->isSuccessful()){
            return $response;
        }
        $this->_login();
        $response = $this->_callAPI($this->getHelper()->getFinalWishlistSite().'/api/wishlist/bought',$data, 'boughtitem');
         if($response->isSuccessful()){
             return $response;
         }
        $this->log(__METHOD__, 'Error send  BoughtItem'.json_decode($response->getBody()), Zend_Log::ERR);
        return false;
    }


}