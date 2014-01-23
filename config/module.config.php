<?php
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
    'service_manager' => array(
        'invokables' => array(
            'speckshipping_shipping_service' => 'SpeckShipping\Service\Shipping',
            'speckshipping_sc_mapper'        => 'SpeckShipping\Mapper\ShippingClass',
            'speckshipping_p_sc_mapper'      => 'SpeckShipping\Mapper\Product',
            'speckshipping_c_sc_mapper'      => 'SpeckShipping\Mapper\Category',
            'speckshipping_w_sc_mapper'      => 'SpeckShipping\Mapper\Website',
        ),
        'aliases' => array(
            'speckshipping_db' => 'Zend\Db\Adapter\Adapter',
        ),
    ),
);
