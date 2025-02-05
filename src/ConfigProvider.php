<?php

namespace Jot\HfOAuth2;

use Jot\HfOAuth2\Aspect\ScopeAspect;
use Jot\HfOAuth2\Command\OAuthScopeCommand;
use Jot\HfOAuth2\Command\OAuthUserCommand;
use Jot\HfOAuth2\Exception\Handler\AuthExceptionHandler;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;

class ConfigProvider
{

    public function __invoke(): array
    {
        return [
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'listeners' => [
                AllowedScopesListener::class,
            ],
            'dependencies' => [
                AuthorizationServer::class => AuthorizationServerFactory::class,
                ResourceServer::class => ResourceServerFactory::class
            ],
            'commands' => [
                OAuthScopeCommand::class,
                OAuthUserCommand::class,
            ],
            'exceptions' => [
                'handler' => [
                    'http' => [
                        AuthExceptionHandler::class,
                    ]
                ]
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for hf_oauth2.',
                    'source' => __DIR__ . '/../publish/hf_oauth2.php',
                    'destination' => BASE_PATH . '/config/autoload/hf_oauth2.php',
                ],
                [
                    'id' => 'migrations',
                    'description' => 'The elasticsearch migration files for hf_oauth2.',
                    'source' => __DIR__ . '/../migrations',
                    'destination' => BASE_PATH . '/migrations',
                ],
            ],
        ];
    }

}