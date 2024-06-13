import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.prod';

@Injectable({
  providedIn: 'root'
})
export class RecoverPassService {
  private apiUrl=environment.apiUrl; //'http://localhost:8000/enviar-codigo'; // URL para enviar el código de recuperación
  // 'http://localhost:8000/recuperar-contrasena'; // URL para cambiar la contraseña

  constructor(private http: HttpClient) { }

  recoverPassword(data: { email: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/enviar-codigo`, data,{ withCredentials: true });
  }

  setNewPassword(data: { codigo: string, nueva_contrasena: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/recuperar-contrasena`, data,{ withCredentials: true })
  }
}