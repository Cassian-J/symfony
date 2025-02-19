<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable:false,name: 'Sender', referencedColumnName: 'user_uuid')]
    private ?string $Sender = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable:false,name: 'Recever', referencedColumnName: 'user_uuid')]
    private ?string $Recever = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(nullable:false,name: 'WhichGroup', referencedColumnName: 'group_uuid')]
    private ?string $WhichGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?Users
    {
        return $this->Sender;
    }

    public function setSender(?Users $Sender): static
    {
        $this->Sender = $Sender;

        return $this;
    }

    public function getRecever(): ?Users
    {
        return $this->Recever;
    }

    public function setRecever(?Users $Recever): static
    {
        $this->Recever = $Recever;

        return $this;
    }

    public function getWhichGroup(): ?Groups
    {
        return $this->WhichGroup;
    }

    public function setWhichGroup(?Groups $WhichGroup): static
    {
        $this->WhichGroup = $WhichGroup;

        return $this;
    }
}
