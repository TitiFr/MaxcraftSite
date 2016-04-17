<?php


namespace Maxcraft\DefaultBundle\Service;


use NathemWS\NathemWSC;

class SymfonyWSC extends NathemWSC
{
    function __construct($key)
    {
        parent::__construct('localhost:7689', $key, uniqid());
    }


}