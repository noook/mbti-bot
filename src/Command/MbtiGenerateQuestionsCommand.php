<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\MbtiQuestion;
use App\Repository\MbtiQuestionRepository;
use Symfony\Component\Yaml\Yaml;

class MbtiGenerateQuestionsCommand extends Command
{
    protected static $defaultName = 'mbti:generate:questions';

    private $path;
    private $em;
    private $mbtiQuestionRepository;
    private $translationsPath;

    public function __construct(ParameterBagInterface $params, MbtiQuestionRepository $mbtiQuestionRepository, ObjectManager $em)
    {
        parent::__construct();
        $this->path = $params->get('kernel.project_dir') . '/resources/mbti-questions.json';
        $this->translationsPath = $params->get('kernel.project_dir') . '/translations';
        $this->mbtiQuestionRepository = $mbtiQuestionRepository;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Loads questions into the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questions = json_decode(file_get_contents($this->path), true)['questions'];

        foreach ($this->mbtiQuestionRepository->findAll() as $item) {
            $this->em->remove($item);
        }
        $this->em->flush();

        $step = 1;
        $translations = [];

        foreach ($questions as $pair) {
            foreach ($pair as $question) {
                $item = (new MbtiQuestion())
                    ->setStep($step)
                    ->setKey($question['position'])
                    ->setValue($question['value']);
                $this->em->persist($item);

                foreach ($question['label'] as $lang => $value) {
                    $translations[$lang][$step][$question['position']] = $value;
                }
            }
            ++$step;
        }
        $this->em->flush();

        foreach ($translations as $lang => $dict) {
            file_put_contents($this->translationsPath . "/mbti-questions." . $lang . ".yml", Yaml::dump($dict));
        }
    }
}
