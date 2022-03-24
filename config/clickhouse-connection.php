<?php

use ClickHouseDB\Client;

return static function(array $yamlConf): Client {
    return new Client(
        [
            'host' => $yamlConf['mapper']['connection']['host'],
            'port' => $yamlConf['mapper']['connection']['port'],
            'username' => $yamlConf['mapper']['connection']['username'],
            'password' => $yamlConf['mapper']['connection']['password'],
        ],
        ['database' => $yamlConf['mapper']['connection']['database']]
    );
};