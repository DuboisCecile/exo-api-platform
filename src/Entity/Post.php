<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PostRepository;
use App\Controller\PostPublishController;
use App\Controller\PostCountController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    normalizationContext: [
        'groups' => ['read:collection'],
        'openapi_definition_name' => 'Collection'
    ],
    denormalizationContext: ['groups' => ['write:Post']],
    paginationItemsPerPage: 2,
    paginationMaximumItemsPerPage: 2,
    paginationClientItemsPerPage: true,
    collectionOperations: [
        'get' => [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'post',
        'count' => [
            'method' => 'GET',
            'path' => '/posts/count',
            'controller' => PostCountController::class,
            'read' => false,
            'filters' => [],
            'pagination_enabled' => false,
            'openapi_context' => [
                'summary' => 'Get the number of posts',
                'parameters' => [
                    'online' => [
                        'name' => 'online',
                        'in' => 'query',
                        'description' => 'Filtre les articles avec la valeur online suivante (0 ou 1)',
                        'required' => false,
                        'schema' => [
                            'type' => 'integer',
                            'maximum' => 1,
                            'minimum' => 0
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'The number of posts',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'integer',
                                    'example' => 3,
                                ],
                            ],
                        ],
                    ],
                ]
            ],
        ]
    ],


    itemOperations: [
        'put',
        'delete',
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection', 'read:item', 'read:Post'],
                'openapi_definition_name' => 'Detail'
            ]
        ],
        'publish' => [
            'method' => 'POST',
            'path' => '/posts/{id}/publish',
            'controller' => PostPublishController::class,
            'openapi_context' => [
                'summary' => 'Publish a post',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [],
                            // 'example' => [],
                        ],
                    ],
                ],
            ],
        ],
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial'])]

class Post
{

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["read:collection"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(["read:collection", "write:Post"]),
        Length(min: 5, groups: ['create:Post'])
    ]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read:collection", "write:Post"])]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(["read:item", "write:Post"])]
    private $content;

    #[ORM\Column(type: 'datetime')]
    #[Groups(["read:item"])]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts', cascade: ['persist'])]
    #[
        Groups(["read:item", "write:Post"]),
        Valid()
    ]
    private $category;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    #[
        Groups(["read:collection"]),
        ApiProperty(openapiContext: ['type' => 'boolean', 'description' => 'En ligne ou pas'])
    ]
    private $online = false;

    public static function validationGroups(self $post)
    {
        return ['create:Post'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }
}
