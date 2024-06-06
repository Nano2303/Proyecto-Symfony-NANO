// src/app/services/auth/login.service.ts
import { Injectable } from '@angular/core';
import { LoginRequest } from './login.model';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, catchError, throwError, tap } from 'rxjs';
import { User } from './user';

@Injectable({
  providedIn: 'root'
})
export class LoginService {
  private apiUrl = 'http://localhost:8000/login'; // Asegúrate de que esta URL apunte a tu backend
  private URLAPI = 'http://localhost:8000';

  constructor(private http: HttpClient) { }

  login(credentials: LoginRequest): Observable<any> {
    return this.http.post<any>(this.apiUrl, credentials,{ withCredentials: true }).pipe(
      tap(response =>{

        if (response.rol && response.email) {
          localStorage.setItem('user_role', response.rol);
          localStorage.setItem('user_email', response.email);
        }

      }),
      catchError(this.handleError)
    );
  }

  logout(): Observable<any> {
    return this.http.post<any>(`${this.URLAPI}/logout`, {},{ withCredentials: true }).pipe(
      tap((response) => {
        console.log(response);
        localStorage.removeItem('user_role');
        localStorage.removeItem('user_email');
      }),
      catchError(this.handleError)
    );
  }

  private handleError(error: HttpErrorResponse) {
    if (error.status === 0) {
      console.error('Se produjo un error:', error.error);
    } else {
      console.error(`El backend retornó el código de estado ${error.status}, body was: `, error.error);
    }
    return throwError(() => new Error('Algo falló, por favor intente nuevamente.'));
  }

}
