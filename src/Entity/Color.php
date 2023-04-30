<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ColorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;

use App\Entity\Traits\Timestampable as TimestampableTrait;

#[ORM\Entity]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => 'color:collection']],
        'post',
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['groups' => 'color:item']],
        'put',
        'delete',
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'name' => 'partial',
    'code' => 'exact'
])]
#[ApiResource]
class Color implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'color:collection', 
        'color:item'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'color:collection', 
        'color:item'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'color:collection', 
        'color:item'
    ])]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups([
        'color:collection', 
        'color:item'
    ])]
    private ?string $image = null;

    #[ORM\Column]
    #[Groups([
        'color:collection', 
        'color:item'
    ])]
    private ?bool $isDark = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function isIsDark(): ?bool
    {
        return $this->isDark;
    }

    public function setIsDark(bool $isDark): self
    {
        $this->isDark = $isDark;

        return $this;
    }
}
