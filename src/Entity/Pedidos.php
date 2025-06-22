<?php

namespace App\Entity;

use App\Repository\PedidosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PedidosRepository::class)
 */
class Pedidos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Mesas::class, inversedBy="pedidos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mesa;

    /**
     * @ORM\ManyToOne(targetEntity=Comida::class, inversedBy="pedidos")
     * @ORM\JoinColumn(nullable=true)
     */
    private $comida;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comentarios;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $marchando;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $extras = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $invitacion;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $descuento;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $descuento_eur;

    /**

     * @ORM\Column(type="integer", nullable=true)
     */
    private $estado = 0;

    /**
     * @ORM\OneToMany(targetEntity=Tickets::class, mappedBy="pedido")
     */
    private $tickets;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $num_plato;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $num_ref;

    /**
     * @ORM\ManyToOne(targetEntity=Productostienda::class, inversedBy="pedidos")
     */
    private $producttienda;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $entregado = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $estado_plato = 0;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMesa(): ?Mesas
    {
        return $this->mesa;
    }

    public function setMesa(?Mesas $mesa): self
    {
        $this->mesa = $mesa;

        return $this;
    }

    public function getComida(): ?Comida
    {
        return $this->comida;
    }

    public function setComida(?Comida $comida): self
    {
        $this->comida = $comida;

        return $this;
    }

    public function getComentarios(): ?string
    {
        return $this->comentarios;
    }

    public function setComentarios(?string $comentarios): self
    {
        $this->comentarios = $comentarios;

        return $this;
    }

    public function isMarchando(): ?bool
    {
        return $this->marchando;
    }

    public function setMarchando(?bool $marchando): self
    {
        $this->marchando = $marchando;

        return $this;
    }

    public function getExtras(): ?array
    {
        return $this->extras;
    }

    public function setExtras(?array $extras): self
    {
        $this->extras = $extras;

        return $this;
    }

    public function isInvitacion(): ?bool
    {
        return $this->invitacion;
    }

    public function setInvitacion(?bool $invitacion): self
    {
        $this->invitacion = $invitacion;

        return $this;
    }

    public function getDescuento(): ?float
    {
        return $this->descuento;
    }

    public function setDescuento(?float $descuento): self
    {
        $this->descuento = $descuento;

        return $this;
    }

    public function getDescuentoEur(): ?float
    {
        return $this->descuento_eur;
    }

    public function setDescuentoEur(?float $descuento_eur): self
    {
        $this->descuento_eur = $descuento_eur;

        return $this;
    }


    public function getEstado(): ?int
    {
        return $this->estado;
    }

    public function setEstado(?int $estado): self
    {
        $this->estado = $estado;

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
            $ticket->setPedido($this);
        }

        return $this;
    }

    public function removeTicket(Tickets $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getPedido() === $this) {
                $ticket->setPedido(null);
            }
        }


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

    public function getNumRef(): ?string
    {
        return $this->num_ref;
    }

    public function setNumRef(?string $num_ref): self
    {
        $this->num_ref = $num_ref;

        return $this;
    }

    public function getProducttienda(): ?ProductosTienda
    {
        return $this->producttienda;
    }

    public function setProducttienda(?ProductosTienda $producttienda): self
    {
        $this->producttienda = $producttienda;

        return $this;
    }

    public function isEntregado(): ?bool
    {
        return $this->entregado;
    }

    public function setEntregado(?bool $entregado): self
    {
        $this->entregado = $entregado;

        return $this;
    }

    public function getEstadoPlato(): ?int
    {
        return $this->estado_plato;
    }

    public function setEstadoPlato(?int $estado_plato): self
    {
        $this->estado_plato = $estado_plato;

        return $this;
    }
}
