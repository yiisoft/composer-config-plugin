<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Command;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Composer\Config\Builder;

final class RebuildCommand extends Command
{
    private ?SymfonyStyle $io = null;

    protected static $defaultName = 'config/rebuild';

    public function __construct() 
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setDescription('Rebuild Yii configuration.')
            ->addOption('baseDir', null, InputOption::VALUE_OPTIONAL, 'Base directory', null)
            ->setHelp('This command rebuild Yii configuration..');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $baseDir = $input->getOption('baseDir');
        Builder::rebuild($baseDir);
        $this->io()->success('Rebuild Yii configuration successfully.');
        return ExitCode::OK;
    }

    private function io(): SymfonyStyle
    {
        if ($this->io === null) {
            $this->io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());
        }
        return $this->io;
    }
}