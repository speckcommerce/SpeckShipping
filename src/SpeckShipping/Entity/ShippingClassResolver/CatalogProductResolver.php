<?php

namespace SpeckShipping\Entity\ShippingClassResolver;

class CatalogProductResolver extends AbstractShippingClassResolver
{
    protected $shippingClassMapper;

    //see if there is a shipping class associated with the product
    //if not, crawl up through parent categories until one is found,
    //if none are found, return the site default shipping class
    public function resolveShippingClass()
    {
        $productId = $this->getCartItem()->getMetadata()->getProductId();

        $sc = $this->getProductShippingClass($productId);
        if ($sc) return $sc;

        $sc = $this->crawlForShippingClass($productId);
        if ($sc) return $sc;

        $sc = $this->getSiteShippingClass();
        return $sc;
    }

    //traverse up category tree, returning a shipping class, or false
    public function crawlForShippingClass($productId = null, $categoryId = null)
    {
        if ($categoryId === null && $productId === null) {
            throw new \Exception('what are we crawling?');
        }

        if ($productId) {
            $parentCategories = $this->getParentCategoriesForProduct($productId);
        } else {
            $parentCategories = $this->getParentCategoriesForCategory($categoryId);
        }

        foreach ($parentCategories as $cat) {
            if ($cat['shipping_class_id'] > 0) {
                return $this->getShippingClassById($cat['shipping_class_id']);
            } else {
                return $this->crawlForShippingClass(null, $cat['category_id']);
            }
        }
        return false;
    }

    public function getShippingClassById($id)
    {
        return $this->getShippingClassMapper()
            ->getShippingClassById($id);
    }

    //get parent categories (array of rows)
    //joined with category shipping cass
    public function getParentCategoriesForCategory($categoryId)
    {
        return $this->getShippingClassMapper()
            ->getParentCategoriesForCategory($categoryId);
    }

    //get parent categories (array of rows)
    //joined with category shipping cass
    public function getParentCategoriesForProduct($productId)
    {
        return $this->getShippingClassMapper()
            ->getParentCategoriesForProduct($productId);
    }

    public function getProductShippingClass($productId)
    {
        return $this->getShippingClassMapper()
            ->getProductShippingClass($productId);
    }

    public function getSiteShippingClass()
    {
        return $this->getShippingClassMapper()
            ->getSiteShippingClass();
    }

    public function getShippingClassMapper()
    {
        return $this->shippingClassMapper;
    }

    public function setShippingClassMapper($shippingClassMapper)
    {
        $this->shippingClassMapper = $shippingClassMapper;
        return $this;
    }
}
