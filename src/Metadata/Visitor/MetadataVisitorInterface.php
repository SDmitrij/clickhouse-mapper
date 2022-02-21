<?php

namespace Mapper\Metadata\Visitor;

interface MetadataVisitorInterface
{
    /**
     * @return CommonInfoPlain|PropertyInfoPlain
     */
    public function visit($data);
}
