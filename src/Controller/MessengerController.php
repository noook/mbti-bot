<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\Logger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class MessengerController extends AbstractController
{
    /**
     * @Route("/messenger/webhook", name="messenger_webhook_get", methods={"GET"})
     */
    public function webhookGet(Request $request, Logger $logger)
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
    public function webhookPost(Request $request)
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MessengerController.php',
        ]);
    }
}
