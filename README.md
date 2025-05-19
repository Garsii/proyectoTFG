# Proyecto TFG: NaturaGym

> **Autora/el**: Álvaro García Jiménez  
> **Licencia**: MIT

---

## 📖 Descripción

**NaturaGym** es una plataforma full‑stack para la gestión de entrenamientos y sesiones de clientes en centros deportivos. Incluye:

1. **Backend Laravel** (`naturagym‑laravel`):  
   - API RESTful y panel de administración  
   - Gestión de usuarios, roles, sesiones y entrenamientos  
   - Autenticación, control de acceso y notificaciones  
2. **Firmware Arduino** (`arduino`):  
   - Dispositivo IoT para lectura de sensores  
   - Integra datos en tiempo real a través de endpoints API  
   - Envía estadísticas de uso y alertas

Este proyecto forma parte de mi Trabajo de Fin de Grado en Informática, y está pensado para ilustrar una arquitectura **polyglot** con componentes web, móvil y de hardware embebido.

---

## 🗂️ Estructura del repositorio

```text
proyectoTFG/
├── arduino/                     # Código para microcontrolador
│   ├── src/                     # Sketch principal (Arduino IDE)
│   └── lib/                     # Librerías adicionales
├── naturagym‑laravel/           # Aplicación Laravel (v10+)
│   ├── app/                     # Lógica de negocio
│   ├── config/                  # Configuración del framework
│   ├── database/                # Migraciones, seeders y factories
│   ├── public/                  # Punto de entrada (DocumentRoot)
│   ├── resources/               # Vistas Blade y assets
│   └── routes/                  # Definición de rutas web y API
├── .env.example                 # Variables de entorno de muestra
├── README.md                    # Este fichero
└── LICENSE                      # Licencia MIT

