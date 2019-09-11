<?php


namespace Acme\Integration;


interface DataProviderInterface
{
    public function get(Request $request): array;
}