import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Categoria {
  id: number;
  nombre: string;
  descripcion: string;
}

@Injectable({
  providedIn: 'root'  // Este decorador registra el servicio en el inyector raíz
})
export class CategoriaServiceService {
  private apiUrl = 'http://localhost:8000/get-categorias';

  constructor(private http: HttpClient) {}

  getCategorias(): Observable<{ categorias: Categoria[] }> {
    return this.http.get<{ categorias: Categoria[] }>(this.apiUrl);
  }
}
