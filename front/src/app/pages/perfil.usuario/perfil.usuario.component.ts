import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PerfilUsuarioService } from '../../services/perfil-usuario/perfil-usuario.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-perfil-usuario',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './perfil.usuario.component.html',
  styleUrls: ['./perfil.usuario.component.scss']
})
export class PerfilUsuarioComponent implements OnInit {
  nombre = "";
  email = "";
  telefono = "";
  calle = "";
  ciudad = "";
  provincia = "";
  codigo_postal = "";
  pais = "";

  editMode = false;
  originalData: any = {};

  roleUser = localStorage.getItem('user_role') == "ROLE_USER" ? localStorage.getItem('user_role') : null;

  constructor(private perfilUsuarioService: PerfilUsuarioService, private router: Router) { }

  ngOnInit(): void {
    if(this.roleUser != null && this.roleUser != "ROLE_ADMIN"){
      this.getInfoUsuarioActual();
    }else{
      this.router.navigate(['/home']);
    }
  }

  getInfoUsuarioActual(): void {
    this.perfilUsuarioService.getInfoUsuarioActual().subscribe({
      next: (data) => {
        this.nombre = data.nombre;
        this.email = data.email;
        this.telefono = data.telefono;
        if (data.direcciones && data.direcciones.length > 0) {
          const direccion = data.direcciones[0];
          this.calle = direccion.calle;
          this.ciudad = direccion.ciudad;
          this.provincia = direccion.provincia;
          this.codigo_postal = direccion.codigo_postal;
          this.pais = direccion.pais;
        }
        this.originalData = { ...data };
      },
      error: (error) => {
        console.error('Error al obtener la información del usuario:', error);
      }
    });
  }

  onSubmit(): void {
    const updatedInfo = {
      nombre: this.nombre,
      email: this.email,
      telefono: this.telefono,
      calle: this.calle,
      ciudad: this.ciudad,
      provincia: this.provincia,
      codigo_postal: this.codigo_postal,
      pais: this.pais
    };

    this.perfilUsuarioService.updateUserInfo(updatedInfo).subscribe({
      next: (response) => {
        console.log('Información actualizada exitosamente:', response);
        this.editMode = false;
      },
      error: (error) => {
        console.error('Error al actualizar la información:', error);
      }
    });
  }

  cancelEdit(): void {
    this.nombre = this.originalData.nombre;
    this.email = this.originalData.email;
    this.telefono = this.originalData.telefono;
    if (this.originalData.direcciones && this.originalData.direcciones.length > 0) {
      const direccion = this.originalData.direcciones[0];
      this.calle = direccion.calle;
      this.ciudad = direccion.ciudad;
      this.provincia = direccion.provincia;
      this.codigo_postal = direccion.codigo_postal;
      this.pais = direccion.pais;
    }
    this.editMode = false;
  }
}
