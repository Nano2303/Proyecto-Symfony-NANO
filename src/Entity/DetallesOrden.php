<?php

namespace App\Entity;

use App\Repository\DetallesOrdenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetallesOrdenRepository::class)]
class DetallesOrden
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    #[ORM\Column]
    private ?float $precio_unitario = null;

    #[ORM\ManyToOne(inversedBy: 'detalles_orden')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ordenes $ordenes = null;

    #[ORM\ManyToOne(inversedBy: 'detalles_orden')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Productos $productos = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getPrecioUnitario(): ?float
    {
        return $this->precio_unitario;
    }

    public function setPrecioUnitario(float $precio_unitario): static
    {
        $this->precio_unitario = $precio_unitario;

        return $this;
    }

    public function getOrdenes(): ?Ordenes
    {
        return $this->ordenes;
    }

    public function setOrdenes(?Ordenes $ordenes): static
    {
        $this->ordenes = $ordenes;

        return $this;
    }

    public function getProductos(): ?Productos
    {
        return $this->productos;
    }

    public function setProductos(?Productos $productos): static
    {
        $this->productos = $productos;

        return $this;
    }
}
