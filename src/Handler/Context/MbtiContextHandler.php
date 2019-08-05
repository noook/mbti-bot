<?php

namespace App\Handler\Context;

use Symfony\Component\Translation\TranslatorInterface;
use App\Collection\InteractionHandlerCollection;
use App\Collection\ContextHandlerCollection;
use App\Collection\MessageFormatterCollection;
use App\Formatter\MessageFormatterAliases;
use App\Golem\GolemManager;
use App\Golem\GolemResponse;
use App\Messenger\MessengerApi;
use App\Messenger\MessengerRequestMessage;

class MbtiContextHandler implements ContextHandlerInterface
{
    private $interactionHandlerCollection;
    private $golemManager;
    private $contextHandlerCollection;
    private $messengerApi;
    private $messageFormatterCollection;
    private $translator;

    public function __construct(
        InteractionHandlerCollection $interactionHandlerCollection,
        GolemManager $golemManager,
        ContextHandlerCollection $contextHandlerCollection,
        MessengerApi $messengerApi,
        MessageFormatterCollection $messageFormatterCollection,
        TranslatorInterface $translator
    )
    {
        $this->interactionHandlerCollection = $interactionHandlerCollection;
        $this->golemManager = $golemManager;
        $this->contextHandlerCollection = $contextHandlerCollection;
        $this->messengerApi = $messengerApi;
        $this->messageFormatterCollection = $messageFormatterCollection;
        $this->translator = $translator;
    }

    public function getAlias(): string
    {
        return ContextHandlerAliases::MBTI;
    }

    public function handleResponse(MessengerRequestMessage $messengerRequest, GolemResponse $golemResponse)
    {
        $interactionId = $golemResponse->getCall()->getInteractionId();

        if ($this->interactionHandlerCollection->has($interactionId)) {
            $this
                ->interactionHandlerCollection
                ->get($interactionId)
                ->handleInteraction($messengerRequest, $golemResponse);
        } else {
            $fbid = $messengerRequest->getSender();
            $message = [
                'type' => MessageFormatterAliases::TEXT,
                'text' => $this->translator->trans('not_understood', [], null),
            ];
            return $this
                ->messengerApi
                ->setRecipient($fbid)
                ->sendMessage(
                    $this
                        ->messageFormatterCollection
                        ->get($message['type'])
                        ->format($message)
                );
        }
    }
}