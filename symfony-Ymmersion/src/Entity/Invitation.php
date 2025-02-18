<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $sender = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $recever = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $groups = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecever(): ?string
    {
        return $this->recever;
    }

    public function setRecever(string $recever): static
    {
        $this->recever = $recever;

        return $this;
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
}
