<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_phone_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true,
 *      ),
 *     exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(null === object.getId())"
 *     )
 * )
 *
 * @Hateoas\Relation(
 *      "create",
 *      href = @Hateoas\Route(
 *          "app_phone_create",
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted('ROLE_SUPERADMIN'))"
 *      )
 * )
 *
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "app_phone_update",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(null === object.getId())"
 *      )
 * )
 *
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "app_phone_delete",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(null === object.getId())"
 *      )
 * )
 *
 * @OA\Schema(schema="Phone")
 */
class Phone
{
    /**
     * @OA\Property(type="integer", description="The ID")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @OA\Property(type="string", description="The brand")
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     */
    private $brand;

    /**
     * @OA\Property(type="string", description="The model")
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     */
    private $model;

    /**
     * @OA\Property(type="string", description="The color")
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank
     */
    private $color;

    /**
     * @OA\Property(type="string", description="The Description")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @OA\Property(type="integer", description="The price")
     * @ORM\Column(type="decimal", precision=6, scale=2)
     * @Assert\NotBlank
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
