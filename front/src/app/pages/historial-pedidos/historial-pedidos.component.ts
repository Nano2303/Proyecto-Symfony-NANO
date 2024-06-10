import { Component, OnInit } from '@angular/core';
import { OrdenesService } from 'src/app/services/historial-pedidos/ordenes-service.service';

@Component({
  selector: 'app-historial-pedidos',
  templateUrl: './historial-pedidos.component.html',
  styleUrls: ['./historial-pedidos.component.scss']
})
export class HistorialPedidosComponent implements OnInit {

  ordenes: any[] = [];

  constructor(private ordenesService: OrdenesService) { }

  ngOnInit(): void {
    this.ordenesService.getOrdenes().subscribe(ordenes => {
      this.ordenes = ordenes;
      this.cargarImagenesProductos();
    });
  }

  cargarImagenesProductos(): void {
    this.ordenesService.getListaProductos().subscribe(productos => {
      this.ordenes.forEach(orden => {
        orden.productos.forEach((producto: any) => {
          const productoInfo = productos.find(p => p.id === producto.id);
          if (productoInfo) {
            producto.image = `./assets/img/${productoInfo.src}`;
          }
        });
      });
    });
  }
}
