<?php


namespace InnoShop\Plugins\Core\JtlConnector;


use DI\Container;
use Jtl\Connector\Core\Authentication\TokenValidatorInterface;
use Jtl\Connector\Core\Connector\ConnectorInterface;
use Jtl\Connector\Core\Mapper\PrimaryKeyMapperInterface;
use Noodlehaus\ConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Connector implements ConnectorInterface
{
    protected PrimaryKeyMapperInterface $primaryKeyMapper;
    protected TokenValidatorInterface $tokenValidator;

    public function __construct(PrimaryKeyMapperInterface $primaryKeyMapper, TokenValidatorInterface $tokenValidator)
    {
        $this->primaryKeyMapper = $primaryKeyMapper;
        $this->tokenValidator = $tokenValidator;
    }

    public function initialize(ConfigInterface $config, Container $container, EventDispatcher $dispatcher): void
    {

    }

    public function getPrimaryKeyMapper(): PrimaryKeyMapperInterface
    {
        return $this->primaryKeyMapper;
    }

    public function getTokenValidator(): TokenValidatorInterface
    {
        return $this->tokenValidator;
    }

    public function getControllerNamespace(): string
    {
        return 'InnoShop\Plugins\Core\JtlConnector\Controller';
    }

    public function getEndpointVersion(): string
    {
        return '0.0.1';
    }

    public function getPlatformVersion(): string
    {
        return '';
    }

    public function getPlatformName(): string
    {
        return 'bulk';
    }
}