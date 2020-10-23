<?php


namespace InnoShop\Kernel\Db;


interface DatabaseTruncateInterface
{
    function apply(): void;
}