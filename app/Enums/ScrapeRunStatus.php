<?php

namespace App\Enums;

enum ScrapeRunStatus: string
{
    case Pending = 'pendiente';
    case Running = 'ejecutando';
    case Completed = 'completado';
    case Error = 'error';
}
