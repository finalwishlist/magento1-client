<?php

class Finalwishlist_SharedWishlist_Model_Session extends Mage_Core_Model_Session_Abstract
{
    private $_token = false;

    public function __construct()
    {
        $this->init('finalwishlist');
    }

    protected function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }

    protected function getToken()
    {
        return $this->_token;
    }
}