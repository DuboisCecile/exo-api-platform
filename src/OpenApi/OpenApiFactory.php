<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\RequestBody;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        /** @var PathItem path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() === "hidden") {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            };
        }
        // $openApi->getPaths()->addPath('/ping', new PathItem(null, 'Ping', null, new Operation('ping-id', [], [], 'Répond')));

        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['cookieAuth'] = new \ArrayObject([
            'type' => 'apiKey',
            'in' => 'cookie',
            'name' => 'PHPSESSID',
        ]);
        // $openApi = $openApi->withSecurity(['cookieAuth' => ['']]);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'john@doe.fr'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '0000'
                ],
            ],
        ]);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'PostApiLogin',
                tags: ['Auth'], // on aurait aussi pu mettre ['User'] si on voulait que cette route apparaisse avec les autres routes de User, et pas à part
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ]
                        ]
                    ]),
                ),
                responses: [
                    '200' => [
                        'description' => 'Utilisateur connecté',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User-read.User',
                                ],
                            ],
                        ],
                    ]
                ]
            )
        );
        $openApi->getPaths()->addPath('/api/login', $pathItem);
        return $openApi;
    }
}
