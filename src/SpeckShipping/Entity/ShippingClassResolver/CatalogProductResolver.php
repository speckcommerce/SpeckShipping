<?php

namespace SpeckShipping\Entity\ShippingClassResolver;

use SpeckShipping\Entity\ShippingClassInterface;

class CatalogProductResolver extends AbstractShippingClassResolver
{
    protected $shippingClassMapper;
    protected $productMapper;
    protected $categoryMapper;
    protected $websiteMapper;

    //see if there is a shipping class associated with the product
    //if not, crawl up through parent categories until one is found,
    //if none are found, return the site default shipping class
    public function resolveShippingClass()
    {
        $productId = $this->getCartItem()->getMetadata()->getProductId();

        $sc = $this->getProductShippingClass($productId);
        if ($sc) return $this->decorateShippingClass($sc);

        $sc = $this->crawlForShippingClass($productId);
        if ($sc) return $this->decorateShippingClass($sc);

        $sc = $this->getSiteShippingClass();
        if ($sc) return $this->decorateShippingClass($sc);

        throw new \Exception('didnt get a shipping class for the item');
    }

    public function decorateShippingClass(ShippingClassInterface $sc)
    {
        $cartMeta = $this->getCartItem()->getMetadata();
        $cartItem = $this->getCartItem();

        $sc->set('resolved', get_class($this));
        $sc->set('quantity', $cartItem->getQuantity());
        $sc->set('product_id', $cartMeta->getProductId());

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
            $catIdField = 'category_id';
        } else {
            $parentCategories = $this->getParentCategoriesForCategory($categoryId);
            $catIdField = 'parent_category_id';
        }

        foreach ($parentCategories as $cat) {
            if ($cat['shipping_class_id']) {
                return $this->getShippingClassById($cat['shipping_class_id']);
            }
            return $this->crawlForShippingClass(null, $cat[$catIdField]);
        }
        return false;
    }

    public function getShippingClassById($id)
    {
        return $this->getShippingClassMapper()
            ->getShippingClassById($id);
    }

    //get parent categories (array of rows)
    //joined with category shipping class
    public function getParentCategoriesForCategory($categoryId)
    {
        return $this->getCategoryMapper()
            ->getParentCategoriesForCategory($categoryId);
    }

    //get parent categories (array of rows)
    //joined with category shipping cass
    public function getParentCategoriesForProduct($productId)
    {
        return $this->getCategoryMapper()
            ->getParentCategoriesForProduct($productId);
    }

    public function getProductShippingClass($productId)
    {
        return $this->getProductMapper()
            ->getProductShippingClass($productId);
    }

    public function getSiteShippingClass()
    {
        return $this->getWebsiteMapper()
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

    public function getProductMapper()
    {
        return $this->productMapper;
    }

    public function setProductMapper($productMapper)
    {
        $this->productMapper = $productMapper;
        return $this;
    }

    public function getCategoryMapper()
    {
        return $this->categoryMapper;
    }

    public function setCategoryMapper($categoryMapper)
    {
        $this->categoryMapper = $categoryMapper;
        return $this;
    }

    public function getWebsiteMapper()
    {
        return $this->websiteMapper;
    }

    public function setWebsiteMapper($websiteMapper)
    {
        $this->websiteMapper = $websiteMapper;
        return $this;
    }
}
