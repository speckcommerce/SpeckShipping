<?php

namespace SpeckShipping;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return array(
            'controllers' => array(
                'invokables' => array(
                    'shipping' => 'SpeckShipping\Controller\Shipping',
                ),
            ),
            'router' => array(
                'routes' => array(
                    'shipping' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/shipping[/[:cartId]]',
                            'defaults' => array(
                                'controller' => 'shipping',
                                'action'     => 'index'
                            ),
                        ),
                    ),
                ),
            ),
            'service_manager' => array(
                'invokables' => array(
                    'speckshipping_shipping_service' => 'SpeckShipping\Service\Shipping',
                ),
            ),
        );
    }

    public function onBootstrap($e)
    {
        $app = $e->getParam('application');
        $em  = $app->getEventManager()->getSharedManager();
        $sl  = $app->getServiceManager();

        $em->attach(
            'SpeckCheckout\Strategy\Step\UserInformation',
            'setComplete',
            function ($e) use ($sl) {
                //$checkoutEvents = new \SwmCatalogLayout\Event\Checkout();
                //$checkoutEvents->addArizonaSalesTax($e, $sl);
            }
        );

        //on cart item add/modify, check for az sales tax and apply if necessary
        $em->attach(
            'SpeckCatalogCart\Service\CartService',
            'persistItem',
            function ($e) use ($sl) {
                //$checkoutEvents = new \SwmCatalogLayout\Event\Checkout();
                //$checkoutEvents->preCartItemPersist($e, $sl);
            }
        );
        $em->attach(
            'SpeckCatalogCart\Service\CartService',
            'addItemToCart',
            function ($e) use ($sl) {
                //$checkoutEvents = new \SwmCatalogLayout\Event\Checkout();
                //$checkoutEvents->preCartItemPersist($e, $sl);
            }
        );

        //todo: attach to shipping service cart shipping cost
        //todo: attach to shipping service cart item shipping cost
    }
}
