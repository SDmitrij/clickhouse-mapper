<?php

namespace Mapper\Metadata;

use Mapper\Metadata\Visitor\MetadataVisitorInterface;

interface MetadataManagerInterface
{
    public function loadFor(string $entityClass): void;

    public function get();

    public function accept(MetadataVisitorInterface $metadataVisitor): self;
}
