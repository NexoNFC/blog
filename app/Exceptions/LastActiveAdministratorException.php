<?php

namespace App\Exceptions;

use RuntimeException;

class LastActiveAdministratorException extends RuntimeException
{
    public static function cannotDelete(): self
    {
        return new self('No se puede eliminar al último administrador activo. El sistema debe conservar al menos un administrador activo.');
    }

    public static function cannotDeactivate(): self
    {
        return new self('No se puede desactivar al último administrador activo. El sistema debe conservar al menos un administrador activo.');
    }

    public static function cannotChangeRole(): self
    {
        return new self('No se puede quitar el rol de administrador al último administrador activo. El sistema debe conservar al menos un administrador activo.');
    }
}
