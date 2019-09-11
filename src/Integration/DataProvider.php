<?php

namespace Acme\Integration;


class DataProvider implements DataProviderInterface
{
    private $host;
    private $user;
    private $password;

    public function __construct(string $host, string $user, string $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Request $request): array
    {
        throw new \RuntimeException('implement me please.');
    }
}