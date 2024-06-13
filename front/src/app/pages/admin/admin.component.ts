import { HttpClient, HttpParams } from '@angular/common/http';
import { Component } from '@angular/core';
import { environment } from 'src/environments/environment.prod';
@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.scss']
})
export class AdminComponent {
  nombreCategoria: string = '';
  descripcionCategoria: string = '';

  categorias: any[] = [];
  nombre: string = '';
  descripcion: string = '';
  precio: number | null = null;
  talla: string = '';
  color: string = '';
  cantidadInventario: number | null = null;
  categoriaId: number | null = null;
  imagen: File | null = null;
  src: string = '';

  productoId: number | null = null; 
  cantidad: number | null = null; 

  email: string = '';

  productoIdEliminar: number | null = null;
  apiUrl=environment.apiUrl;

  constructor(private http: HttpClient) {}

  ngOnInit() {
    this.http.get<any>(`${this.apiUrl}/get-categorias`).subscribe(
      response => {
        this.categorias = response.categorias;
      },
      error => {
        console.error('Error al obtener categorías:', error);
      }
    );
  }


  crearCategoria() {
    const categoria = {
      nombre: this.nombreCategoria,
      descripcion: this.descripcionCategoria
    };

    this.http.post<any>(`${this.apiUrl}/crear-categoria`, categoria, {withCredentials: true}).subscribe(
      response => {
        console.log('Categoría creada exitosamente:', response);
      },
      error => {
        console.error('Error al crear categoría:', error);
      }
    );
  }




  onFileSelected(event: any) {
    const file: File = event.target.files[0];
    if (file) {
      this.imagen = file;
      this.src = file.name; 
    }
  }

  crearProducto() {
    if (this.imagen) {
      const formData = new FormData();
      formData.append('file', this.imagen, this.imagen.name);

      this.http.post<any>(`${this.apiUrl}/file/upload`, formData, {withCredentials: true}).subscribe(
        response => {
          this.enviarProducto();
        },
        error => {
          console.error('Error al subir la imagen:', error);
        }
      );
    } else {
      this.enviarProducto();
    }
  }

  enviarProducto() {
    const producto = {
      nombre: this.nombre,
      descripcion: this.descripcion,
      precio: this.precio,
      talla: this.talla,
      color: this.color,
      cantidad_inventario: this.cantidadInventario,
      categoria_id: this.categoriaId,
      src: this.src
    };

    this.http.post<any>(`${this.apiUrl}/crear-productos`, producto, {withCredentials: true}).subscribe(
      response => {
        console.log('Producto creado exitosamente:', response);
      },
      error => {
        console.error('Error al crear producto:', error);
      }
    );
  }

  reponerStock() {
    const reponerData = {
      id: this.productoId,
      cantidad: this.cantidad
    };

    this.http.patch<any>(`${this.apiUrl}/reponer_productos`, reponerData, {withCredentials : true}).subscribe(
      response => {
        console.log('Stock repuesto exitosamente:', response);
      },
      error => {
        console.error('Error al reponer stock:', error);
      }
    );
  }

  borrarUsuario() {
    const usuario = {
      email: this.email
    };

    this.http.patch<any>(`${this.apiUrl}/delete-user`, usuario, {withCredentials: true}).subscribe(
      response => {
        console.log('Usuario borrado exitosamente:', response);
      },
      error => {
        console.error('Error al borrar usuario:', error);
      }
    );
  }

  borrarProducto() {
    if (!this.productoIdEliminar) {
      console.error('No se proporcionó el ID del producto a eliminar');
      return;
    }
  
    const producto = { id: this.productoIdEliminar };
  
    this.http.request<any>('delete', `${this.apiUrl}/borrar_producto`, {
      body: producto,
      withCredentials: true
    }).subscribe(
      response => {
        console.log('Producto borrado exitosamente:', response);
      },
      error => {
        console.error('Error al borrar producto:', error);
      }
    );
  }
  

}
