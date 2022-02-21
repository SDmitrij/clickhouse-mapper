<?php

namespace Mapper\Entity;

interface EntityManagerInterface
{
    public function attach($entity): void;

    public function release(): void;

    public function getBuffer(): array;
}
