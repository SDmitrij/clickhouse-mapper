<?php

namespace Mapper\Test\Builder;

use DateTime;
use Entity\Order;
use Exception;
use Generator;

class OrderBuilder
{
    /**
     * @throws Exception
     */
    public function build(int $numOfOrders = 10): Generator
    {
        for ($i = 0; $i < $numOfOrders; ++$i) {
            yield new Order(
                new DateTime('-5 days'),
                new DateTime(),
                random_int(1, 100),
                random_int(1, 100),
                random_int(1, 100),
                random_int(1, 100),
                (new StatusBuilder())->build()
            );
        }
    }
}
