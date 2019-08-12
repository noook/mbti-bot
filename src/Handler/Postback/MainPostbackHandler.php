<?php

namespace App\Handler\Postback;

use Symfony\Component\Translation\TranslatorInterface;
use App\Collection\MessageFormatterCollection;
use App\Formatter\MessageFormatterAliases;
use App\Handler\QuickReply\QuickReplyDomainAliases;
use App\Helper\BasicMessages;
use App\Messenger\MessengerApi;
use App\Messenger\MessengerRequestMessage;
use App\Repository\FacebookUserRepository;

class MainPostbackHandler implements PostbackDomainInterface
{
    private $facebookUserRepository;
    private $messageFormatterCollection;
    private $messengerApi;
    private $translator;
    private $basicMessageHelper;

    public function __construct(
        FacebookUserRepository $facebookUserRepository,
        MessageFormatterCollection $messageFormatterCollection,
        MessengerApi $messengerApi,
        TranslatorInterface $translator,
        BasicMessages $basicMessageHelper
    )
    {
        $this->facebookUserRepository = $facebookUserRepository;
        $this->messageFormatterCollection = $messageFormatterCollection;
        $this->messengerApi = $messengerApi;
        $this->translator = $translator;
        $this->basicMessageHelper = $basicMessageHelper;
    }

    public function getAlias(): string
    {
        return PostbackDomainAliases::MAIN_DOMAIN;
    }

    public function handleReply(MessengerRequestMessage $message)
    {
        $this->basicMessageHelper->greet($message);
    }
}