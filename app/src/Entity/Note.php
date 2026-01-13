<?php

namespace App\Entity;

use App\Contract\EntityInterface;
use App\Enum\Group;
use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ORM\Table(name: '`notes`')]
#[ORM\HasLifecycleCallbacks]
class Note implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    #[Groups(groups: Group::PUBLIC->value)]
    private ?int $id = null;

    #[ORM\Column(
        name: 'name',
        type: Types::STRING,
        nullable: false,
        options: [
            'comment' => 'Наименование',
        ],
    )]
    #[Groups(groups: Group::PUBLIC->value)]
    private string $name;

    #[ORM\Column(
        name: 'description',
        type: Types::TEXT,
        nullable: false,
        options: [
            'comment' => 'Текст',
        ],
    )]
    #[Groups(groups: Group::PUBLIC->value)]
    private string $description;

    #[ORM\Column(
        name: 'is_private',
        type: Types::BOOLEAN,
        nullable: false,
        options: [
            'default' => true,
            'comment' => 'Приватная заметка',
        ],
    )]
    #[Groups(groups: Group::PUBLIC->value)]
    private bool $isPrivate;

    #[ORM\ManyToOne(
        targetEntity: User::class,
        cascade: ['persist'],
        inversedBy: 'note',
    )]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        nullable: false,
        options: [
            'unsigned' => true,
            'comment' => 'ID пользователя',
        ]
    )]
    #[Groups(groups: Group::PUBLIC->value)]
    private User $user;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIME_IMMUTABLE,
        nullable: false,
        options: ['comment' => 'Дата создания'],
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(
        name: 'updated_at',
        type: Types::DATETIME_IMMUTABLE,
        nullable: false,
        options: ['comment' => 'Дата изменения'],
    )]
    private \DateTimeImmutable $updatedAt;

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsPrivate(): bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
