import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap, map } from 'rxjs/operators'; // Importar map
import { environment } from 'src/environments/environment.prod';

@Injectable({
  providedIn: 'root'
})
export class OrdenesService {
  private apiUrl=environment.apiUrl;

  constructor(private http: HttpClient) { }

  getOrdenes(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/get-ordenes`, { withCredentials: true });
  }

  getListaProductos(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/lista_productos`, { withCredentials: true });
  }
}
