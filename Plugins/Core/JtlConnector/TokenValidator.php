<?php


namespace InnoShop\Plugins\Core\JtlConnector;


use Jtl\Connector\Core\Authentication\TokenValidatorInterface;

class TokenValidator implements TokenValidatorInterface
{
    protected string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function validate(string $token): bool
    {
        return hash_equals($this->token, $token);
    }
}