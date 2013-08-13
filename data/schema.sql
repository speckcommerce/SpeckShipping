CREATE TABLE IF NOT EXISTS `shipping_class` (
  `shipping_class_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `base_cost` decimal(15,5) NOT NULL,
  `meta` text NOT NULL,
  PRIMARY KEY (`shipping_class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shipping_class_category` (
  `shipping_class_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  UNIQUE KEY `ship_class_cat_idx` (`shipping_class_id`,`website_id`,`category_id`),
  KEY `website_id` (`website_id`),
  KEY `shipping_class_category_ibfk_2` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shipping_class_product` (
  `shipping_class_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  UNIQUE KEY `shipping_class_id` (`shipping_class_id`,`product_id`,`website_id`),
  KEY `website_id` (`website_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shipping_class_website` (
  `shipping_class_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  UNIQUE KEY `ship_class_site_idx` (`shipping_class_id`,`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `shipping_class_category`
  ADD CONSTRAINT `shipping_class_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `catalog_category` (`category_id`),
  ADD CONSTRAINT `shipping_class_category_ibfk_1` FOREIGN KEY (`shipping_class_id`) REFERENCES `shipping_class` (`shipping_class_id`),
  ADD CONSTRAINT `shipping_class_category_ibfk_3` FOREIGN KEY (`website_id`) REFERENCES `website` (`website_id`);

ALTER TABLE `shipping_class_product`
  ADD CONSTRAINT `shipping_class_product_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`),
  ADD CONSTRAINT `shipping_class_product_ibfk_1` FOREIGN KEY (`shipping_class_id`) REFERENCES `shipping_class` (`shipping_class_id`),
  ADD CONSTRAINT `shipping_class_product_ibfk_2` FOREIGN KEY (`website_id`) REFERENCES `website` (`website_id`);
