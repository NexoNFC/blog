<?php

namespace App\Enums;

enum ContentStatus: string
{
    case Imported = 'importado';
    case Processing = 'procesando';
    case Draft = 'borrador';
    case Published = 'publicado';
    case Archived = 'archivado';
    case Error = 'error';
}
