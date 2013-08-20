<?php

namespace SpeckShipping;

use SpeckShipping\Entity\ShippingClassResolver\CatalogProductResolver;

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
        return include(__DIR__ . '/config/module.config.php');
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'speckshipping_config' => function ($sm) {
                    $config = $sm->get('Config');
                    return $config['speck_shipping'];
                },
                'catalog_product_sc_resolver' => function ($sm) {
                    $resolver = new CatalogProductResolver();
                    $resolver->setShippingClassMapper($sm->get('speckshipping_sc_mapper'));
                    $resolver->setProductMapper($sm->get('speckshipping_p_sc_mapper'));
                    $resolver->setCategoryMapper($sm->get('speckshipping_c_sc_mapper'));
                    $resolver->setWebsiteMapper($sm->get('speckshipping_w_sc_mapper'));
                    return $resolver;
                },
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'speckShipping' => function($sm) {
                    $sm = $sm->getServiceLocator();
                    $helper = new \SpeckShipping\View\Helper\Shipping;
                    $helper->setShippingService($sm->get('speckshipping_shipping_service'));
                    return $helper;
                },
            ),
        );
    }

    public function onBootstrap($e)
    {
        $app = $e->getParam('application');
        $sl  = $app->getServiceManager();
        $em  = $app->getEventManager()->getSharedManager();

        /*
         * some events you may wish to attach to..
         *
         *   SpeckCheckout\Strategy\Step\UserInformation
         *   setComplete
         *
         *   SpeckCatalogCart\Service\CartService
         *   persistItem, addItemToCart
         *
         */

        $shipping = new \SpeckShipping\Event\ShippingClassEvents();
        $shipping->setServiceLocator($sl);
        $em->attach(
            'SpeckShipping\Service\Shipping',
            'getShippingClass',
            function ($e) use ($shipping) {
                return $shipping->shippingClassForCartItem($e);
            }
        );
        $em->attach(
            'SpeckShipping\Service\Shipping',
            'getShippingClassCost',
            function ($e) use ($shipping) {
                $shipping->shippingClassCostModifiers($e);
            }
        );

        //cart shipping cost events
        $em->attach(
            'SpeckShipping\Service\Shipping',
            'getShippingCost',
            function ($e) {
                $handler = new \SpeckShipping\Event\IncrementalQtyCost($e);
                $handler->adjustCosts();
            },
            300
        );
        $em->attach(
            'SpeckShipping\Service\Shipping',
            'getShippingCost',
            function ($e) {
                $handler = new \SpeckShipping\Event\ShippingPriority($e);
                $handler->adjustCosts();
            },
            200
        );
        $em->attach(
            'SpeckShipping\Service\Shipping',
            'getShippingCost',
            function ($e) {
                $handler = new \SpeckShipping\Event\CartShippingCost($e);
                return $handler->getShippingCost();
            },
            100
        );
    }
}
