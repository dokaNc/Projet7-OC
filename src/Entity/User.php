<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_user_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(null === object.getId())"
 *      )
 * )
 *
 * @OA\Schema(schema="User")
 *
 */
class User implements UserInterface
{
    /**
     * @OA\Property(type="integer", description="The ID")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"default", "user", "unique", "one"})
     */
    private $id;

    /**
     * @OA\Property(type="string", description="The email")
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     * @Assert\NotBlank
     * @Groups({"default", "user", "unique", "one"})
     */
    private $email;

    /**
     * @OA\Property(type="string", description="The roles")
     * @ORM\Column(type="json")
     * @Groups({"default"})
     */
    private $roles = [];

    /**
     * @OA\Property(type="string", description="The password")
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Groups({"default"})
     */
    private $password;

    /**
     * @OA\Property(type="string", description="The Client link to User")
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users", cascade={"persist"})
     * @Groups({"default", "user", "unique"})
     */
    private $Clients;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @OA\Property(type="string", property="email")
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @OA\Property(type="string", property="password")
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getClients(): ?Client
    {
        return $this->Clients;
    }

    public function setClients(?Client $Clients): self
    {
        $this->Clients = $Clients;

        return $this;
    }
}
