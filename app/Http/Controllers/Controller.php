<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'TrashTrack API',
    version: '1.0.0',
    description: 'TrashTrack adalah API untuk pelacakan sampah, pelaporan insiden, dan manajemen data sampah pengguna.'
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Gunakan token JWT pada header Authorization: Bearer {token}'
)]
abstract class Controller
{
    // Tambahkan method umum jika diperlukan
}
