<?php

namespace App\Entity;

use App\Repository\FichajeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FichajeRepository::class)
 */
class Fichaje
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
    private $user;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="time")
     */
    private $inicio_am;

    /**
     * @ORM\Column(type="time")
     */
    private $fin_am;

    /**
     * @ORM\Column(type="time")
     */
    private $inicio_pm;

    /**
     * @ORM\Column(type="time")
     */
    private $fin_pm;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getInicioAm(): ?\DateTimeInterface
    {
        return $this->inicio_am;
    }

    public function setInicioAm(\DateTimeInterface $inicio_am): self
    {
        $this->inicio_am = $inicio_am;

        return $this;
    }

    public function getFinAm(): ?\DateTimeInterface
    {
        return $this->fin_am;
    }

    public function setFinAm(\DateTimeInterface $fin_am): self
    {
        $this->fin_am = $fin_am;

        return $this;
    }

    public function getInicioPm(): ?\DateTimeInterface
    {
        return $this->inicio_pm;
    }

    public function setInicioPm(\DateTimeInterface $inicio_pm): self
    {
        $this->inicio_pm = $inicio_pm;

        return $this;
    }

    public function getFinPm(): ?\DateTimeInterface
    {
        return $this->fin_pm;
    }

    public function setFinPm(\DateTimeInterface $fin_pm): self
    {
        $this->fin_pm = $fin_pm;

        return $this;
    }
}
