# Proyecto de Gestión de PNF

Este proyecto es un sistema de gestión de Programas Nacionales de Formación (PNF) y sus ubicaciones asociadas, incluyendo aldeas y parroquias. A continuación se detallan las características y la estructura del proyecto.

## Estructura del Proyecto

```
edixson-1
├── .htaccess
├── aldeas.php
├── cargar_aldeas.php
├── cargar_municipios.php
├── cargar_parroquias.php
├── dashboard.php
├── editar.php
├── gestion_pnf_1.pdf
├── gestion_pnf_img.png
├── gestion_pnf.sql
├── guardar_preinscripcion.php
├── index.php
├── inicio.html
├── preinscripcion.php
├── preinscritos.php
├── README.md
├── css/
│   └── style.css
├── img/
│   ├── banner-1.png
│   ├── banner-2.png
│   └── banner-3.png
├── includes/
│   ├── auth.php
│   ├── db.php
│   ├── menu-publico.php
│   └── menu.php
├── pnf/
│   ├── agregar_pnf.php
│   ├── editar_pnf.php
│   └── pnf.php
└── ubicaciones/
    ├── agregar_aldea.php
    ├── aldeas.php
    ├── estados.php
    ├── gestionar_aldea_pnf.php
    ├── municipios.php
    └── parroquias.php
```

## Descripción de Archivos

Claro, aquí tienes la estructura del proyecto en formato Markdown:

# Estructura del Proyecto

## Archivos Raíz

* **.htaccess**: Configuración del servidor Apache para redirigir o proteger rutas.
* **aldeas.php**: Muestra las aldeas disponibles y los PNF asociados.
* **cargar_aldeas.php**, **cargar_municipios.php**, **cargar_parroquias.php**: Scripts para cargar datos dinámicos de aldeas, municipios y parroquias.
* **dashboard.php**: Página principal del sistema tras iniciar sesión, con accesos a las secciones de gestión.
* **editar.php**: Permite editar registros genéricos (probablemente relacionado con aldeas o PNF).
* **gestion_pnf.sql**: Script SQL para crear y poblar la base de datos del sistema.
* **guardar_preinscripcion.php**: Procesa y guarda los datos de preinscripción en la base de datos.
* **index.php**: Página de inicio de sesión del sistema.
* **inicio.html**: Página pública de inicio con enlaces a las secciones principales.
* **preinscripcion.php**: Formulario para que los usuarios realicen su preinscripción.
* **preinscritos.php**: Muestra una lista de usuarios preinscritos con sus datos.

## Directorio `css`

* **style.css**: Archivo de estilos CSS para el diseño del sistema.

## Directorio `img`

* **banner-1.png**, **banner-2.png**, **banner-3.png**: Imágenes utilizadas en el diseño del sistema.

## Directorio `includes`

* **auth.php**: Funciones de autenticación y control de acceso.
* **db.php**: Configuración y conexión a la base de datos mediante PDO.
* **menu-publico.php**: Menú de navegación para usuarios no autenticados.
* **menu.php**: Menú de navegación para usuarios autenticados.

## Directorio `pnf`

* **agregar_pnf.php**: Formulario para agregar un nuevo Programa Nacional de Formación (PNF).
* **editar_pnf.php**: Permite editar los datos de un PNF existente.
* **pnf.php**: Lista los PNF registrados y permite gestionarlos (activar, desactivar, eliminar).

## Directorio `ubicaciones`

* **agregar_aldea.php**: Formulario para agregar una nueva aldea.
* **aldeas.php**: Lista las aldeas registradas y permite gestionarlas.
* **estados.php**: Gestión de los estados disponibles en el sistema.
* **gestionar_aldea_pnf.php**: Asigna PNF a aldeas específicas.
* **municipios.php**: Gestión de los municipios asociados a los estados.
* **parroquias.php**: Gestión de las parroquias asociadas a los municipios.

## Instrucciones de Instalación

1. Clona o descarga el repositorio en tu servidor local.
2. Importa el archivo `gestion_pnf.sql` en tu gestor de base de datos para crear la base de datos y las tablas necesarias.
3. Configura los datos de conexión a la base de datos en `includes/db.php`.
4. Accede a `localhost/edixson-1` en tu navegador para iniciar.

## Uso

- Inicia sesión con tus credenciales.
- Navega a través del dashboard para gestionar aldeas y PNF.
- Utiliza las opciones disponibles para agregar, editar o eliminar registros según sea necesario.

## Notas

- Asegúrate de tener un servidor web y un gestor de base de datos configurados correctamente.
- Este proyecto es un sistema básico y puede ser ampliado con más funcionalidades según sea necesario.