<?php

namespace Mapper\Metadata\Visitor;

class CommonInfoVisitor implements MetadataVisitorInterface
{
    public function visit($data): CommonInfoPlain
    {
        return new CommonInfoPlain($data['common']['table'], $data['common']['view']);
    }
}