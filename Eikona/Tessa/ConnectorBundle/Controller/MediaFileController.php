<?php
/**
 * MediaFileController.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2016 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Controller;

use Pim\Bundle\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class MediaFileController extends Controller
{
    const SSO_ACTION_ASSET_DETAIL = 'detail';
    const SSO_ACTION_ASSET_SELECT = 'select';

    /**
     * @param int $assetId
     * @return Response
     */
    public function previewAction($assetId)
    {
        $authGuard = $this->get('eikona.tessa.auth_guard');
        $downloadToken = $authGuard->getDownloadAuthToken($assetId, 'bild');
        $url = $this->get('eikona.tessa')->getBaseUrl()
            . '/ui/bild.php'
            . '?asset_system_id=' . $assetId
            . '&type=preview'
            . '&key=' . $downloadToken;

        return new RedirectResponse($url, 301);
    }

    /**
     * @param $assetId
     * @return Response
     */
    public function detailAction($assetId)
    {
        return $this->gotoTessaWithAuthentication(
            self::SSO_ACTION_ASSET_DETAIL,
            ['id' => $assetId]
        );
    }

    /**
     * @param $data
     * @return Response
     */
    public function selectAction($data)
    {
        // $data wird vom Frontend als Query-Parameter formatiert -> in Array umwandeln
        parse_str(urldecode($data), $dataDecoded);

        return $this->gotoTessaWithAuthentication(
            self::SSO_ACTION_ASSET_SELECT,
            $dataDecoded
        );
    }

    /**
     * @param string $action
     * @param array $payload
     * @return Response
     */
    protected function gotoTessaWithAuthentication(?string $action = self::SSO_ACTION_ASSET_SELECT, $payload = [])
    {
        $tessa = $this->get('eikona.tessa');

        if (!$tessa->isAvailable()) {
            return $this->render('@EikonaTessaConnector/Tessa/404.html.twig');
        }

        $authGuard = $this->get('eikona.tessa.auth_guard');
        $tokenStorage = $this->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();
        $url = $tessa->getBaseUrl() . '/ui/login.php';
        $auth = $authGuard->getHmac('GET', $url, time());

        $queryParams = [
            'auth' => $auth,
            'system' => 'akeneo',
            'action' => $action,
            'data' => $payload,
            'user' => [
                'username' => $user->getUsername(),
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
                'email' => $user->getEmail(),
                'locale' => $user->getUiLocale()->getCode(),
            ]
        ];

        $urlWithParams = $url . '?' . http_build_query($queryParams);
        return new RedirectResponse($urlWithParams, 301);
    }
}
