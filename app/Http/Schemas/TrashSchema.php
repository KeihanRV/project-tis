<?php

namespace App\Http\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Trash',
    title: 'Trash',
    description: 'Model untuk data Sampah yang dilacak',
    type: 'object'
)]
class TrashSchema
{
    #[OA\Property(
        property: 'id',
        type: 'integer',
        format: 'int64',
        description: 'ID unik sampah',
        example: 1
    )]
    public int $id;

    #[OA\Property(
        property: 'name',
        type: 'string',
        description: 'Nama/jenis sampah',
        maxLength: 255,
        example: 'Botol Plastik'
    )]
    public string $name;

    #[OA\Property(
        property: 'category',
        type: 'string',
        description: 'Kategori sampah (organik, anorganik, berbahaya, dll)',
        maxLength: 255,
        example: 'Anorganik'
    )]
    public string $category;

    #[OA\Property(
        property: 'weight',
        type: 'number',
        format: 'float',
        description: 'Berat sampah dalam kilogram',
        example: 2.5
    )]
    public float $weight;

    #[OA\Property(
        property: 'created_at',
        type: 'string',
        format: 'date-time',
        description: 'Tanggal dan waktu pencatatan sampah',
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
