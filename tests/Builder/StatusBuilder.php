<?php

namespace Mapper\Test\Builder;

use Entity\Status;

class StatusBuilder
{
    public function build(): Status
    {
        return new Status(
            0,
            1,
            0,
            0,
            0,
            0,
            0,
            0
        );
    }
}