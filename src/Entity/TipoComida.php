<?php

namespace App\Entity;

use App\Repository\TipoComidaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TipoComidaRepository::class)
 */
class TipoComida
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Stock::class, mappedBy="type_food")
     */
    private $stock;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $icon;

    /**
     * @ORM\OneToMany(targetEntity=Comida::class, mappedBy="type_food")
     */
    private $comida;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity=Statscomida::class, mappedBy="tipocomida")
     */
    private $statscomidas;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ruta_img;

    public function __construct()
    {
        $this->comida = new ArrayCollection();
        $this->statscomidas = new ArrayCollection();
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

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Collection<int, Comida>
     */
    public function getComida(): Collection
    {
        return $this->comida;
    }

    public function addComida(Comida $comida): self
    {
        if (!$this->comida->contains($comida)) {
            $this->comida[] = $comida;
            $comida->setTypeFood($this);
        }

        return $this;
    }

    public function removeComida(Comida $comida): self
    {
        if ($this->comida->removeElement($comida)) {
            // set the owning side to null (unless already changed)
            if ($comida->getTypeFood() === $this) {
                $comida->setTypeFood(null);
            }
        }

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
     * @return Collection<int, Statscomida>
     */
    public function getStatscomidas(): Collection
    {
        return $this->statscomidas;
    }

    public function addStatscomida(Statscomida $statscomida): self
    {
        if (!$this->statscomidas->contains($statscomida)) {
            $this->statscomidas[] = $statscomida;
            $statscomida->setTipocomida($this);
        }

        return $this;
    }

    public function removeStatscomida(Statscomida $statscomida): self
    {
        if ($this->statscomidas->removeElement($statscomida)) {
            // set the owning side to null (unless already changed)
            if ($statscomida->getTipocomida() === $this) {
                $statscomida->setTipocomida(null);
            }
        }

        return $this;
    }

    public function getRutaImg(): ?string
    {
        return $this->ruta_img;
    }

    public function setRutaImg(?string $ruta_img): self
    {
        $this->ruta_img = $ruta_img;

        return $this;
    }
}
