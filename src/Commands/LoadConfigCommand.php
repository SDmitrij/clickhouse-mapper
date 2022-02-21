<?php

namespace Mapper\Commands;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

class LoadConfigCommand extends Command
{
    private PhpFilesAdapter $cache;

    public function __construct(PhpFilesAdapter $cache)
    {
        parent::__construct();
        $this->cache = $cache;
    }

    protected function configure()
    {
        $this
            ->setName('mapper:load-conf')
            ->setDescription('Load config from external .yaml file');

        $this->addArgument('path', InputArgument::REQUIRED, 'Path to .yaml config');
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        /** @var CacheItem $configItem */
        $configItem = $this->cache->getItem('config');

        $config = $this->processYamlConfig($path);
        $configItem->set($config);

        $this->cache->save($configItem);
    }

    private function validateConfig(array $config): void
    {
        Assert::keyExists($config, 'mapper');
        Assert::keyExists($config['mapper'], 'connection');

        $clickHouseSection = $config['mapper']['connection'];

        Assert::keyExists($clickHouseSection, 'host');
        Assert::keyExists($clickHouseSection, 'port');
        Assert::keyExists($clickHouseSection, 'username');
        Assert::keyExists($clickHouseSection, 'password');
        Assert::keyExists($clickHouseSection, 'database');
    }

    private function processYamlConfig(string $path): array
    {
        $conf = Yaml::parseFile($path);
        $this->validateConfig($conf);

        return $conf;
    }
}
