<?php

namespace App\Helper;

use App\Collection\MessageFormatterCollection;
use App\Formatter\MessageFormatterAliases;
use App\Handler\QuickReply\QuickReplyDomainAliases;
use App\Messenger\MessengerApi;
use App\Messenger\MessengerRequestMessage;
use App\Repository\FacebookUserRepository;
use Symfony\Component\Translation\TranslatorInterface;

class BasicMessages
{
    private $facebookUserRepository;
    private $messageFormatterCollection;
    private $messengerApi;
    private $translator;

    public function __construct(
        FacebookUserRepository $facebookUserRepository,
        MessageFormatterCollection $messageFormatterCollection,
        MessengerApi $messengerApi,
        TranslatorInterface $translator
    )
    {
        $this->facebookUserRepository = $facebookUserRepository;
        $this->messageFormatterCollection = $messageFormatterCollection;
        $this->messengerApi = $messengerApi;
        $this->translator = $translator;
    }

    public function greet(MessengerRequestMessage $message)
    {
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $message->getSender()]);
        $name = null === $user ? '' : $user->getFirstname();
        $locale = null === $user ? 'fr' : $user->getLocale();
        $messages = [];

        for ($i = 1; $i <= 3; $i++) {
            $messages[] = [
                'type' => MessageFormatterAliases::TEXT,
                'text' => $this->translator->trans('get_started_'.$i, ['{name}' => $name, null, $locale]),
            ];
        }
        $last = array_pop($messages);
        $last = array_merge($last, [
            'type' => MessageFormatterAliases::QUICK_REPLY,
            'quick_replies' => [
                [
                    'title' => $this->translator->trans('take_the_test', [], 'mbti', $locale),
                    'payload' => \json_encode([
                        'domain' => QuickReplyDomainAliases::MBTI_DOMAIN,
                        'type' => 'start-test',
                    ]),
                ],
            ],
        ]);
        $messages[] = $last;

        foreach ($messages as $key => $message) {
            $this->messengerApi->setTyping('on');

            $message = $messages[$key] = $this
                    ->messageFormatterCollection
                    ->get($message['type'])
                    ->format($message);
            sleep(1);
            $this->messengerApi->sendMessage($message);
            $this->messengerApi->setTyping('off');
        }
    }
}