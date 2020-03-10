<?php
    /** (c) EIKONA AG, it.x informationssysteme gmbh, Alle Rechte vorbehalten.
     *
     * Historie ----------------------------------------------------------------------------------------
     * 25.05.2016 mmr Erstellung
     * Historie ----------------------------------------------------------------------------------------
     */

    namespace Eikona\Tessa\ConnectorBundle\Enrich\Provider\Field;

    use Pim\Bundle\EnrichBundle\Provider\Field\FieldProviderInterface;
    use Pim\Component\Catalog\Model\AttributeInterface;

    class MamFieldProvider implements FieldProviderInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getField($attribute)
        {
            return 'eikona-tessa-field';
        }

        /**
         * {@inheritdoc}
         */
        public function supports($element)
        {
            /* @var $element AttributeInterface */
            return $element instanceof AttributeInterface &&
                    $element->getAttributeType() === 'eikona_catalog_tessa';
        }
    }