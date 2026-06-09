<?php

namespace App\Entity;

use App\Repository\ModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'models')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Brand $brand = null;

    /**
     * @var Collection<int, VehicleReference>
     */
    #[ORM\OneToMany(targetEntity: VehicleReference::class, mappedBy: 'model', orphanRemoval: true)]
    private Collection $vehicleReferences;

    public function __construct()
    {
        $this->vehicleReferences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, VehicleReference>
     */
    public function getVehicleReferences(): Collection
    {
        return $this->vehicleReferences;
    }

    public function addVehicleReference(VehicleReference $vehicleReference): static
    {
        if (!$this->vehicleReferences->contains($vehicleReference)) {
            $this->vehicleReferences->add($vehicleReference);
            $vehicleReference->setModel($this);
        }

        return $this;
    }

    public function removeVehicleReference(VehicleReference $vehicleReference): static
    {
        if ($this->vehicleReferences->removeElement($vehicleReference)) {
            // set the owning side to null (unless already changed)
            if ($vehicleReference->getModel() === $this) {
                $vehicleReference->setModel(null);
            }
        }

        return $this;
    }
}
