<?php

namespace Mageplaza\ProductType\Model\Product;

class Type extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const TYPE_ID = 'printable_product_type';

    public function save($product)
    {
        parent::save($product);
        return $this;
    }

    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
	return $this;
    }
}
