// src/app/services/auth/login.service.ts
import { Injectable } from '@angular/core';
import { LoginRequest } from './login.model';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, catchError, throwError } from 'rxjs';
import { User } from './user';

@Injectable({
  providedIn: 'root'
})
export class LoginService {
  private apiUrl = 'http://localhost:8000/login'; // Asegúrate de que esta URL apunte a tu backend

  constructor(private http: HttpClient) { }

  login(credentials: LoginRequest): Observable<User> {
    return this.http.post<User>(this.apiUrl, credentials).pipe(
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
