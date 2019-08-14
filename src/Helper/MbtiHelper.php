<?php

namespace App\Helper;

use Symfony\Component\Translation\TranslatorInterface;
use App\Collection\MessageFormatterCollection;
use App\Entity\FacebookUser;
use App\Entity\MbtiAnswer;
use App\Entity\MbtiTest;
use App\Formatter\MessageFormatterAliases;
use App\Handler\QuickReply\QuickReplyDomainAliases;
use App\Messenger\MessengerApi;
use App\Repository\FacebookUserRepository;
use App\Repository\MbtiAnswerRepository;
use App\Repository\MbtiQuestionRepository;
use App\Repository\MbtiTestRepository;

class MbtiHelper
{
    const TEST_LENGTH = 44;
    const EMOJI_VOTE = ['🥝', '🍉', '🍎', '🍑', '🍍', '🍌', '🥥', '🍒'];
    const DICHOTOMIES = ['I', 'E', 'N', 'S', 'T', 'F', 'J', 'P'];
    const DICHOTOMY_PAIRS = [['I', 'E'], ['N', 'S'], ['T', 'F'], ['J', 'P']];

    private $facebookUserRepository;
    private $mbtiQuestionRepository;
    private $mbtiTestRepository;
    private $mbtiAnswerRepository;
    private $messengerApi;
    private $messageFormatterCollection;
    private $translator;

    public function __construct(
        FacebookUserRepository $facebookUserRepository,
        MbtiQuestionRepository $mbtiQuestionRepository,
        MbtiTestRepository $mbtiTestRepository,
        MbtiAnswerRepository $mbtiAnswerRepository,
        MessengerApi $messengerApi,
        MessageFormatterCollection $messageFormatterCollection,
        TranslatorInterface $translator
    )
    {
        $this->facebookUserRepository = $facebookUserRepository;
        $this->mbtiQuestionRepository = $mbtiQuestionRepository;
        $this->mbtiTestRepository = $mbtiTestRepository;
        $this->mbtiAnswerRepository = $mbtiAnswerRepository;
        $this->messengerApi = $messengerApi;
        $this->messageFormatterCollection = $messageFormatterCollection;
        $this->translator = $translator;
    }

    public function startTest(FacebookUser $user): MbtiTest
    {
        $test = $this->mbtiTestRepository->currentTest($user);

        if (null === $test) {
            $test = $this->mbtiTestRepository->createTest($user);

            $startMessages = ['lets_start_test', 'test_is_44_questions_long', 'answer_like_this'];
            foreach ($startMessages as $message) {
                $item = [
                    'type' => MessageFormatterAliases::TEXT,
                    'text' => $this->translator->trans($message, [], null, $user->getLocale()),
                ];
                $this
                    ->messengerApi
                    ->setRecipient($user->getFbid())
                    ->setTyping('on');
                sleep(1);
                $this
                    ->messengerApi
                    ->sendMessage(
                        $this
                            ->messageFormatterCollection
                            ->get($item['type'])
                            ->format($item)
                    )
                    ->setTyping('off');
            }
        }

        return $test;
    }

    /**
     * @param MbtiTest $test
     * @return MbtiQuestion[]
     */
    public function getNextQuestion(MbtiTest $test): array
    {
        $answer = $this->mbtiAnswerRepository->findOneBy([
            'step' => $test->getStep(),
            'test' => $test,
        ]);
        return $this->mbtiQuestionRepository->findBy(['step' => $answer->getQuestion()]);
    }

    /**
     * @param MbtiQuestion[] $questions
     */
    public function prepareQuestion(array $questions, FacebookUser $user): array
    {
        $emojis = self::EMOJI_VOTE;
        shuffle($emojis);
        $emojis = array_slice($emojis, 0, 2);
        $test = $this->mbtiTestRepository->currentTest($user);

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
        $answer = $this->mbtiAnswerRepository->findOneBy([
            'step' => $test->getStep(),
            'test' => $test,
        ]);
        $text = implode("\n", [
            $this->translator->trans('question_x_of_y', ['{step}' => $answer->getStep()], null, $user->getLocale()),
            $emojis[0]. ' ' . $this->translator->trans($questions[0]->getStep() . '.' . $questions[0]->getKey(), [], 'mbti-questions', $user->getLocale()),
            '',
            $emojis[1]. ' ' . $this->translator->trans($questions[1]->getStep() . '.' . $questions[1]->getKey(), [], 'mbti-questions', $user->getLocale()),
        ]);
        // shuffle($payloads);

        return [
            'type' => MessageFormatterAliases::QUICK_REPLY,
            'text' => $text,
            'quick_replies' => array_map(function ($item) use ($test) {
                return [
                    'title' => $item['emoji'],
                    'payload' => \json_encode([
                        'domain' => QuickReplyDomainAliases::MBTI_DOMAIN,
                        'type' => 'answer',
                        'step' => $test->getStep(),
                        'value' => $item['question']->getValue(),
                    ]),
                ];
            }, $payloads),
        ];
    }

    public function answerQuestion(string $fbid, array $reply): ?array
    {
        $user = $this->facebookUserRepository->findOneBy(['fbid' => $fbid]);
        $test = $this->mbtiTestRepository->findOneBy([
            'user' => $user,
            'completed' => false,
        ]);
        $answer = $this->mbtiAnswerRepository->findOneBy([
            'step' => $test->getStep(),
            'test' => $test,
        ]);
        $answer->setValue($reply['value']);

        $this->mbtiAnswerRepository->saveAnswer($answer);
        $this->mbtiTestRepository->nextStep($test);

        if (true === $test->getCompleted()) {
            $this->completeTest($test);
            return null;
        }

        $questions = $this->getNextQuestion($test);

        return $this->prepareQuestion($questions, $user);
    }

    public function completeTest(MbtiTest $test)
    {
        $this->calculate($test);
    }

    private function calculate(MbtiTest $test)
    {
        $answers = $test->getAnswers()->toArray();
        $keys = array_reduce(self::DICHOTOMIES, function (array $carry, string $dichotomy) {
            $carry[$dichotomy] = 0;
            return $carry;
        }, []);
        $results = array_reduce($answers, function (array $carry, MbtiAnswer $answer) {
            $carry[$answer->getValue()] += 1;
            return $carry;
        }, $keys);

        $type = '';

        foreach (self::DICHOTOMY_PAIRS as $pair) {
            $scores = array_filter($results, function (string $dichotomy) use ($pair) {
                return in_array($dichotomy, $pair);
            }, ARRAY_FILTER_USE_KEY);
            $type .= array_search(max($scores), $scores);;
        }

        $test->setResult($type);

        $this->mbtiTestRepository->saveEndResults($test, $results);
    }
}
