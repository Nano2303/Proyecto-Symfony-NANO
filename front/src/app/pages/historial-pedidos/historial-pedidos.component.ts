import { Component, OnInit } from '@angular/core';
import { Route, Router } from '@angular/router';
import { OrdenesService } from 'src/app/services/historial-pedidos/ordenes-service.service';

@Component({
  selector: 'app-historial-pedidos',
  templateUrl: './historial-pedidos.component.html',
  styleUrls: ['./historial-pedidos.component.scss']
})
export class HistorialPedidosComponent implements OnInit {

  ordenes: any[] = [];
  roleUser = localStorage.getItem('user_role') == "ROLE_USER" ? localStorage.getItem('user_role') : null;

  constructor(private ordenesService: OrdenesService,private router: Router) { }

  ngOnInit(): void {
    if(this.roleUser != null && this.roleUser != 'ROLE_ADMIN'){
      this.ordenesService.getOrdenes().subscribe(ordenes => {
        this.ordenes = ordenes.sort((a, b) => b.id - a.id);
        this.cargarImagenesProductos();
      });
    }else{
      this.router.navigate(['/home']);
    }
    
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
