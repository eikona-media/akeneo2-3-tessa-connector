<?php
/**
 * ProductPropertiesNormalizer.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Extension\Normalizer\Standard;

use Eikona\Tessa\ConnectorBundle\Utilities\IdPrefixer;
use Pim\Component\Api\Repository\AttributeRepositoryInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class ProductPropertiesNormalizer extends \Pim\Component\Api\Normalizer\ProductNormalizer
{
    /**
     * @var ContainerInterface
     */
    private $request;

    /**
     * @var IdPrefixer
     */
    protected $idPrefixer;

    public function __construct(
        NormalizerInterface $productNormalizer,
        AttributeRepositoryInterface $attributeRepository,
        RouterInterface $router,
        RequestStack $requestStack,
        IdPrefixer $idPrefixer
    )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->productNormalizer = $productNormalizer;
        $this->attributeRepository = $attributeRepository;
        $this->router = $router;
        $this->idPrefixer = $idPrefixer;
        parent::__construct($productNormalizer, $attributeRepository, $router);
    }

    public function normalize($product, $format = null, array $context = [])
    {
        $data = parent::normalize(
            $product,
            $format,
            $context
        );

        $idWithPrefix = $this->idPrefixer->getPrefixedId($product);

        $data['id'] = $idWithPrefix;

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($this->request === null) {
            return false;
        }

        $isTessaApiRequest = (boolean)json_decode(strtolower($this->request->get('tessa')));

        if (($data instanceof ProductInterface || $data instanceof ProductModelInterface)
            && 'external_api' === $format
            && $isTessaApiRequest === true) {
            return true;
        }

        return false;
    }

}
