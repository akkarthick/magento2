<?php
namespace Mageplaza\ProductType\Model\Product;

class Price extends \Magento\Catalog\Model\Product\Type\Price
{

    public function getPrice($product)
    {
            return 0;
    }
}
