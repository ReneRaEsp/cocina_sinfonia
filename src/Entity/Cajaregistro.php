<?php

namespace App\Entity;

use App\Repository\CajaregistroRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CajaregistroRepository::class)
 */
class Cajaregistro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dia;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $iniciocaja;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $finalcaja;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalcaja;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $descuadre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observaciones;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDia(): ?\DateTimeInterface
    {
        return $this->dia;
    }

    public function setDia(\DateTimeInterface $dia): self
    {
        $this->dia = $dia;

        return $this;
    }

    public function getIniciocaja(): ?float
    {
        return $this->iniciocaja;
    }

    public function setIniciocaja(?float $iniciocaja): self
    {
        $this->iniciocaja = $iniciocaja;

        return $this;
    }

    public function getFinalcaja(): ?float
    {
        return $this->finalcaja;
    }

    public function setFinalcaja(?float $finalcaja): self
    {
        $this->finalcaja = $finalcaja;

        return $this;
    }

    public function getTotalcaja(): ?float
    {
        return $this->totalcaja;
    }

    public function setTotalcaja(?float $totalcaja): self
    {
        $this->totalcaja = $totalcaja;

        return $this;
    }

    public function getDescuadre(): ?float
    {
        return $this->descuadre;
    }

    public function setDescuadre(?float $descuadre): self
    {
        $this->descuadre = $descuadre;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }
}
