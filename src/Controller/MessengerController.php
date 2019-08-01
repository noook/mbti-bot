<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use App\Collection\MessengerEventHandlerCollection;
use App\Helper\Logger;
use App\Messenger\MessengerRequest;
use App\Messenger\MessengerApi;
use App\Messenger\FacebookApi;

class MessengerController extends AbstractController
{
    const OBJECT_REQUEST = 'page';

    /**
     * @Route("/messenger/webhook", name="messenger_webhook_get", methods={"GET"})
     */
    public function webhookGet(Request $request)
    {
        $verifyToken = $this->getParameter('messenger_verify_token');
        $data = $request->query->all();
        $mode = $data['hub_mode'];
        $token = $data['hub_verify_token'];
        $challenge = $data['hub_challenge'];

        if (null !== $mode && null !== $token) {
            if ('subscribe' === $mode && $verifyToken == $token) {
                return new Response($challenge, Response::HTTP_OK);
            } else {
                throw new AccessDeniedException();
            }
        }
    }

    /**
     * @Route("/messenger/webhook", name="messenger_webhook_post", methods={"POST"})
     */
    public function webhookPost(
        Request $request,
        MessengerApi $messengerApi,
        MessengerEventHandlerCollection $messengerEventHandlerCollection,
        FacebookApi $facebookApi
    ): Response
    {
        $messengerRequest = new MessengerRequest($request->getContent());
        
        if (self::OBJECT_REQUEST === $messengerRequest->getObject()) {
            foreach ($messengerRequest->getEntries() as $message) {
                $facebookApi->updateUser($message->getSender());
                $messengerApi
                    ->setRecipient($message->getSender())
                    ->markSeen();

                $messengerEventHandlerCollection
                    ->get($message->getType())
                    ->handle($message);
            }
        }

        return new Response('EVENT RECEIVED', Response::HTTP_OK);
    }
}
