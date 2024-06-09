import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PerfilUsuarioService {

  private apiUrl = 'http://localhost:8000/get-info-actual-user'; // URL base de la API
  private apiUrlSet = 'http://localhost:8000/update-user-info';

  constructor(private http: HttpClient) { }

  // Obtener información del usuario actual
  getInfoUsuarioActual(): Observable<any> {
    return this.http.get<any>(this.apiUrl, { withCredentials: true });
  }

  // Actualizar información del usuario
  updateUserInfo(userInfo: any): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json'
    });

    return this.http.put<any>(this.apiUrlSet, userInfo, {withCredentials: true });
  }
}
