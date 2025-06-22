<?php

namespace App\Entity;

use App\Repository\ProductostiendaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductostiendaRepository::class)
 */
class Productostienda
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pvp;

    /**
     * @ORM\OneToMany(targetEntity=Pedidos::class, mappedBy="producttienda")
     */
    private $pedidos;

    /**
     * @ORM\OneToMany(targetEntity=Stock::class, mappedBy="producttienda")
     */
    private $stocks;

    /**
     * @ORM\OneToMany(targetEntity=Statscomida::class, mappedBy="tienda")
     */
    private $statscomidas;

    public function __construct()
    {
        $this->pedidos = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->statscomidas = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }


    public function getPvp(): ?float
    {
        return $this->pvp;
    }

    public function setPvp(?float $pvp): self
    {
        $this->pvp = $pvp;

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
            $pedido->setProducttienda($this);
        }

        return $this;
    }

    public function removePedido(Pedidos $pedido): self
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getProducttienda() === $this) {
                $pedido->setProducttienda(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setProducttienda($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getProducttienda() === $this) {
                $stock->setProducttienda(null);
            }
        }

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
            $statscomida->setTienda($this);
        }

        return $this;
    }

    public function removeStatscomida(Statscomida $statscomida): self
    {
        if ($this->statscomidas->removeElement($statscomida)) {
            // set the owning side to null (unless already changed)
            if ($statscomida->getTienda() === $this) {
                $statscomida->setTienda(null);
            }
        }

        return $this;
    }
}
