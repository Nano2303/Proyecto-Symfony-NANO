import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RecoverPassService {
  private apiUrl = 'http://localhost:8000/enviar-codigo'; // URL para enviar el código de recuperación
  private newPassUrl = 'http://localhost:8000/recuperar-contrasena'; // URL para cambiar la contraseña

  constructor(private http: HttpClient) { }

  recoverPassword(data: { email: string }): Observable<any> {
    return this.http.post(this.apiUrl, data);
  }

  setNewPassword(data: { codigo: string, nueva_contrasena: string }): Observable<any> {
    return this.http.post(this.newPassUrl, data,{ withCredentials: true })
  }
}