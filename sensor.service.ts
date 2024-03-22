import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SensorService {

  constructor(private http: HttpClient) { }

  // Método para simular la lectura de datos del sensor MQ2
  // En un caso real, esto estaría conectado a un dispositivo físico que proporciona los datos.
  readMQ2Data(): Observable<any> {
    // Simulando una lectura de datos de alcohol
    const alcoholData = Math.random() * 100; // Genera un valor aleatorio entre 0 y 100 (simulando un porcentaje de alcohol)
    return new Observable(observer => {
      setTimeout(() => {
        observer.next(alcoholData);
      }, 1000); // Simula un retraso de 1 segundo para obtener los datos
    });
  }

  // Otros métodos para interactuar con el sensor, como obtener lecturas de otros gases, etc.
}
