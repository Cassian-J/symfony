<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID,nullable:false)]
    #[ORM\GeneratedValue(strategy: "NONE")] // UUID généré manuellement
    private ?string $UserUuid = null;

    #[ORM\Column(type: Types::TEXT, unique: true,nullable:false)]
    private ?string $Email = null;

    #[ORM\Column(type: Types::TEXT, unique: true,nullable:false)]
    private ?string $Pseudo = null;

    #[ORM\Column(type: Types::TEXT,nullable:false)]
    private ?string $Pwd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE,nullable:false)]
    private ?\DateTimeInterface $lastConnection = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(name: 'GroupUuid', referencedColumnName: 'group_uuid', nullable: true)]
    private ?string $GroupUuid = null;

    public function getUserUuid(): ?string
    {
        return $this->UserUuid;
    }

    public function setUserUuid(string $UserUuid): static
    {
        $this->UserUuid = $UserUuid;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->Pseudo;
    }

    public function setPseudo(string $Pseudo): static
    {
        $this->Pseudo = $Pseudo;

        return $this;
    }

    public function getPwd(): ?string
    {
        return $this->Pwd;
    }

    public function setPwd(string $Pwd): static
    {
        $this->Pwd = $Pwd;

        return $this;
    }

    public function getLastConnection(): ?\DateTimeInterface
    {
        return $this->lastConnection;
    }

    public function setLastConnection(\DateTimeInterface $lastConnection): static
    {
        $this->lastConnection = $lastConnection;

        return $this;
    }

    public function getGroupUuid(): ?string
    {
        return $this->GroupUuid;
    }

    public function setGroupUuid(?string $GroupUuid): static
    {
        $this->GroupUuid = $GroupUuid;

        return $this;
    }
}
