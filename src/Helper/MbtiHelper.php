<?php

namespace App\Helper;

use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\FacebookUser;
use App\Entity\MbtiTest;
use App\Formatter\MessageFormatterAliases;
use App\Repository\MbtiQuestionRepository;
use App\Repository\MbtiTestRepository;

class MbtiHelper
{
    const EMOJI_VOTE = ['🥝', '🍉', '🍎', '🍑', '🍍'];

    private $mbtiQuestionRepository;
    private $mbtiTestRepository;
    private $translator;

    public function __construct(
        MbtiQuestionRepository $mbtiQuestionRepository,
        MbtiTestRepository $mbtiTestRepository,
        TranslatorInterface $translator
    )
    {
        $this->mbtiQuestionRepository = $mbtiQuestionRepository;
        $this->mbtiTestRepository = $mbtiTestRepository;
        $this->translator = $translator;
    }

    public function getNextQuestion(MbtiTest $test)
    {
        return $this->mbtiQuestionRepository->findBy(['step' => $test->getStep()]);
    }

    /**
     * @param MbtiQuestion[] $questions
     * @return void
     */
    public function prepareQuestion(array $questions, FacebookUser $user): array
    {
        $emojis = self::EMOJI_VOTE;
        shuffle($emojis);
        $emojis = array_slice($emojis, 0, 2);

        $payloads = [
            [
                'emoji' => $emojis[0],
                'question' => $questions[0],
            ],
            [
                'emoji' => $emojis[1],
                'question' => $questions[1],
            ],
        ];
        $text = implode("\n", [
            $emojis[0]. ' ' . $this->translator->trans($questions[0]->getStep() . '.' . $questions[0]->getKey(), [], 'mbti', $user->getLocale()),
            '',
            $emojis[1]. ' ' . $this->translator->trans($questions[1]->getStep() . '.' . $questions[1]->getKey(), [], 'mbti', $user->getLocale()),
        ]);
        shuffle($payloads);

        return [
            'type' => MessageFormatterAliases::QUICK_REPLY,
            'text' => $text,
            'quick_replies' => array_map(function ($item) {
                return [
                    'title' => $item['emoji'],
                    'payload' => \json_encode([
                        'step' => $item['question']->getStep(),
                        'value' => $item['question']->getValue(),
                    ]),
                ];
            }, $payloads),
        ];
    }
}