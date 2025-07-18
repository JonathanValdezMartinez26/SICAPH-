<?php

namespace Controllers;

use Core\Controller;
use Models\Formatos as FormatosDAO;

class Formatos extends Controller
{
    public function Cultiva()
    {
        $script = <<<HTML
            <script>
                const tabla = "#historialFormatos"

                const subirFormato = () => {
                    confirmarMovimiento("¿Desea subir este nuevo formato?").then((continuar) => {
                        if (!continuar) return

                        const archivo = $("#empresa").prop("files")[0]
                        const nombre = $("#nombre").val().trim()
                        const fechas = getInputFechas("#fechasVigencia", true, false)

                        const formData = new FormData();
                        formData.append("nombre", nombre);
                        formData.append("archivo", archivo);

                        consultaServidor("/Formatos/registrarFormatoCultiva", formData, (respuesta) => {
                            if (!respuesta.success) return showError(respuesta.mensaje)

                            $("#modalSubirFormato").modal("hide")
                            showSuccess(respuesta.mensaje)
                        }, {
                            procesar: false,
                            tipoContenido: false
                        })
                    })
                }

                $(document).ready(function() {
                    setInputFechas("#filtroFechas", { rango: true, iniD: -30 })
                    setInputFechas("#fechasVigencia", { rango: true, enModal: true, finD: 365, minD: 0 })
                    configuraTabla(tabla)

                    $("#btnAgregar").on("click", function() {
                        $("#modalSubirFormato").modal("show")
                    })

                    $("#subirFormato").on("click", subirFormato)
                })
            </script>
        HTML;

        self::set("titulo", "Formatos CULTIVA");
        self::set("script", $script);
        self::render("formatos_cultiva");
    }

    public function registrarFormatoCultiva()
    {
        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            return self::respuestaJSON(self::respuesta(false, 'Archivo no recibido o error en la carga.'));
        }

        if ($_FILES['archivo']['size'] > 5 * 1024 * 1024) {
            return self::respuestaJSON([
                'success' => false,
                'mensaje' => "El archivo {$_FILES['archivo']['name']} excede el tamaño máximo permitido de 5 MB."
            ]);
        }

        $datos = [
            'nombre' => $_POST['nombre'] ?? '',
            'archivo' => fopen($_FILES['archivo']['tmp_name'], 'rb'),
        ];

        $resultado = FormatosDAO::registraFormatoCultiva($datos);
        if (is_resource($datos['archivo'])) fclose($datos['archivo']);

        self::respuestaJSON($resultado);
    }

    public function MCM()
    {
        $script = <<<HTML
            <script>
                const tabla = "#historialFormatos"

                $(document).ready(function() {
                    setInputFechas("#filtroFechas", { rango: true, iniD: -30 })
                    setInputFechas("#fechasVigencia", { rango: true })
                    configuraTabla(tabla)

                    $("#btnAgregar").on("click", function() {
                        $("#modalSubirFormato").modal("show")
                    })
                })
            </script>
        HTML;

        self::set("titulo", "Formatos MCM");
        self::set("script", $script);
        self::render("formatos_mcm");
    }
}
