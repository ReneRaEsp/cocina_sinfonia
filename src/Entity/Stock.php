<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Stock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=TipoComida::class, inversedBy="stock")
     */
    private $type_food;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=Comida::class, inversedBy="stock")
     */
    private $comida;

    /**
     * @ORM\ManyToOne(targetEntity=Productostienda::class, inversedBy="stocks")
     */
    private $producttienda;


    public function __construct()
    {
        $this->type_food = new ArrayCollection();
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

    /**
     * @return TipoComida|null
     */
    public function getTypeFood()
    {
        return $this->type_food;
    }
    
    public function setTypeFood(TipoCOmida $tf){

        $this->type_food = $tf;

        return $this;
    }

    public function addTypeFood(TipoComida $typeFood): self
    {
        if (!$this->type_food->contains($typeFood)) {
            $this->type_food[] = $typeFood;
            $typeFood->setStock($this);
        }

        return $this;
    }

    public function removeTypeFood(TipoComida $typeFood): self
    {
        if ($this->type_food->removeElement($typeFood)) {
            // set the owning side to null (unless already changed)
            if ($typeFood->getStock() === $this) {
                $typeFood->setStock(null);
            }
        }

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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

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

    public function getProducttienda(): ?productostienda
    {
        return $this->producttienda;
    }

    public function setProducttienda(?productostienda $producttienda): self
    {
        $this->producttienda = $producttienda;

        return $this;
    }

}
