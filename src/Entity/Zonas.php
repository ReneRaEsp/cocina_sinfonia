<?php

namespace App\Entity;

use App\Repository\ZonasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ZonasRepository::class)
 */
class Zonas
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity=Mesas::class, mappedBy="zonas")
     */
    private $mesas;

    public function __construct()
    {
        $this->mesas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, mesas>
     */
    public function getMesas(): Collection
    {
        return $this->mesas;
    }

    public function addMesa(mesas $mesa): self
    {
        if (!$this->mesas->contains($mesa)) {
            $this->mesas[] = $mesa;
            $mesa->setZonas($this);
        }

        return $this;
    }

    public function removeMesa(mesas $mesa): self
    {
        if ($this->mesas->removeElement($mesa)) {
            // set the owning side to null (unless already changed)
            if ($mesa->getZonas() === $this) {
                $mesa->setZonas(null);
            }
        }

        return $this;
    }
}
