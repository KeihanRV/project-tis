<?php

namespace App\Http\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    title: 'User',
    description: 'Model untuk User/Pengguna dalam sistem TrashTrack',
    type: 'object'
)]
class UserSchema
{
    #[OA\Property(
        property: 'id',
        type: 'integer',
        format: 'int64',
        description: 'ID unik pengguna',
        example: 1
    )]
    public int $id;

    #[OA\Property(
        property: 'name',
        type: 'string',
        description: 'Nama lengkap pengguna',
        maxLength: 100,
        example: 'John Doe'
    )]
    public string $name;

    #[OA\Property(
        property: 'email',
        type: 'string',
        format: 'email',
        description: 'Alamat email pengguna',
        example: 'john@example.com'
    )]
    public string $email;

    #[OA\Property(
        property: 'created_at',
        type: 'string',
        format: 'date-time',
        description: 'Tanggal dan waktu pembuatan akun',
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
