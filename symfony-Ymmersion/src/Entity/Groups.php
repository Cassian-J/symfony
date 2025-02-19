<?php

namespace App\Entity;

use App\Repository\GroupsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupsRepository::class)]
class Groups
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID ,nullable:false)]
    #[ORM\GeneratedValue(strategy: "NONE")] // UUID généré manuellement
    private ?string $GroupUuid = null;

    #[ORM\Column(type: Types::TEXT,nullable:false)]
    private ?string $Name = null;

    #[ORM\Column(type: Types::INTEGER,nullable:false)]
    private ?int $Point = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'],targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable:false ,name: 'Creator', referencedColumnName: 'user_uuid', unique: true)]
    private ?string $Creator = null;


    public function getGroupUuid(): ?string
    {
        return $this->GroupUuid;
    }

    public function setGroupUuid(string $GroupUuid): static
    {
        $this->GroupUuid = $GroupUuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

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

    public function getCreator(): ?string
    {
        return $this->Creator;
    }

    public function setCreator(string $Creator): static
    {
        $this->Creator = $Creator;

        return $this;
    }
}
