<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MessengerController extends AbstractController
{
    /**
     * @Route("/messenger/webhook", name="messenger_webhook_get", methods={"GET"})
     */
    public function webhookGet(Request $request)
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MessengerController.php',
        ]);
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
