<?php
return [
    "default" => "development",

    "connections" => [
        "development" => [
            "driver"      => "mysql",
            "dbname"      => "SPMM",
            "host"        => "172.17.0.2",
            "port"        => "3306",
            "username"    => "root",
            "password"    => "123456",
            "charset"     => "utf8",
            "collation"   => "utf8mb4_unicode_ci",
            "prefix"      => "",
            "strict"      => false,
            "engine"      => null,
        ],
    ],

    "errmode" => [
        "silent" => PDO::ERRMODE_SILENT,
        "warning" => PDO::ERRMODE_WARNING,
        "exception" => PDO::ERRMODE_EXCEPTION,
    ],
    
    "case" => [
        "natural" => PDO::CASE_NATURAL,
        "upper" => PDO::CASE_UPPER,
        "lower" => PDO::CASE_LOWER,
    ],

    "fetch" => [
        "assoc" => PDO::FETCH_ASSOC,
        "obj" => PDO::FETCH_OBJ,
    ],

    "nulls" => [
        "natural" => PDO::NULL_NATURAL,
        "empty" => PDO::NULL_EMPTY_STRING,
        "to_string" => PDO::NULL_TO_STRING,
    ],    
];
