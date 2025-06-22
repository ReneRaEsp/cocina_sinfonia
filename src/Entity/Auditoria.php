<?php

namespace App\Entity;

use App\Repository\AuditoriaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AuditoriaRepository::class)
 */
class Auditoria
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
    private $usuario;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modificado;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rol_anterior;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rol_nuevo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getModificado(): ?string
    {
        return $this->modificado;
    }

    public function setModificado(string $modificado): self
    {
        $this->modificado = $modificado;

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

    public function getRolAnterior(): ?string
    {
        return $this->rol_anterior;
    }

    public function setRolAnterior(string $rol_anterior): self
    {
        $this->rol_anterior = $rol_anterior;

        return $this;
    }

    public function getRolNuevo(): ?string
    {
        return $this->rol_nuevo;
    }

    public function setRolNuevo(string $rol_nuevo): self
    {
        $this->rol_nuevo = $rol_nuevo;

        return $this;
    }
}
