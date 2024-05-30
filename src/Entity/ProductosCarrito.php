<?php

namespace App\Entity;

use App\Repository\ProductosCarritoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductosCarritoRepository::class)]
class ProductosCarrito
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    #[ORM\ManyToOne(inversedBy: 'productos_carrito')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CarritoCompras $carritoCompras = null;

    #[ORM\ManyToOne(inversedBy: 'productos_carrito')]
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

    public function getCarritoCompras(): ?CarritoCompras
    {
        return $this->carritoCompras;
    }

    public function setCarritoCompras(?CarritoCompras $carritoCompras): static
    {
        $this->carritoCompras = $carritoCompras;

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
