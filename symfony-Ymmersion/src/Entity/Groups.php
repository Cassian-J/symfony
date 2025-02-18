<?php

namespace App\Entity;

use App\Repository\GroupsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupsRepository::class)]
class Groups
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $GroupsUuid = null;

    #[ORM\Column(length: 200)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $creator = null;

    #[ORM\Column]
    private ?int $points = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupsUuid(): ?string
    {
        return $this->GroupsUuid;
    }

    public function setGroupsUuid(string $GroupsUuid): static
    {
        $this->GroupsUuid = $GroupsUuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreator(): ?string
    {
        return $this->creator;
    }

    public function setCreator(string $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }
}
