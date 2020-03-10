<?php

    namespace Eikona\Tessa\ConnectorBundle\Security;

    use Eikona\Tessa\ConnectorBundle\Tessa;
    use Eikona\Tessa\ConnectorBundle\Utilities\Math;
    use Monolog\Logger;

    class AuthGuard
    {
        private $encryption = 'sha256';

        /**
         * @var Logger
         */
        private $logger;

        /**
         * @var string
         */
        private $tessaId;

        /**
         * @var Math
         */
        private $mathUtil;

        /** @var int */
        private $userId;

        /**
         * AuthGuard constructor.
         *
         * @param Tessa  $tessa
         * @param Math   $math
         * @param Logger $logger
         */
        public function __construct(Tessa $tessa, Math $math, Logger $logger)
        {
            $this->username = $tessa->getUsername();
            $this->token = $tessa->getAccessToken();
            $this->userId = $tessa->getUserId();
            $this->tessaId = $tessa->getSystemIdentifier();
            $this->logger = $logger;
            $this->mathUtil = $math;
        }

        public function getHmac($httpMethod, $uri, $timestamp)
        {
            $secret = $this->getSecret($httpMethod, $uri, $timestamp);
            $hmac = sprintf('%s:%s', $this->username, $secret);
            $this->logger->debug(
                    sprintf(
                            'Generated hmac secret: %s with username=%s,pw=%s,method=%s,uri=%s,t=%s',
                            base64_decode($secret),
                            $this->username,
                            $this->token,
                            $httpMethod,
                            $uri,
                            $timestamp
                    )
            );

            return $hmac;
        }

        private function getSecret($httpMethod, $uri, $timestamp)
        {
            $secret = hash_hmac(
                    $this->encryption,
                    $this->token,
                    sprintf('%s+%s+%s', $httpMethod, $uri, $timestamp)
            );

            return base64_encode($secret);
        }

        /**
         * @param $assetId          int
         * @param $moduleIdentifier string
         *
         * @return string
         */
        public function getDownloadAuthToken($assetId, $moduleIdentifier)
        {
            // Addition (statt Concat), da das auf Tessa-Seite ebenfalls so gemacht wird
            $sum = (int)$assetId
                + $this->mathUtil->getCrossSum(hash('md5', $this->tessaId))
                + $this->mathUtil->getCrossSum(hash('md5', "{$moduleIdentifier}_{$assetId}"));

            return hash('sha256', $sum);
        }
    }
