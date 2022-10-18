<?php

declare(strict_types=1);

namespace Mothership\HeadlessShopwareVarnishCacheBundle\Command;

use Mothership\HeadlessShopwareVarnishCacheBundle\Cache\GatewayInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function explode;

class InvalidationCommand extends Command
{
    protected static $defaultName = 'varnish:invalidate';
    private GatewayInterface $gateway;

    public function __construct(GatewayInterface $gateway)
    {
        parent::__construct();
        $this->gateway = $gateway;
    }

    protected function configure(): void
    {
        $this->setDescription('Invalidates the Varnish cache based on tag or regex');
        $this->addOption('tags', 't', InputOption::VALUE_REQUIRED, 'Comma separated tags to clear');
        $this->addOption('regex', 'r', InputOption::VALUE_REQUIRED, 'Regex to match URLs which should be flushed');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!empty($input->getOption('regex'))) {
            $this->gateway->ban($input->getOption('regex'));
        } elseif (!empty($input->getOption('tags'))) {
            $cacheTags = explode(',', $input->getOption('tags'));
            $this->gateway->invalidate($cacheTags);
        } else {
            $this->gateway->invalidate(['all']);
        }

        return Command::SUCCESS;
    }
}
