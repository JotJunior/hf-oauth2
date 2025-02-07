<?php

namespace Jot\HfShield\Controller;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\RateLimit\Annotation\RateLimit;
use Hyperf\Swagger\Annotation as SA;
use Jot\HfShield\Annotation\Scope;
use Jot\HfShield\Entity\User\User;
use Jot\HfShield\Middleware\CheckCredentials;
use Jot\HfShield\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use function Hyperf\Support\make;

#[SA\HyperfServer('http')]
#[SA\Tag(
    name: 'User',
    description: 'Endpoints related to users management'
)]
#[SA\Schema(schema: "app.error.response", required: ["result", "error"],
    properties: [
        new SA\Property(property: "result", type: "string", example: "error"),
        new SA\Property(property: "error", type: "string", example: "Error message"),
        new SA\Property(property: "data", type: "string|array", example: null),
    ],
    type: "object"
)]
#[Controller(prefix: '/oauth')]
class UserController extends AbstractController
{

    protected string $repository = UserRepository::class;

    #[SA\Post(
        path: "/oauth/users",
        description: "Create a new users.",
        summary: "Create a New User",
        requestBody: new SA\RequestBody(
            required: true,
            content: new SA\JsonContent(ref: "#/components/schemas/jot.hfshield.entity.user.user")
        ),
        tags: ["User"],
        responses: [
            new SA\Response(
                response: 201,
                description: "User created",
                content: new SA\JsonContent(ref: "#/components/schemas/jot.hfshield.entity.user.user")
            ),
            new SA\Response(
                response: 400,
                description: "Bad request",
                content: new SA\JsonContent(ref: "#/components/schemas/app.error.response")
            ),
            new SA\Response(
                response: 500,
                description: "Application error",
                content: new SA\JsonContent(ref: "#/components/schemas/app.error.response")
            )
        ]
    )]
    #[Scope(allow: 'oauth:user:create')]
    #[RateLimit(create: 1, capacity: 2)]
    #[Middleware(CheckCredentials::class)]
    public function createUser(): PsrResponseInterface
    {
        $userData = $this->request->all();
        $user = make(User::class, ['data' => $userData]);

        return $this->saveUser($user);
    }

    private function saveUser(User $user): PsrResponseInterface
    {
        $createdUser = $this->repository()->create($user);
        $userData = $createdUser
            ->hide(['password_salt', 'password'])
            ->toArray();
        return $this->response->json($userData);
    }

    #[SA\Put(
        path: "/oauth/users/{id}",
        description: "Update the details of an existing users.",
        summary: "Update an existing User",
        requestBody: new SA\RequestBody(
            required: true,
            content: new SA\JsonContent(ref: "#/components/schemas/jot.hfshield.entity.user.user")
        ),
        tags: ["User"],
        parameters: [
            new SA\Parameter(
                name: "id",
                description: "Unique identifier of the users",
                in: "path",
                required: true,
                schema: new SA\Schema(type: "string", example: "12345")
            )
        ],
        responses: [
            new SA\Response(
                response: 200,
                description: "User Updated",
                content: new SA\JsonContent(ref: "#/components/schemas/jot.hfshield.entity.user.user")
            ),
            new SA\Response(
                response: 400,
                description: "Bad Request",
                content: new SA\JsonContent(ref: "#/components/schemas/app.error.response")
            ),
            new SA\Response(
                response: 404,
                description: "User Not Found",
                content: new SA\JsonContent(ref: "#/components/schemas/app.error.response")
            ),
            new SA\Response(
                response: 500,
                description: "Application error",
                content: new SA\JsonContent(ref: "#/components/schemas/app.error.response")
            )
        ]
    )]
    #[Scope(allow: 'oauth:user:update')]
    #[RateLimit(create: 1, capacity: 2)]
    #[Middleware(CheckCredentials::class)]
    public function updateUser(string $id): PsrResponseInterface
    {
        $userData = $this->request->all();
        $userData['id'] = $id;
        $user = make(User::class, ['data' => $userData]);
        $user->setEntityState('update');
        if (!$user->validate()) {
            return $this->response->withStatus(400)->json($user->getErrors());
        }
        $updatedUser = $this->repository()->update($user);
        return $this->response->json($updatedUser->toArray());
    }

}