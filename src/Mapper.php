<?php

namespace Mapper;

require_once dirname(__DIR__).'/utils/autoload-file-searcher.php';
require_once getAutoloadFile();

use Mapper\Entity\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class Mapper
{
    private static ?ContainerInterface $container = null;

    public static function getEntityManager(): EntityManagerInterface
    {
        if (null === self::$container) {
            self::$container = ContainerBuilder::build();
        }

        return self::$container->get(EntityManagerInterface::class);
    }
}
