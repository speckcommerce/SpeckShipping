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
        return array(
            'speck_shipping' => array(
                //shipping class level cost modifiers
                'sc_cost_modifiers' => array(
                    //name of modifier => fqcn of class that implements costmodifierinterface
                ),
                'shipping_class_resolvers' => array(
                    //fqcn of the item metadata => resolver name(from servicelocator)
                    'SpeckCatalogCart\Model\CartProductMeta' => 'catalog_product_sc_resolver'
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'speckshipping_shipping_service' => 'SpeckShipping\Service\Shipping',
                'speckshipping_sc_mapper' => 'SpeckShipping\Mapper\ShippingClass',
                'speckshipping_p_sc_mapper' => 'SpeckShipping\Mapper\Product',
                'speckshipping_c_sc_mapper' => 'SpeckShipping\Mapper\Category',
                'speckshipping_w_sc_mapper' => 'SpeckShipping\Mapper\Website',
            ),
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
            'aliases' => array(
                'speckshipping_db' => 'Zend\Db\Adapter\Adapter',
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
         *   'SpeckCheckout\Strategy\Step\UserInformation',
         *   'setComplete'
         *
         *   'SpeckCatalogCart\Service\CartService',
         *   'persistItem'
         *
         *   'SpeckCatalogCart\Service\CartService',
         *   'addItemToCart'
         *
         */

        $shipping = new \SpeckShipping\Event\Shipping();
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

        $shippingCost = new \SpeckShipping\Event\CartShippingCost();
        $shippingCost->setServiceLocator($sl);
        $em->attach(
            'SpeckShipping\Service\Shipping',
            'getShippingCost',
            function ($e) use ($shippingCost) {
                $shippingCost->quantityCostIncrementer($e);
            },
            300
        );
        $em->attach(
            'SpeckShipping\Service\Shipping',
            'getShippingCost',
            function ($e) use ($shippingCost) {
                $shippingCost->shippingPriority($e);
            },
            200
        );
        $em->attach(
            'SpeckShipping\Service\Shipping',
            'getShippingCost',
            function ($e) use ($shippingCost) {
                $shippingCost->cartShippingCost($e);
            },
            100
        );
    }
}
