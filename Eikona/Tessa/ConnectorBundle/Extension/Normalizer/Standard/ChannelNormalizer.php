<?php
/**
 * ChannelNormalizer.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Extension\Normalizer\Standard;

use Pim\Component\Catalog\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ChannelNormalizer extends \Pim\Component\Catalog\Normalizer\Standard\ChannelNormalizer
{
    /** @var null|\Symfony\Component\HttpFoundation\Request */
    private $request;

    public function __construct(NormalizerInterface $translationNormalizer, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();

        parent::__construct($translationNormalizer);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return ['id' => $object->getId()] + parent::normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($this->request === null) {
            return false;
        }

        $isTessaApiRequest = (boolean)json_decode(strtolower($this->request->get('tessa')));

        return $data instanceof ChannelInterface && 'external_api' === $format && $isTessaApiRequest;
    }

}
