<?php

namespace App\Entity;

use App\Repository\ComidaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ComidaRepository::class)
 */
class Comida
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
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=TipoComida::class, inversedBy="comida")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type_food;

    /**
     * @ORM\OneToMany(targetEntity=Pedidos::class, mappedBy="comida")
     */
    private $pedidos;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $precio;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $unitario;

    /**
     * @ORM\OneToMany(targetEntity=Stock::class, mappedBy="comida")
     */
    private $stock;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $iscomida;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isbebida;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $extra;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $posiblesextras = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $num_plato = 1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numplato;

    /**
     * @ORM\OneToMany(targetEntity=Statscomida::class, mappedBy="comida")
     */
    private $statscomidas;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $rutaimg;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_deleted;


    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeFood(): ?TipoComida
    {
        return $this->type_food;
    }

    public function setTypeFood(?TipoComida $type_food): self
    {
        $this->type_food = $type_food;

        return $this;
    }

    /**
     * @return Collection<int, Pedidos>
     */
    public function getPedidos(): Collection
    {
        return $this->pedidos;
    }

    public function addPedido(Pedidos $pedido): self
    {
        if (!$this->pedidos->contains($pedido)) {
            $this->pedidos[] = $pedido;
            $pedido->setComida($this);
        }

        return $this;
    }

    public function removePedido(Pedidos $pedido): self
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getComida() === $this) {
                $pedido->setComida(null);
            }
        }

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function isUnitario(): ?bool
    {
        return $this->unitario;
    }

    public function setUnitario(?bool $unitario): self
    {
        $this->unitario = $unitario;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): self
    {
        // unset the owning side of the relation if necessary
        if ($stock === null && $this->stock !== null) {
            $this->stock->setComida(null);
        }

        // set the owning side of the relation if necessary
        if ($stock !== null && $stock->getComida() !== $this) {
            $stock->setComida($this);
        }

        $this->stock = $stock;

        return $this;
    }

    public function isIscomida(): ?bool
    {
        return $this->iscomida;
    }

    public function setIscomida(?bool $iscomida): self
    {
        $this->iscomida = $iscomida;

        return $this;
    }

    public function isIsbebida(): ?bool
    {
        return $this->isbebida;
    }

    public function setIsbebida(?bool $iscomida): self
    {
        $this->isbebida = $iscomida;

        return $this;
    }


    public function isExtra(): ?bool
    {
        return $this->extra;
    }

    public function setExtra(?bool $extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    public function getPosiblesextras(): ?array
    {
        return $this->posiblesextras;
    }

    public function setPosiblesextras(?array $posiblesextras): self
    {
        $this->posiblesextras = $posiblesextras;

        return $this;
    }

    public function getNumPlato(): ?int
    {
        return $this->num_plato;
    }

    public function setNumPlato(?int $num_plato): self
    {
        $this->num_plato = $num_plato;

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
            $statscomida->setComida($this);
        }

        return $this;
    }

    public function removeStatscomida(Statscomida $statscomida): self
    {
        if ($this->statscomidas->removeElement($statscomida)) {
            // set the owning side to null (unless already changed)
            if ($statscomida->getComida() === $this) {
                $statscomida->setComida(null);
            }
        }

        return $this;
    }

    public function getRutaimg(): ?string
    {
        return $this->rutaimg;
    }

    public function setRutaimg(?string $rutaimg): self
    {
        $this->rutaimg = $rutaimg;

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): self
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }
}

