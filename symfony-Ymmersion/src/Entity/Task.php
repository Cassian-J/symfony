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

    #[ORM\Column(type: Types::STRING,length: 255,nullable:false)]
    private ?string $Periodicity = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'UserUuid', referencedColumnName: 'user_uuid', nullable: true)]
    private ?string $UserUuid = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(nullable:true,name: 'GroupUuid', referencedColumnName: 'group_uuid')]
    private ?string $GroupUuid = null;

    #[ORM\Column]
    private ?int $difficulty = null;

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

    public function getPeriodicity(): ?string
    {
        return $this->Periodicity;
    }

    public function setPeriodicity(string $Periodicity): static
    {
        $this->Periodicity = $Periodicity;

        return $this;
    }

    public function getUserUuid(): ?string
    {
        return $this->UserUuid;
    }

    public function setUserUuid(?string $UserUuid): static
    {
        $this->UserUuid = $UserUuid;

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

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }
}
