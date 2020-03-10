<?php


namespace Eikona\Tessa\ConnectorBundle\AttributeType\Factory;


use Pim\Component\Catalog\Factory\Value\ScalarValueFactory;

class TessaValueFactory extends ScalarValueFactory
{
    /**
     * {@inheritdoc}
     */
    public function supports($attributeType)
    {
        return $attributeType === $this->supportedAttributeTypes;
    }
}
