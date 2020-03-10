<?php

namespace Eikona\Tessa\ConnectorBundle;

use Eikona\Tessa\ConnectorBundle\Utilities\IdPrefixer;
use Monolog\Logger;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Pim\Component\Catalog\Model\CategoryInterface;
use Pim\Component\Catalog\Model\ChannelInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Pim\Component\Catalog\Repository\ProductModelRepositoryInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Tessa
{

    /**
     * BasisURL des Tessas ohne Trailing-Slash
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $uiUrl;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var NormalizableInterface
     */
    private $serializer;
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $systemIdentifier;

    /** @var int */
    private $userId;

    /** @var ProductModelRepositoryInterface */
    protected $productModelRepository;

    /** @var IdPrefixer */
    protected $idPrefixer;

    /**
     * Tessa constructor.
     *
     * @param ConfigManager $oroGlobal
     * @param SerializerInterface $serializer
     * @param Kernel|KernelInterface $kernel
     * @param Logger $logger
     * @param ProductModelRepositoryInterface $productModelRepository
     * @param IdPrefixer $idPrefixer
     */
    public function __construct(
        ConfigManager $oroGlobal,
        SerializerInterface $serializer,
        KernelInterface $kernel,
        Logger $logger,
        ProductModelRepositoryInterface $productModelRepository,
        IdPrefixer $idPrefixer
    )
    {
        $this->baseUrl = trim($oroGlobal->get('pim_eikona_tessa_connector.base_url'), ' /');
        $this->uiUrl = trim($oroGlobal->get('pim_eikona_tessa_connector.ui_url'), ' /');
        $this->username = trim($oroGlobal->get('pim_eikona_tessa_connector.username'));
        $this->accessToken = trim($oroGlobal->get('pim_eikona_tessa_connector.api_key'));
        $this->userId = (int)substr($this->accessToken, 0, strpos($this->accessToken, ':'));
        $this->systemIdentifier = trim($oroGlobal->get('pim_eikona_tessa_connector.system_identifier'));
        $this->serializer = $serializer;
        $this->kernel = $kernel;
        $this->logger = $logger;
        $this->productModelRepository = $productModelRepository;
        $this->idPrefixer = $idPrefixer;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getUiUrl()
    {
        return $this->uiUrl;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->determineTessaHttpStatus() < 400;
    }

    /**
     * @return mixed
     */
    protected function determineTessaHttpStatus()
    {
        $ch = curl_init($this->baseUrl);

        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpcode;
    }

    /**
     * @param ProductInterface $product
     */
    public function notifyAboutProductModifications($product)
    {
        if (!$product instanceof ProductInterface && !$product instanceof ProductModelInterface) {
            return;
        }

        $idWithPrefix = $this->idPrefixer->getPrefixedId($product);

        $this->sendModificationNotification(
            $this->serializer->serialize(
                [
                    'id' => $idWithPrefix,
                    'data' => $this->normalize($product),
                    'type' => 'product',
                ],
                'json'
            )
        );

        // Auch die Produkte des Produkt-Models an Tessa übertragen
        if ($product instanceof ProductModelInterface) {
            $products = $this->productModelRepository->findChildrenProducts($product);

            foreach ($products as $childProduct) {
                $this->notifyAboutProductModifications($childProduct);
            }
        }
    }

    /**
     * @param string $payload Die Payload, die an den Request angehängt werden soll.
     */
    private function sendModificationNotification($payload)
    {
        if (empty($this->baseUrl)) {
            return;
        }

        $ch = curl_init($this->getUrlToSendEventsTo());

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Tessa-Api-Token: ' . $this->getAccessToken(),
            )
        );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->kernel->isDebug() ? 30 : 0);
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($this->kernel->isDebug()) {
            if ($httpcode === 200) {
                $this->logger->info('tessa: request sucessfully sent');
            } else {
                $this->logger->error(
                    'tessa: error sending request to dam',
                    [
                        'error' => curl_error($ch),
                        'status' => $httpcode,
                        'result' => $result,
                    ]
                );
            }
        }

        curl_close($ch);
    }

    private function getUrlToSendEventsTo()
    {
        return $this->baseUrl . '/dienste/akeneo/warteschlange.php';
    }

    private function normalize($object)
    {
        return $this->serializer->normalize($object, 'standard');
    }

    /**
     * @param CategoryInterface $category
     */
    public function notifyAboutCategoryModifications($category)
    {
        if (!$category instanceof CategoryInterface) {
            return null;
        }


        $this->sendModificationNotification(
            $this->serializer->serialize(
                [
                    'id' => $category->getId(),
                    'code' => $category->getCode(),
                    'data' => $this->normalize($category),
                    'type' => 'category',
                ],
                'json'
            )
        );
    }

    public function notifyAboutChannelModifications($channel)
    {
        if (!$channel instanceof ChannelInterface) {
            return null;
        }


        $this->sendModificationNotification(
            $this->serializer->serialize(
                [
                    'id' => $channel->getId(),
                    'code' => $channel->getCode(),
                    'data' => $this->normalize($channel),
                    'type' => 'channel',
                ],
                'json'
            )
        );

    }

    /**
     * @param $resourceName
     * @param $resource
     */
    public function notifyAboutEntityDeletion($resourceId, $resource)
    {
        $this->sendModificationNotification(
            $this->serializer->serialize(
                [
                    'context' => 'Deletion',
                    'resourceName' => get_class($resource),
                    'id' => $resourceId,
                    'entity' => null,
                ],
                'json'
            )
        );

    }

    /**
     * @return string
     */
    public function getSystemIdentifier()
    {
        return $this->systemIdentifier;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

}
