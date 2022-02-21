<?php

require_once dirname(__DIR__).'/utils/autoload-file-searcher.php';
require_once getAutoloadFile();

use Mapper\Commands\LoadConfigCommand;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Console\Application;

$cacheDir = dirname(__DIR__).'/mapper-cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir);
}

$cli = new Application();
$cli->add(
    new LoadConfigCommand(
        new PhpFilesAdapter(
            'mapper.common-cache',
            0,
            $cacheDir,
            false
        )
    )
);

$cli->run();
