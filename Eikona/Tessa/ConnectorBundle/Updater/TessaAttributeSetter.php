<?php
/**
 * TessaAttributeSetter.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Updater;

use Pim\Component\Catalog\Builder\EntityWithValuesBuilderInterface;
use Pim\Component\Catalog\Builder\ProductBuilderInterface;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\EntityWithValuesInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Updater\Setter\AbstractAttributeSetter;
use Pim\Component\Catalog\Validator\AttributeValidatorHelper;

class TessaAttributeSetter extends AbstractAttributeSetter
{
    /**
     * @param ProductBuilderInterface $productBuilder
     * @param AttributeValidatorHelper $attrValidatorHelper
     * @param array $supportedTypes
     */
    public function __construct(
        EntityWithValuesBuilderInterface $entityWithValuesBuilder,
        array $supportedTypes
    ) {
        parent::__construct($entityWithValuesBuilder);
        $this->supportedTypes = $supportedTypes;
    }

    /**
     * Set attribute data
     *
     * @param ProductInterface $product The product to modify
     * @param AttributeInterface $attribute The attribute of the product to modify
     * @param mixed $data The data to set
     * @param array $options Options passed to the setter
     */
    public function setAttributeData(
        EntityWithValuesInterface $entityWithValues,
        AttributeInterface $attribute,
        $data,
        array $options = []
    ) {
        $options = $this->resolver->resolve($options);
        $this->setData($entityWithValues, $attribute, $data, $options['locale'], $options['scope']);
    }

    /**
     * Set the data into the product value
     *
     * @param ProductInterface $product
     * @param AttributeInterface $attribute
     * @param mixed $data
     * @param string $locale
     * @param string $scope
     */
    protected function setData(
        EntityWithValuesInterface $product,
        AttributeInterface $attribute,
        $data,
        $locale,
        $scope
    ) {
        $this->entityWithValuesBuilder->addOrReplaceValue(
            $product,
            $attribute,
            $locale,
            $scope,
            $data
        );
    }
}
