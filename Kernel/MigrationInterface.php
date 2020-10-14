<?php


namespace InnoShop\Kernel;


interface MigrationInterface
{
    function getId(): string;
    function getDescription(): string;
    function migrate(): void;
}