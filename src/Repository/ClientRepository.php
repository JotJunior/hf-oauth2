<?php

declare(strict_types=1);

namespace Jot\HfShield\Repository;

use Jot\HfShield\Entity\Client\Client;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Jot\HfShield\Entity\ClientEntity;

class ClientRepository extends AbstractRepository implements ClientRepositoryInterface
{

    private const HASH_ALGORITHM = 'sha256';

    use CryptTrait;

    protected string $entity = Client::class;

    public function getClientEntity(string $clientIdentifier): ?ClientEntityInterface
    {
        /** @var Client $client */
        $client = $this->find($clientIdentifier);

        if (empty($client)) {
            return null;
        }

        $clientData = $client->toArray();

        return (new ClientEntity())
            ->setIdentifier($clientData['id'])
            ->setName($clientData['name'])
            ->setRedirectUri($clientData['redirect_uri']);
    }


    public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType): bool
    {
        $foundClient = $this->find($clientIdentifier);

        if ($foundClient === null) {
            return false;
        }

        return $this->isClientSecretValid($foundClient->getSecret(), $clientSecret);
    }

    private function isClientSecretValid(?string $storedSecret, string $providedSecret): bool
    {
        if ($storedSecret === null) {
            return false;
        }

        $hashedProvidedSecret = hash_hmac(self::HASH_ALGORITHM, $providedSecret, $this->config['encryption_key']);
        return hash_equals($storedSecret, $hashedProvidedSecret);
    }
}
