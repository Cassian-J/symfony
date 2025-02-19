<?php

namespace App\Entity;

use App\Repository\GroupLogsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: GroupLogsRepository::class)]
class GroupLogs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(nullable: false,name: 'GroupUuid', referencedColumnName: 'GroupUuid')]
    private ?Groups $GroupUuid = null;

    #[ORM\Column(nullable: false,type: Types::BOOLEAN)]
    private ?bool $Addition = null;

    #[ORM\Column(nullable: false,type: Types::INTEGER)]
    private ?int $Point = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupUuid(): ?Groups
    {
        return $this->GroupUuid;
    }

    public function setGroupUuid(?Groups $GroupUuid): static
    {
        $this->GroupUuid = $GroupUuid;

        return $this;
    }

    public function isAddition(): ?bool
    {
        return $this->Addition;
    }

    public function setAddition(bool $Addition): static
    {
        $this->Addition = $Addition;

        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->Point;
    }

    public function setPoint(int $Point): static
    {
        $this->Point = $Point;

        return $this;
    }

}
