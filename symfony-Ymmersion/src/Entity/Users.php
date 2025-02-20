<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID,nullable:false)]
    #[ORM\GeneratedValue(strategy: "NONE")] // UUID généré manuellement
    private ?string $UserUuid = null;

    #[ORM\Column(type: Types::TEXT, unique: true, nullable: false)]
    private ?string $Email = null;

    #[ORM\Column(type: Types::TEXT, unique: true, nullable: false)]
    private ?string $Pseudo = null;

    #[ORM\Column(type: Types::TEXT,nullable:false)]
    private ?string $Pwd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE,nullable:false)]
    private ?\DateTimeInterface $lastConnection = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(name: 'GroupUuid', referencedColumnName: 'group_uuid', nullable: true)]
    private ?Groups $GroupUuid = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $profilePicture = null;

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

    public function getGroupUuid(): ?Groups
    {
        return $this->GroupUuid;
    }

    public function setGroupUuid(?Groups $GroupUuid): static
    {
        $this->GroupUuid = $GroupUuid;

        return $this;
    }

    public function getProfilePicture(): mixed
    {
        return $this->profilePicture;
    }

    public function setProfilePicture($profilePicture): static
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Pwd;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return $this->Email;
    }
}
