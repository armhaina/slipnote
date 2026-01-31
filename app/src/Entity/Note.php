<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ORM\Table(name: '`notes`')]
#[ORM\Index(name: 'idx_notes_name', columns: ['name'])]
#[ORM\Index(name: 'idx_notes_description', columns: ['description'])]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    /** @phpstan-ignore property.unusedType */
    private ?int $id = null;

    #[ORM\Column(
        name: 'name',
        type: Types::STRING,
        nullable: false,
        options: [
            'comment' => 'Наименование',
        ],
    )]
    private string $name;

    #[ORM\Column(
        name: 'description',
        type: Types::TEXT,
        nullable: true,
        options: [
            'comment' => 'Текст',
        ],
    )]
    private ?string $description = null;

    #[ORM\Column(
        name: 'is_trashed',
        type: Types::BOOLEAN,
        nullable: false,
        options: [
            'default' => false,
            'comment' => 'Удалено',
        ],
    )]
    private bool $isTrashed;

    #[ORM\Column(
        name: 'deleted_at',
        type: Types::DATETIME_IMMUTABLE,
        nullable: true,
        options: [
            'comment' => 'Дата удаления',
        ],
    )]
    private ?\DateTimeImmutable $deletedAt = null;

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
    private UserInterface $user;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsTrashed(): bool
    {
        return $this->isTrashed;
    }

    public function setIsTrashed(bool $isTrashed): self
    {
        $this->isTrashed = $isTrashed;

        return $this;
    }

    public function getUser(): User|UserInterface
    {
        return $this->user;
    }

    public function setUser(User|UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $dateTimeImmutable): self
    {
        $this->createdAt = $dateTimeImmutable;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $dateTimeImmutable): self
    {
        $this->updatedAt = $dateTimeImmutable;

        return $this;
    }

    public static function shortName(): string
    {
        return new \ReflectionClass(objectOrClass: self::class)->getShortName();
    }
}
