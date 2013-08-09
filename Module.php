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
                'cost_modifiers' => array(
                    'incremental_qty' => '\SpeckShipping\Entity\CostModifier\IncrementalQty',
                ),
                'shipping_class_resolvers' => array(
                    //fqcn of the item metadata => resolver name(from servicelocator)
                    'SpeckCatalogCart\Model\CartProductMeta' => 'catalog_product_sc_resolver'
                ),
            ),
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
        );
    }
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'speckshipping_shipping_service' => 'SpeckShipping\Service\Shipping',
                'speckshipping_sc_mapper' => 'SpeckShipping\Mapper\ShippingClass',
            ),
            'factories' => array(
                'speckshipping_config' => function ($sm) {
                    $config = $sm->get('Config');
                    return $config['speck_shipping'];
                },
                'catalog_product_sc_resolver' => function ($sm) {
                    $resolver = new CatalogProductResolver();
                    $resolver->setShippingClassMapper($sm->get('speckshipping_sc_mapper'));
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
        $em  = $app->getEventManager()->getSharedManager();
        $sl  = $app->getServiceManager();

        $em->attach(
            'SpeckCheckout\Strategy\Step\UserInformation',
            'setComplete',
            function ($e) use ($sl) {
            }
        );

        $em->attach(
            'SpeckCatalogCart\Service\CartService',
            'persistItem',
            function ($e) use ($sl) {
            }
        );

        $em->attach(
            'SpeckCatalogCart\Service\CartService',
            'addItemToCart',
            function ($e) use ($sl) {
            }
        );

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
            'getShippingCost',
            function ($e) use ($shipping) {
                $shipping->cartShippingCost($e);
            }
        );
        $em->attach(
            'SpeckShipping\Service\Shipping',
            'getShippingClassCost',
            function ($e) use ($shipping) {
                $shipping->shippingClassCostModifiers($e);
            }
        );
    }
}
