<?php

namespace App\Handler\MessengerEvent;

use Symfony\Component\Translation\TranslatorInterface;
use App\Collection\ContextHandlerCollection;
use App\Collection\MessageFormatterCollection;
use App\Formatter\MessageFormatterAliases;
use App\Golem\GolemManager;
use App\Messenger\MessengerApi;
use App\Messenger\MessengerRequestMessage;
use App\Repository\FacebookUserRepository;

class MessageHandler implements MessengerEventHandlerInterface
{
    private $golemManager;
    private $facebookUserRepository;
    private $contextHandlerCollection;
    private $messengerApi;
    private $messageFormatterCollection;
    private $translator;

    public function __construct(
        GolemManager $golemManager,
        FacebookUserRepository $facebookUserRepository,
        ContextHandlerCollection $contextHandlerCollection,
        MessengerApi $messengerApi,
        MessageFormatterCollection $messageFormatterCollection,
        TranslatorInterface $translator
    )
    {
        $this->golemManager = $golemManager;
        $this->facebookUserRepository = $facebookUserRepository;
        $this->contextHandlerCollection = $contextHandlerCollection;
        $this->messengerApi = $messengerApi;
        $this->messageFormatterCollection = $messageFormatterCollection;
        $this->translator = $translator;
    }

    public function getAlias(): string
    {
        return MessengerHandlerEventAliases::MESSAGE_HANDLER;
    }

    /**
     * @todo Handle Golem request
     */
    public function handle(MessengerRequestMessage $message)
    {
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $message->getSender()]);
        $options = [
            'text' => $message->getText(),
        ];
        if (null !== $user && null !== $user->getLocale()) {
            $options['language'] = explode('_', $user->getLocale())[0];
        }
        $golemRequest = $this->golemManager->createRequest($options);
        $golemResponse = $this->golemManager->makeRequest($golemRequest);

        if (null === $golemResponse->getCall()) {
            return $this->messageNotUnderstood($message->getSender());
        }

        if ($this->contextHandlerCollection->has($golemResponse->getCall()->getContextId())) {
            $this
                ->contextHandlerCollection
                ->get($golemResponse->getCall()->getContextId())
                ->handleResponse($message, $golemResponse);
        }
    }

    private function messageNotUnderstood(string $fbid)
    {
        $messages = [];
        $messages[] = [
            'type' => MessageFormatterAliases::TEXT,
            'text' => $this->translator->trans('not_understood', [], null),
        ];
        $messages[] = [
            'type' => MessageFormatterAliases::TEXT,
            'text' => $this->translator->trans('bot_is_able_to', [], null),
        ];

        foreach ($messages as $message) {
            $this
                ->messengerApi
                ->setRecipient($fbid)
                ->setTyping('on');
            sleep(1);
            $this
                ->messengerApi
                ->sendMessage(
                    $this
                        ->messageFormatterCollection
                        ->get($message['type'])
                        ->format($message)
                )
                ->setTyping('off');
        }
    }
}