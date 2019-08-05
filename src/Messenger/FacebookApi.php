<?php

namespace App\Messenger;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Repository\FacebookUserRepository;
use App\Entity\FacebookUser;

class FacebookApi
{
    const GET_STARTED_PAYLOAD = [
        'domain' => 'main',
    ];

    private $messengerToken;
    private $facebookUserRepository;

    public function __construct(ParameterBagInterface $params, FacebookUserRepository $facebookUserRepository)
    {
        $this->messengerToken = $params->get('messenger_api_token');
        $this->facebookUserRepository = $facebookUserRepository;
    }

    private function getInformations(string $fbid): ?FacebookUser
    {
        $url = 'https://graph.facebook.com/' . $fbid;
        $fields = implode(',', ['first_name', 'last_name', 'locale']);

        $query = http_build_query([
            'fields' => $fields,
            'access_token' => $this->messengerToken,
        ]);

        $opts = [
            'http' => [
                'method'  => 'GET',
                'header'  => 'Content-Type: application/json',
            ],
        ];

        $context  = stream_context_create($opts);
        try {
            $response = \json_decode(file_get_contents("$url?$query", false, $context), true);
        } catch (\Throwable $e) {
            return null;
        }

        return (new FacebookUser)
        ->setFbid($response['id'])
        ->setFirstname($response['first_name'])
        ->setLastname($response['last_name'])
        ->setLocale($response['locale'])
        ->setLastActive(new \DateTimeImmutable());
    }

    public function updateUser(string $fbid)
    {
        $user = $this->getInformations($fbid);
        if (null === $user) {
            return;
        }
        $this->facebookUserRepository->insertOrUpdate($user);
    }

    public function updateBotProfile(): bool
    {
        $url = 'https://graph.facebook.com/v4.0/me/messenger_profile';

        $query = http_build_query([
            'access_token' => $this->messengerToken,
        ]);

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => \json_encode([
                    'get_started' => [
                        'payload' => \json_encode(self::GET_STARTED_PAYLOAD),
                    ]
                ]),
            ],
        ];

        $context  = stream_context_create($opts);
        try {
            $response = file_get_contents("$url?$query", false, $context);
        } catch (\Throwable $e) {
            return false;
        }
        $response = \json_decode($response, true);

        return $response['result'] === 'success';
    }
}