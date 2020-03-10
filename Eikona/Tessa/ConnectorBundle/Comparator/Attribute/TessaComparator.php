<?php
    /**
     * TessaComparator.php
     *
     * @author    Matthias Mahler <m.mahler@eikona.de>
     * @copyright 2017 Eikona AG (http://www.eikona.de)
     */

    namespace Eikona\Tessa\ConnectorBundle\Comparator\Attribute;

    use Pim\Component\Catalog\Comparator\ComparatorInterface;

    class TessaComparator implements ComparatorInterface
    {

        public function supports($type)
        {
            return 'eikona_catalog_tessa' === $type;
        }

        public function compare($data, $originals)
        {
            $default = ['locale' => null, 'scope' => null, 'data' => null];
            $originals = array_merge($default, $originals);

            return (string)$data['data'] !== (string)$originals['data'] ? $data : null;
        }

    }