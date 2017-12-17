<?php

class Finalwishlist_SharedWishlist_Model_Observer extends Mage_Core_Model_Observer
{


    public function addToFinalWishlist($observer)
    {

        $wishlistSession = Mage::getSingleton('wishlist/session');
        if ($wishlistSession->getAddToFinalwishlist()) {

            $wishlistSession->setAddToFinalwishlist(null);
            $items = $observer->getItems();
            if (!empty($items)) {
                /**
                 * @var $lastItem Mage_Wishlist_Model_Item
                 */
                $lastItem = array_pop($items);
                if (!$lastItem->getHasError()) {

                    $connection = Mage::getSingleton('finalwishlist_sharedwishlist/connection');
                    $data = array();

                    $customer = Mage::getModel('customer/session')->getCustomer();
                    $data['customerid'] = $customer->getId();
                    $data['email'] = $customer->getEmail();
                    $data['customer_name'] = $customer->getFirstName()." ".$customer->getLastName();
                    $data['item_id'] = $lastItem->getId();
                    $data['product_name'] = $lastItem->getProduct()->getName();
                    $data['product_link'] = $lastItem->getProduct()->getProductUrl();
                    $data['image_link'] = Mage::getUrl('media') . 'catalog/product' .$lastItem->getProduct()->getThumbnail();

                    if ($response = $connection->addItem($data)) {
                        $responseDecoded = json_decode($response->getBody());
                        $hash = base64_encode(serialize(array('user_id' => $responseDecoded->success)));
                        $referer = Mage::helper('finalwishlist_sharedwishlist')->getFinalWishlistSite() . DS . 'wishlist' . DS . $hash;
                        $message = Mage::helper('finalwishlist_sharedwishlist')->__('%1$s has been added to your Finalwishlist.com. Click to see it <a href="%2$s">here</a>',
                            $lastItem->getProduct()->getName(), Mage::helper('core')->escapeUrl($referer));
                        Mage::getSingleton('customer/session')->addSuccess($message);
                    } else {
                        Mage::getSingleton('customer/session')->addError(Mage::helper('finalwishlist_sharedwishlist')->__('An error occurred while adding item to Finalwishlist.com'));
                    }
                }
            }
        }
    }

    public function removeFromFinalWishlist($observer)
    {

        $wishlistSession = Mage::getSingleton('wishlist/session');
        if ($wishlistSession->setRemoveFromFinalwishlist()) {

            $wishlistSession->setRemoveFromFinalwishlist(null);
            $item = $observer->getEvent()->getDataObject();
            if ($item) {
                if (!$item->getHasError()) {

                    $connection = Mage::getSingleton('finalwishlist_sharedwishlist/connection');
                    $data = array();

                    $customer = Mage::getModel('customer/session')->getCustomer();
                    $data['customerid'] = $customer->getId();
                    $data['item_id'] = $item->getId();
                    if ($response = $connection->removeItem($data)) {
                        $message = Mage::helper('finalwishlist_sharedwishlist')->__('Item has been removed to your Finalwishlist.com');
                        Mage::getSingleton('customer/session')->addSuccess($message);
                    } else {
                        Mage::getSingleton('customer/session')->addError(Mage::helper('finalwishlist_sharedwishlist')->__('An error occurred while remove item to Finalwishlist.com'));
                    }
                }
            }
        }
    }


    public function addFWtoSession($observer)
    {
        $request = $observer->getEvent()->getControllerAction()->getRequest();
        if ($request->getParam('fwaffiliation')) {
            Mage::getSingleton('core/session')->setData('fwaffiliation', $request->getParam('fwaffiliation'));
        }
        return $this;
    }

    public function boughtToFinalWishlist($observer)
    {

        $affiliationData = Mage::getSingleton('core/session')->getData('fwaffiliation');
        /** @var  Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();

        if ($affiliationData && $order) {
            $decodedAffiliationData = unserialize(base64_decode($affiliationData));
            /** @var Mage_Wishlist_Model_Item $item */
            $item = Mage::getModel('wishlist/item')->load($decodedAffiliationData['item_id']);
            if ($item && $item->getData()) {
                $productId = $item->getProductId();
                $itemsInOrder = $order->getItemsCollection()->addAttributeToFilter('product_id', $productId);
                if (count($itemsInOrder)) {
                    $product = $itemsInOrder->getFirstItem();
                    $customer = Mage::getModel('customer/customer')->load($decodedAffiliationData['customerid']);
                    $data = array();
                    $data['who_gave_customerid'] = ($order->getCustomerId()) ? $order->getCustomerId() : true;
                    $data['customerid'] = ($customer->getId()) ? $customer->getId() : true;
                    $data['email'] = $customer->getEmail();
                    $data['item_id'] = $decodedAffiliationData['item_id'];
                    $data['product_name'] = $product->getName();
                    $connection = Mage::getSingleton('finalwishlist_sharedwishlist/connection');
                    if ($response = $connection->boughtItem($data)) {
                        $message = Mage::helper('finalwishlist_sharedwishlist')->__('%s marked as gifted to Finalwishlist.com',$product->getName());
                        Mage::getSingleton('customer/session')->addSuccess($message);
                    } else {
                        Mage::getSingleton('customer/session')->addError(Mage::helper('finalwishlist_sharedwishlist')->__('An error occurred while send boughtItem to Finalwishlist.com'));
                    }
                }
            }
        }
        return $this;

    }
}
 
