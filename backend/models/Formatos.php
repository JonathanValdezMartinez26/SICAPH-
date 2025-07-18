<?php

namespace Models;

use Core\Model;
use Core\Database;

class Formatos extends Model
{
    public static function registraFormatoCultiva($datos)
    {
        $qryA = <<<SQL
            INSERT INTO REPOSITORIO_CAPITALH (ARCHIVO, NOMBRE)
            VALUES (EMPTY_BLOB(), :nombre)
            RETURNING ARCHIVO, ID INTO :archivo, :id
        SQL;

        $valA = [
            'nombre' => $datos['nombre']
        ];

        $retA = [
            'archivo' => [
                'valor' => $datos['archivo'],
                'tipo' => \PDO::PARAM_LOB
            ],
            'id' => [
                'valor' => '',
                'tipo' => \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT,
                'largo' => 40
            ]
        ];

        try {
            $db = new Database('cultiva');
            $db->CRUD($qryA, $valA, $retA);

            if (!$retA['id']['valor']) throw new \Exception("Error al insertar el formato.");

            return self::resultado(true, 'Formato registrado correctamente.', ['formatoId' => $retA['id']['valor']]);
        } catch (\Exception $e) {
            $db->rollback();
            return self::resultado(false, 'Error al registrar el formato.', null, $e->getMessage());
        }
    }
}
