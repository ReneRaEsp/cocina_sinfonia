<?php

namespace App\Entity;

use App\Repository\FacturasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FacturasRepository::class)
 */
class Facturas
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
    private $nombre;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $concepto;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_factura;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $empresa;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $importe;

    /**
     * @ORM\Column(type="text", length=50)
     */
    private $ruta_pdf;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\OneToMany(targetEntity=ImpuestoFacturas::class, mappedBy="factura" , cascade={"persist", "remove"})
     */
    private $impuestoFacturas;

    public function __construct()
    {
        $this->impuestoFacturas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getConcepto(): ?string
    {
        return $this->concepto;
    }

    public function setConcepto(string $concepto): self
    {
        $this->concepto = $concepto;

        return $this;
    }

    public function getFechaFactura(): ?\DateTimeInterface
    {
        return $this->fecha_factura;
    }

    public function setFechaFactura(\DateTimeInterface $fecha): self
    {
        $this->fecha_factura = $fecha;

        return $this;
    }

    public function getEmpresa(): ?string
    {
        return $this->empresa;
    }

    public function setEmpresa(string $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    public function getImporte(): ?float
    {
        return $this->importe;
    }

    public function setImporte(?string $importe): self
    {
        $this->importe = $importe;

        return $this;
    }

    public function getRutaPdf(): ?string
    {
        return $this->ruta_pdf;
    }

    public function setRutaPdf(string $ruta_pdf): self
    {
        $this->ruta_pdf = $ruta_pdf;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return Collection<int, ImpuestoFacturas>
     */
    public function getImpuestoFacturas(): Collection
    {
        return $this->impuestoFacturas;
    }

    public function addImpuestoFactura(ImpuestoFacturas $impuestoFactura): self
    {
        if (!$this->impuestoFacturas->contains($impuestoFactura)) {
            $this->impuestoFacturas[] = $impuestoFactura;
            $impuestoFactura->setFactura($this);
        }

        return $this;
    }

    public function removeImpuestoFactura(ImpuestoFacturas $impuestoFactura): self
    {
        if ($this->impuestoFacturas->removeElement($impuestoFactura)) {
            // set the owning side to null (unless already changed)
            if ($impuestoFactura->getFactura() === $this) {
                $impuestoFactura->setFactura(null);
            }
        }

        return $this;
    }
}
