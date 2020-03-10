<?php
    /**
     * TessaType.php
     *
     * @author    Matthias Mahler <m.mahler@eikona.de>
     * @copyright 2017 Eikona AG (http://www.eikona.de)
     */

    namespace Eikona\Tessa\ConnectorBundle\AttributeType;

    use Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType;

    class TessaType extends AbstractAttributeType
    {

        /**
         * {@inheritdoc}
         */
        public function getName()
        {
            return 'eikona_catalog_tessa';
        }

    }
