<?php

namespace App\Entity;

use App\Repository\LogsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogsRepository::class)]
class Logs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $groups = null;

    #[ORM\Column]
    private ?bool $addition = null;

    #[ORM\Column]
    private ?int $point = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroups(): ?string
    {
        return $this->groups;
    }

    public function setGroups(string $groups): static
    {
        $this->groups = $groups;

        return $this;
    }

    public function isAddition(): ?bool
    {
        return $this->addition;
    }

    public function setAddition(bool $addition): static
    {
        $this->addition = $addition;

        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(int $point): static
    {
        $this->point = $point;

        return $this;
    }
}
