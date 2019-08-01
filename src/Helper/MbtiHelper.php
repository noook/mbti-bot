<?php

namespace App\Helper;

use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\FacebookUser;
use App\Entity\MbtiAnswer;
use App\Entity\MbtiTest;
use App\Formatter\MessageFormatterAliases;
use App\Handler\QuickReply\QuickReplyDomainAliases;
use App\Repository\FacebookUserRepository;
use App\Repository\MbtiAnswerRepository;
use App\Repository\MbtiQuestionRepository;
use App\Repository\MbtiTestRepository;

class MbtiHelper
{
    const EMOJI_VOTE = ['🥝', '🍉', '🍎', '🍑', '🍍'];

    private $facebookUserRepository;
    private $mbtiQuestionRepository;
    private $mbtiTestRepository;
    private $mbtiAnswerRepository;
    private $translator;

    public function __construct(
        FacebookUserRepository $facebookUserRepository,
        MbtiQuestionRepository $mbtiQuestionRepository,
        MbtiTestRepository $mbtiTestRepository,
        MbtiAnswerRepository $mbtiAnswerRepository,
        TranslatorInterface $translator
    )
    {
        $this->facebookUserRepository = $facebookUserRepository;
        $this->mbtiQuestionRepository = $mbtiQuestionRepository;
        $this->mbtiTestRepository = $mbtiTestRepository;
        $this->mbtiAnswerRepository = $mbtiAnswerRepository;
        $this->translator = $translator;
    }

    /**
     * @param MbtiTest $test
     * @return MbtiQuestion[]
     */
    public function getNextQuestion(MbtiTest $test): array
    {
        return $this->mbtiQuestionRepository->findBy(['step' => $test->getStep()]);
    }

    /**
     * @param MbtiQuestion[] $questions
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
            $this->translator->trans('question_x_of_y', ['{step}' => $questions[0]->getStep()], null, $user->getLocale()),
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
                        'domain' => QuickReplyDomainAliases::MBTI_DOMAIN,
                        'type' => 'answer',
                        'step' => $item['question']->getStep(),
                        'value' => $item['question']->getValue(),
                    ]),
                ];
            }, $payloads),
        ];
    }

    public function answerQuestion(string $fbid, string $value): array
    {
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $fbid]);
        $test = $this->mbtiTestRepository->findOneBy([
            'user' => $user,
            'completed' => false,
        ]);
        $answer = (new MbtiAnswer())
            ->setStep($test->getStep())
            ->setTest($test)
            ->setValue($value);

        $this->mbtiAnswerRepository->saveAnswer($answer);
        $this->mbtiTestRepository->nextStep($test);

        $questions = $this->getNextQuestion($test);

        return $this->prepareQuestion($questions, $user);
    }
}
