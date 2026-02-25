# ⚓ MiUMC - Portal Estudiantil Universitario

Desarrollado por: EVG Dev Studio

Institución: Universidad Nacional Experimental Marítima del Caribe (UMC)

Bienvenido al nuevo Portal Estudiantil MiUMC.

# 🛠️ Stack Tecnológico

El proyecto está construido bajo una arquitectura separada (Frontend y Backend) para garantizar escalabilidad y seguridad:

Frontend (Interfaz de Usuario):

Framework: React.js + Vite (Rendimiento ultrarrápido).

Estilos: Tailwind CSS (Diseño utilitario y responsive).

Enrutamiento: React Router Dom.

Iconografía: Lucide React.

Backend (Servidor y API REST):

Entorno: Node.js.

Framework: Express.js.

Base de Datos: MySQL (Driver mysql2/promise).

Utilidades: cors, dotenv, nodemon.

# 🚀 Guía de Instalación y Configuración

Sigue estos pasos exactamente en orden para levantar el entorno local en tu computadora sin errores de compatibilidad.

# 1. Requisitos Previos

Antes de clonar el código, asegúrate de tener instaladas estas herramientas:

Git: Para clonar y manejar los repositorios.

Node.js (Versión 18 o superior): Incluye npm, que usaremos para instalar dependencias.

Laragon o XAMPP: Servidor local para ejecutar MySQL. (Recomendamos Laragon por su velocidad).

DBeaver o phpMyAdmin: Para administrar visualmente la base de datos.

# 2. Configurar la Base de Datos (MySQL)

Abre Laragon (o XAMPP) e inicia los servicios de MySQL (Puerto 3306).

Abre tu gestor de base de datos (Ej: DBeaver) y conéctate a tu servidor local (Usuario: root, sin contraseña por defecto).

Crea una nueva base de datos ejecutando el comando:

CREATE DATABASE miumc_db;


Ejecuta o importa el script maestro llamado miumc_db.sql (está en el repositorio frentond). Este archivo creará todas las tablas e insertará los pensums oficiales y el usuario administrador.

# 3. Clonar y Levantar el Backend (API)

Abre tu terminal y ejecuta los siguientes comandos:

# 1. Clonar el repositorio del Backend
git clone [https://github.com/VJWild/miumc-backend.git](https://github.com/VJWild/miumc-backend.git)
cd miumc-backend

# 2. Instalar dependencias 
 (Usamos --legacy-peer-deps para evitar cualquier conflicto de versiones entre paquetes)
npm install --legacy-peer-deps


Paso Clave: Variables de Entorno Crea un archivo llamado .env en la raíz de la carpeta miumc-backend y pega lo siguiente (ajusta la contraseña si tu MySQL local la requiere):

DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=miumc_db
PORT=5000


#Iniciar el Servidor:

npm run dev


Verás un mensaje indicando: 🚀 Servidor MiUMC corriendo en http://localhost:5000. ¡Déjalo corriendo en esta terminal!
Si quieres saber si todo funciona correctamente. Coloca esta direccion en el navegador: http://localhost:5000/api/subjects/1. Y si te muestra los datos de la BDD entonces todo está al tiroteo.

# 4. Clonar y Levantar el Frontend (React + Vite)

Con el backend encendido, abre una nueva pestaña en tu terminal (para no apagar el servidor), sal de la carpeta del backend y procede con el frontend:

# 1. Volver a la carpeta raíz de tus proyectos
cd ..

# 2. Clonar el repositorio del Frontend
git clone [https://github.com/VJWild/miumc-frontend.git](https://github.com/VJWild/miumc-frontend.git)
cd miumc-frontend

# 3. Instalar dependencias
# (Crítico usar --legacy-peer-deps para evitar conflictos entre React y Tailwind)
npm install --legacy-peer-deps

# 4. Iniciar la Interfaz Web
npm run dev


La terminal te mostrará un enlace local (usualmente http://localhost:5173). Haz Ctrl+Clic para abrirlo en tu navegador.

🎉 ¡Listo! Ya tienes MiUMC corriendo completamente en tu equipo.

#¡¡Instalación de nuevas librerías: Si instalas un paquete nuevo, avisa al equipo para que los demás ejecuten "npm install --legacy-peer-deps" al bajar los cambios!!
