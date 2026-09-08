<?php

namespace App\Enums;

enum MediaKind: string
{
    case Image = 'imagen';
    case Video = 'video';
    case Link = 'enlace';
}
