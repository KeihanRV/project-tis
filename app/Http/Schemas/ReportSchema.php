<?php

namespace App\Http\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Report',
    title: 'Report',
    description: 'Model untuk Laporan/Pelaporan insiden sampah',
    type: 'object'
)]
class ReportSchema
{
    #[OA\Property(
        property: 'id',
        type: 'integer',
        format: 'int64',
        description: 'ID unik laporan',
        example: 1
    )]
    public int $id;

    #[OA\Property(
        property: 'user_id',
        type: 'integer',
        format: 'int64',
        description: 'ID pengguna yang membuat laporan',
        example: 1
    )]
    public int $user_id;

    #[OA\Property(
        property: 'title',
        type: 'string',
        description: 'Judul laporan',
        maxLength: 255,
        example: 'Sampah Menumpuk di Taman Kota'
    )]
    public string $title;

    #[OA\Property(
        property: 'description',
        type: 'string',
        description: 'Deskripsi detail laporan',
        example: 'Terdapat tumpukan sampah besar di sudut taman utara yang belum dibersihkan selama 3 hari.'
    )]
    public string $description;

    #[OA\Property(
        property: 'status',
        type: 'string',
        enum: ['pending', 'in_progress', 'completed', 'rejected'],
        description: 'Status laporan',
        example: 'pending'
    )]
    public string $status;

    #[OA\Property(
        property: 'created_at',
        type: 'string',
        format: 'date-time',
        description: 'Tanggal dan waktu pembuatan laporan',
        example: '2026-05-09T10:30:00.000000Z'
    )]
    public string $created_at;

    #[OA\Property(
        property: 'updated_at',
        type: 'string',
        format: 'date-time',
        description: 'Tanggal dan waktu pembaruan terakhir',
        example: '2026-05-09T10:30:00.000000Z'
    )]
    public string $updated_at;
}
