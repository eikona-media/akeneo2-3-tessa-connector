<?php
/**
 * IdPrefixer.php
 *
 * @author      Timo Müller <t.mueller@eikona-media.de>
 * @copyright   2018 Eikona AG (http://eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Utilities;


use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;

class IdPrefixer
{
    /**
     * Gibt die ID eines Produkts oder Produkt-Modells mit einem
     * Prefix für Tessa zurück
     *
     * @param ProductInterface|ProductModelInterface $product
     * @return string
     */
    public function getPrefixedId($product)
    {
        if ($product instanceof ProductInterface) {
            return 'P-' . (string)$product->getId();
        }

        if ($product instanceof ProductModelInterface) {
            return 'PM-' . (string)$product->getId();
        }

        throw new \InvalidArgumentException('Invalid type for parameter $product');
    }
}
