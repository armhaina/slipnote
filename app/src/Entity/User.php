<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['unsigned' => true])]
    /** @phpstan-ignore property.unusedType */
    private ?int $id = null;

    /** @var array<string> */
    #[ORM\Column(
        name: 'roles',
        type: Types::JSON,
        nullable: false,
        options: ['comment' => 'Роли пользователя'],
    )]
    private array $roles;

    #[ORM\Column(
        name: 'email',
        type: Types::STRING,
        length: 255,
        unique: true,
        options: ['comment' => 'Электронная почта пользователя'],
    )]
    private string $email;

    #[ORM\Column(
        name: 'password',
        type: Types::STRING,
        length: 255,
        options: ['comment' => 'Пароль пользователя'],
    )]
    private string $password;

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

    /** @var Collection<int, Note> */
    #[ORM\OneToMany(
        targetEntity: Note::class,
        mappedBy: 'user',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @see UserInterface
     *
     * @return array<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    /**
     * The public representation of the user (e.g. a username, an email address, etc.).
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public static function shortName(): string
    {
        return new \ReflectionClass(objectOrClass: self::class)->getShortName();
    }
}
