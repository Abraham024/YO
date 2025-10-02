<?php
// Inicia la sesión al principio de todo
session_start();

// Verifica si el usuario ha iniciado sesión Y si es un administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    // Si no, redirígelo a la página de login
    header('Location: login.php');
    exit;
}

// Incluye el archivo de conexión a la base de datos
require_once('../conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sección Administrativa</title>
    <style>
        :root {
            --color1: #F27A4C; /* Acento */
            --color2: #F7571A; /* Acento Alt */
            --color3: #01050E; /* Texto más fuerte */
            --color4: #3F4349; /* Texto fuerte */
            --color5: #595B5F; /* Texto medio */
            --color6: #747575; /* Texto sutil */
            --color7: #E6E1DB; /* Fondo sutil */
            --color8: #FAF7F0; /* Fondo más claro */
            --color9: #FFFFFF; /* Blanco */
        }

        body {
            font-family: 'Arial', sans-serif;       /*color fondo*/
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;                              /*color seccion Administrativa hoja*/
            background-color: #FFFFFF;            
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
        }

        h3, h3 {                               /*letra de los titulos*/
            color: #2c3e50;                 /*color titulo Seccion Administrativa*/
            font-weight: 500;
            margin-top: 0;
        }

        h1 {
            font-size: 2em;
        }

        h2 {
            font-size: 1.5em;
        }

        nav {
            background-color: var(--color1);
            padding: 10px 0;
            border-radius: 5px;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-start;
        }

        nav li a {
            display: block;
            color: var(--color9);
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s, color 0.3s;
        }

        nav li a:hover {
            background-color: var(--color2);
            color: var(--color9);
            border-radius: 5px;
        }

        hr {
            border: 0;
            height: 1px;
            background-color: var(--color7);
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: var(--color9);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--color7);
        }

        th {
            background-color: var(--color4);
            color: var(--color9);
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: var(--color8);
        }

        tr:hover {
            background-color: var(--color7);
        }

        .mensaje-exito {
            color: var(--color3);
            background-color: #c8e6c9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .mensaje-error {
            color: var(--color3);
            background-color: #ffcdd2;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .form-container {
            background: var(--color8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--color5);
        }

        input[type="text"], 
        input[type="email"], 
        input[type="tel"], 
        input[type="password"], 
        input[type="number"], 
        button {
            width: 100%;
            padding: 12px;
            margin: 5px 0 15px 0;
            display: inline-block;
            border: 1px solid var(--color6);
            border-radius: 5px;
            box-sizing: border-box;
            color: var(--color4);
        }

        button {
            background-color: var(--color1);
            color: var(--color9);
            border: none;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: var(--color2);
        }

        a {
            color: var(--color1);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sección Administrativa</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="menu.php">Catálogo de Zonas</a></li>
                <li><a href="atletas.php">Catálogo de Atletas</a></li>
                <li><a href="reservaciones.php">Actualizar Reservaciones</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
        <hr>
