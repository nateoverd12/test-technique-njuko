<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' =>'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '8889',
                    'user'     => 'root',
                    'password' => 'root',
                    'dbname'   => 'data',
                )
            )
        )
    )
);