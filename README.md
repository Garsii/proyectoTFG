# Proyecto TFG: NaturaGym

> **Autora/el**: Álvaro García Jiménez  
> **Licencia**: MIT

---

## Descripción

**NaturaGym** es una plataforma full‑stack para la gestión de entrenamientos y sesiones de clientes en centros deportivos. Incluye:

1. **Backend Laravel** (`naturagym‑laravel`):  
   - API RESTful y panel de administración  
   - Gestión de usuarios, roles, sesiones y suscripciones  
   - Autenticación, control de acceso y notificaciones  
2. **Firmware Arduino** (`arduino`):  
   - Dispositivo IoT para lectura de sensores  
   - Integra datos en tiempo real a través de endpoints API  
   - Envía estadísticas de uso y alertas

Este proyecto forma parte de mi Trabajo de Fin de Grado en Administracion de Sistemas Informaticos en Red, y está pensado para ilustrar una arquitectura **polyglot** con componentes web y de hardware embebido.

---

## Tecnologías

- **PHP 8.3.6+ & Laravel 10**  
- **Base de datos**: MySQL / MariaDB (relacional)  
- **Frontend**: Blade · Tailwind CSS · Vite  
- **IoT y NFC**: ESP32 como bus de comunicacion con PN532 / Arduino C++  
- **Correo**: IONOS SMTP  
- **Servidor**: Apache 2.4 con OpenSSL (HTTP/HTTPS)  

---

## Instalación

1. **Clona el repositorio**  
   ```bash
   git clone https://github.com/Garsii/proyectoTFG.git
   cd proyectoTFG/naturagym-laravel

2. **Configura el entorno**
   ```bash
   cp .env.example .env
   composer install
   npm install
   npm run build    # o `npm run dev` en desarrollo
   php artisan key:generate
3. **Base de datos**
   ```bash
   mysql -u tu_usuario -p naturagym < ../database/schema/naturagym_schema.sql
