<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Messenger\FacebookApi;

class SetupBotProfileCommand extends Command
{
    protected static $defaultName = 'messenger:profile:update';

    private $facebookApi;

    public function __construct(FacebookApi $facebookApi)
    {
        parent::__construct();
        $this->facebookApi = $facebookApi;
    }

    protected function configure()
    {
        $this
            ->setDescription("Sets up the Messenger bot's profile")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $success = $this->facebookApi->updateBotProfile();

        if ($success === true) {
            $io->success('Bot profile updated !');
        } else {
            $io->error("An error occurred while updated the bot's profile.");
        }
    }
}
