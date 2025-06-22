<?php

namespace App\Entity;

use App\Repository\MesasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MesasRepository::class)
 */
class Mesas
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $numero;

    /**
     * @ORM\OneToMany(targetEntity=Ventas::class, mappedBy="mesa")
     */
    private $ventas;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $pagado;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $por_pagar;

    /**
     * @ORM\OneToMany(targetEntity=Pedidos::class, mappedBy="mesa")
     */
    private $pedidos;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $union_mesas = [];

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $localizacion;

    /**
     * @ORM\ManyToOne(targetEntity=Zonas::class, inversedBy="mesas")
     */
    private $zonas;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comensales;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $factura;

    /**
     * @ORM\OneToMany(targetEntity=Tickets::class, mappedBy="mesaid")
     */
    private $tickets;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $coord_x;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $coord_y;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @ORM\OneToMany(targetEntity=Statscomida::class, mappedBy="mesa")
     */
    private $statscomidas;

    /**
     * @ORM\OneToMany(targetEntity=Statscomensales::class, mappedBy="mesa")
     */
    private $statscomensales;

    

    public function __construct()
    {
        $this->ventas = new ArrayCollection();
        $this->pedidos = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->statscomidas = new ArrayCollection();
        $this->statscomensales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return Collection<int, Ventas>
     */
    public function getVentas(): Collection
    {
        return $this->ventas;
    }

    public function addVenta(Ventas $venta): self
    {
        if (!$this->ventas->contains($venta)) {
            $this->ventas[] = $venta;
            $venta->setMesa($this);
        }

        return $this;
    }

    public function removeVenta(Ventas $venta): self
    {
        if ($this->ventas->removeElement($venta)) {
            // set the owning side to null (unless already changed)
            if ($venta->getMesa() === $this) {
                $venta->setMesa(null);
            }
        }

        return $this;
    }

    public function getPagado(): ?string
    {
        return $this->pagado;
    }

    public function setPagado(?string $pagado): self
    {
        $this->pagado = $pagado;

        return $this;
    }

    public function getPorPagar(): ?string
    {
        return $this->por_pagar;
    }

    public function setPorPagar(string $por_pagar): self
    {
        $this->por_pagar = $por_pagar;

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
            $pedido->setMesa($this);
        }

        return $this;
    }

    public function removePedido(Pedidos $pedido): self
    {
        if ($this->pedidos->removeElement($pedido)) {
            // set the owning side to null (unless already changed)
            if ($pedido->getMesa() === $this) {
                $pedido->setMesa(null);
            }
        }

        return $this;
    }

    public function getUnionMesas(): ?array
    {
        return $this->union_mesas;
    }

    public function setUnionMesas(?array $union_mesas): self
    {
        $this->union_mesas = $union_mesas;

        return $this;
    }

    public function getLocalizacion(): ?string
    {
        return $this->localizacion;
    }

    public function setLocalizacion(string $localizacion): self
    {
        $this->localizacion = $localizacion;

        return $this;
    }

    public function getZonas(): ?Zonas
    {
        return $this->zonas;
    }

    public function setZonas(?Zonas $zonas): self
    {
        $this->zonas = $zonas;

        return $this;
    }

    public function getComensales(): ?int
    {
        return $this->comensales;
    }

    public function setComensales(?int $comensales): self
    {
        $this->comensales = $comensales;

        return $this;
    }

    public function isFactura(): ?bool
    {
        return $this->factura;
    }

    public function setFactura(?bool $factura): self
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * @return Collection<int, Tickets>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Tickets $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setMesaid($this);
        }

        return $this;
    }

    public function removeTicket(Tickets $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getMesaid() === $this) {
                $ticket->setMesaid(null);
            }
        }

        return $this;
    }

    public function getCoordX(): ?float
    {
        return $this->coord_x;
    }

    public function setCoordX(?float $coord_x): self
    {
        $this->coord_x = $coord_x;

        return $this;
    }

    public function getCoordY(): ?float
    {
        return $this->coord_y;
    }

    public function setCoordY(?float $coord_y): self
    {
        $this->coord_y = $coord_y;

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
            $statscomida->setMesa($this);
        }

        return $this;
    }

    public function removeStatscomida(Statscomida $statscomida): self
    {
        if ($this->statscomidas->removeElement($statscomida)) {
            // set the owning side to null (unless already changed)
            if ($statscomida->getMesa() === $this) {
                $statscomida->setMesa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Statscomensales>
     */
    public function getStatscomensales(): Collection
    {
        return $this->statscomensales;
    }

    public function addStatscomensale(Statscomensales $statscomensale): self
    {
        if (!$this->statscomensales->contains($statscomensale)) {
            $this->statscomensales[] = $statscomensale;
            $statscomensale->setMesa($this);
        }

        return $this;
    }

    public function removeStatscomensale(Statscomensales $statscomensale): self
    {
        if ($this->statscomensales->removeElement($statscomensale)) {
            // set the owning side to null (unless already changed)
            if ($statscomensale->getMesa() === $this) {
                $statscomensale->setMesa(null);
            }
        }

        return $this;
    }
}
