<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mapper\Helper;

use JsonException;

class JsonHelper
{
    /**
     * @throws JsonException
     */
    public static function toJson(array $data): string
    {
        return json_encode(
            $data,
            JSON_THROW_ON_ERROR | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION | JSON_ERROR_UTF8
        );
    }
}
