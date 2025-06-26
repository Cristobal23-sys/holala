body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #b8d4e3;
        }

        .form-table td {
            padding: 8px 12px;
            vertical-align: middle;
            border: none;
        }

        .form-table label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .form-table input[type="text"],
        .form-table input[type="email"],
        .form-table input[type="number"] {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #666;
            border-radius: 3px;
            font-size: 14px;
            box-sizing: border-box;
            background-color: white;
        }

        .form-table input[type="text"]:focus,
        .form-table input[type="email"]:focus,
        .form-table input[type="number"]:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 3px rgba(74, 144, 226, 0.3);
        }

        /* Fila superior - Nombre y Apellido */
        .row-name-apellido .name-cell {
            width: 45%;
        }

        .row-name-apellido .apellido-cell {
            width: 55%;
        }

        /* Segunda fila - Edad, Sexo, Correo */
        .row-edad-sexo-correo .edad-cell {
            width: 15%;
        }

        .row-edad-sexo-correo .sexo-cell {
            width: 20%;
        }

        .row-edad-sexo-correo .correo-cell {
            width: 50%;
        }

        .row-edad-sexo-correo .buscar-cell {
            width: 15%;
            text-align: center;
            vertical-align: middle;
        }

        /* Tercera fila - Labels pequeños */
        .row-labels .label-cell {
            width: 12%;
        }

        .row-labels .empty-cell {
            width: 52%;
        }

        .row-labels input[type="text"] {
            width: 100%;
            padding: 4px 6px;
            font-size: 12px;
        }

        /* Cuarta fila - Final 1 y Final 2 */
        .row-final .final1-cell {
            width: 47%;
        }

        .row-final .final2-cell {
            width: 53%;
        }

        /* Botón de búsqueda */
        .search-button {
            background-color: #4a90e2;
            color: white;
            border: 2px solid #366bb3;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .search-button:hover {
            background-color: #366bb3;
        }

        .search-icon {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 4px;
            }

            .form-table {
                font-size: 12px;
            }

            .form-table td {
                padding: 6px 8px;
            }

            .form-table input[type="text"],
            .form-table input[type="email"],
            .form-table input[type="number"] {
                padding: 4px 6px;
                font-size: 12px;
            }

            .search-button {
                padding: 6px 12px;
                font-size: 12px;
                min-height: 35px;
            }

            /* Ajustar anchos en móvil */
            .row-name-apellido .name-cell,
            .row-name-apellido .apellido-cell {
                width: 50%;
            }

            .row-edad-sexo-correo .edad-cell {
                width: 20%;
            }

            .row-edad-sexo-correo .sexo-cell {
                width: 25%;
            }

            .row-edad-sexo-correo .correo-cell {
                width: 40%;
            }

            .row-edad-sexo-correo .buscar-cell {
                width: 15%;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .form-table {
                font-size: 11px;
            }

            .form-table td {
                padding: 4px 6px;
            }

            .search-button {
                padding: 4px 8px;
                font-size: 11px;
                min-height: 30px;
            }

            .search-icon {
                width: 14px;
                height: 14px;
            }
        }