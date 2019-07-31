<?php

namespace App\Messenger;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Repository\FacebookUserRepository;
use App\Entity\FacebookUser;

class FacebookApi
{
    private $messengerToken;
    private $facebookUserRepository;

    public function __construct(ParameterBagInterface $params, FacebookUserRepository $facebookUserRepository)
    {
        $this->messengerToken = $params->get('messenger_api_token');
        $this->facebookUserRepository = $facebookUserRepository;
    }

    private function getInformations(string $fbid): FacebookUser
    {
        $url = 'https://graph.facebook.com/' . $fbid;
        $fields = implode(',', ['first_name', 'last_name']);

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
        $response = \json_decode(file_get_contents("$url?$query", false, $context), true);

        return (new FacebookUser)
            ->setFbid($response['id'])
            ->setFirstname($response['first_name'])
            ->setLastname($response['last_name'])
            ->setLastActive(new \DateTimeImmutable());
    }

    public function updateUser(string $fbid)
    {
        $user = $this->getInformations($fbid);
        $this->facebookUserRepository->insertOrUpdate($user);
    }
}