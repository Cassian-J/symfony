<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER,nullable:false)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING,length: 250,nullable:false)]
    private ?string $Title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;

    #[ORM\Column(type: Types::STRING,length: 6,nullable:false)]
    private ?string $color = null;

    #[ORM\Column]
    private ?int $difficulty = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $Periodicity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Days = null;

    #[ORM\Column]
    private ?bool $IsGroupTask = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'UserUuid', referencedColumnName: 'user_uuid', nullable: true)]
    private ?Users $UserUuid = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(nullable:true,name: 'GroupUuid', referencedColumnName: 'group_uuid')]
    private ?Groups $GroupUuid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): static
    {
        $this->Title = $Title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getUserUuid(): ?Users
    {
        return $this->UserUuid;
    }

    public function setUserUuid(?Users $UserUuid): static
    {
        $this->UserUuid = $UserUuid;

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

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPeriodicity(): ?string
    {
        return $this->Periodicity;
    }

    public function setPeriodicity(string $Periodicity): static
    {
        $this->Periodicity = $Periodicity;

        return $this;
    }

    public function getDays(): ?string
    {
        return $this->Days;
    }

    public function setDays(?string $Days): static
    {
        $this->Days = $Days;

        return $this;
    }

    public function isGroupTask(): ?bool
    {
        return $this->IsGroupTask;
    }

    public function setIsGroupTask(bool $IsGroupTask): static
    {
        $this->IsGroupTask = $IsGroupTask;

        return $this;
    }
}
