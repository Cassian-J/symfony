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
    #[ORM\JoinColumn(nullable: false,name: 'GroupUuid', referencedColumnName: 'group_uuid')]
    private ?Groups $GroupUuid = null;

    #[ORM\Column(nullable: false,type: Types::INTEGER)]
    private ?int $Point = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(nullable: false, name: 'UserUuid', referencedColumnName: 'user_uuid')]
    private ?Users $UserUuid = null;

    #[ORM\ManyToOne(targetEntity: Task::class)]
    #[ORM\JoinColumn(nullable: false, name: 'TaskId', referencedColumnName: 'id')]
    private ?Task $TaskId = null;

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

    public function getPoint(): ?int
    {
        return $this->Point;
    }

    public function setPoint(int $Point): static
    {
        $this->Point = $Point;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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

    public function getTaskId(): ?Task
    {
        return $this->TaskId;
    }

    public function setTaskId(?Task $TaskId): static
    {
        $this->TaskId = $TaskId;

        return $this;
    }

}
