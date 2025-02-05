<?php

declare(strict_types=1);

namespace Jot\HfOAuth2\Entity\AccessToken;

use Jot\HfRepository\Entity;
use Jot\HfRepository\Trait\HasTimestamps;
use Jot\HfRepository\Trait\HasLogicRemoval;
use Hyperf\Swagger\Annotation as SA;

#[SA\Schema(schema: "jot.hfoauth2.entity.accesstoken.client")]
class Client extends Entity
{

    

        #[SA\Property(
        property: "id",
        type: "string",
        example: ""
    )]
    protected ?string $id = null;

    #[SA\Property(
        property: "name",
        type: "string",
        example: ""
    )]
    protected ?string $name = null;

    #[SA\Property(
        property: "redirect_uri",
        type: "string",
        example: ""
    )]
    protected ?string $redirectUri = null;



}
