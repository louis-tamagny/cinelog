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
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $isDisabled = false;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'commentUser')]
    private Collection $comment;

    /**
     * @var Collection<int, Response>
     */
    #[ORM\OneToMany(targetEntity: Response::class, mappedBy: 'userResponse')]
    private Collection $response;

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'watch_later_movies')]
    private Collection $watchLater;

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'favourite_movies')]
    private Collection $favourite;

    public function __construct()
    {
        $this->comment = new ArrayCollection();
        $this->response = new ArrayCollection();
        $this->watchLater = new ArrayCollection();
        $this->favourite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isDisabled(): ?bool
    {
        return $this->isDisabled;
    }

    public function setIsDisabled(bool $isDisabled): static
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comment->contains($comment)) {
            $this->comment->add($comment);
            $comment->setCommentUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCommentUser() === $this) {
                $comment->setCommentUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Response>
     */
    public function getResponse(): Collection
    {
        return $this->response;
    }

    public function addResponse(Response $response): static
    {
        if (!$this->response->contains($response)) {
            $this->response->add($response);
            $response->setUserResponse($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): static
    {
        if ($this->response->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getUserResponse() === $this) {
                $response->setUserResponse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getWatchLater(): Collection
    {
        return $this->watchLater;
    }

    public function addWatchLater(Movie $watchLater): static
    {
        if (!$this->watchLater->contains($watchLater)) {
            $this->watchLater->add($watchLater);
        }

        return $this;
    }

    public function removeWatchLater(Movie $watchLater): static
    {
        $this->watchLater->removeElement($watchLater);

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getFavourite(): Collection
    {
        return $this->favourite;
    }

    public function addFavourite(Movie $favourite): static
    {
        if (!$this->favourite->contains($favourite)) {
            $this->favourite->add($favourite);
        }

        return $this;
    }

    public function removeFavourite(Movie $favourite): static
    {
        $this->favourite->removeElement($favourite);

        return $this;
    }
}
