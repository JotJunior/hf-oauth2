<?php

namespace Jot\HfOAuth2\Repository;

use Hyperf\Contract\ConfigInterface;
use Jot\HfRepository\Repository;
use League\OAuth2\Server\CryptTrait;
use Psr\Container\ContainerInterface;

class AbstractRepository extends Repository
{
    use CryptTrait;

    protected array $config = [];


    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(ConfigInterface::class)->get('hf_oauth2', []);
        $this->setEncryptionKey($this->config['encryption_key']);
    }

}