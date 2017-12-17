<?php

class Finalwishlist_SharedWishlist_IndexController extends Mage_Core_Controller_Front_Action {



    public function AddAction()
    {

        $realAddToWishlist = str_replace(Mage::getBaseUrl(),'',$this->getRequest()->getPost('finalwishlist'));
        $wishlistSession = Mage::getSingleton('wishlist/session');
        $wishlistSession->setAddToFinalwishlist(true);
        $this->_redirect($realAddToWishlist);
        return true;
    }

    public function RemoveAction()
    {

        $realAddToWishlist = str_replace('/finalwishlist/','wishlist/',$this->getRequest()->getRequestUri());
        $wishlistSession = Mage::getSingleton('wishlist/session');
        $wishlistSession->setRemoveFromFinalwishlist(true);
        $this->_redirect($realAddToWishlist);
        return true;
    }

}