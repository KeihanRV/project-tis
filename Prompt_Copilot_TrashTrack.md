# Prompt GitHub Copilot untuk Testing Otomatis TrashTrack

Gunakan prompt ini untuk menghasilkan file pengujian otomatis pada aplikasi **TrashTrack** dengan route API berikut:

- `POST /login`
- `POST /register`
- `POST /v1/logout`
- `GET /v1/profile`
- `GET /v1/trash`
- `GET /v1/trash/{id}`
- `POST /v1/trash`
- `GET /v1/reports`
- `GET /v1/reports/{id}`
- `POST /v1/reports`
- `PUT /v1/reports/{id}`
- `PUT /v1/reports/{id}/status`
- `DELETE /v1/reports/{id}`
- `POST /v1/reports/search`
- `POST /v1/reports/filter`

---

## 1. Prompt Utama

> "Generate a complete Laravel Feature Test using Pest or PHPUnit for the TrashTrack application based on the routes in `routes/api.php`.
>
> The test should cover:
>
> 1. Authentication: `register`, `login`, `profile`, and `logout`.
> 2. Trash endpoints: `index`, `show`, and `create`.
> 3. Report endpoints: full CRUD, status update, search, and filter.
> 4. Authorization: ensure all `/v1/*` routes are protected by `auth:api`.
>
> Technical requirements:
>
> - Use `RefreshDatabase`.
> - Use `$this->actingAs($user, 'api')` for authenticated requests.
> - Validate JSON response structure and HTTP status codes.
> - Include negative tests for unauthenticated access and missing required fields."

---

## 2. Prompt Spesifik Modul

### A. AuthController

> "Create a Pest feature test for `AuthController` covering:
>
> - `POST /login`
> - `POST /register`
> - `GET /v1/profile`
> - `POST /v1/logout`
>
> Test successful and failed login, plus protected access to `profile` and `logout`."

### B. TrashController

> "Generate feature tests for `TrashController` with:
>
> - `GET /api/v1/trash`
> - `GET /api/v1/trash/{id}`
> - `POST /api/v1/trash`
>
> Ensure authenticated requests succeed and unauthenticated requests are blocked."

### C. ReportController

> "Generate feature tests for `ReportController` including:
>
> - `GET /api/v1/reports`
> - `POST /api/v1/reports`
> - `GET /api/v1/reports/{id}`
> - `PUT /api/v1/reports/{id}`
> - `PUT /api/v1/reports/{id}/status`
> - `DELETE /api/v1/reports/{id}`
> - `POST /api/v1/reports/search`
> - `POST /api/v1/reports/filter`
>
> Include request validation and authenticated access using bearer token auth."

---

## 3. Prompt Edge Cases dan Validasi

> "Write negative tests for the TrashTrack API:
>
> 1. Unauthenticated users cannot access any `/v1/*` route.
> 2. `register` fails when email already exists.
> 3. `GET /api/v1/reports/{id}` returns 404 for invalid IDs.
> 4. `PUT /api/v1/reports/{id}/status` rejects invalid status values."

---

## 4. Cara Menggunakan

1. Buka `routes/api.php`.
2. Buka GitHub Copilot Chat atau inline Copilot.
3. Tempel prompt di atas.
4. Jika Copilot menghasilkan kode, simpan di folder `tests/Feature/`.

---

## 5. Contoh Hasil Copilot (Singkat)

```php
public function test_authenticated_user_can_create_report()
{
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'api')
        ->postJson('/api/v1/reports', [
            'title' => 'Sampah menumpuk',
            'description' => 'Tumpukan sampah plastik di jalan.',
            'location' => 'Jl. Merdeka',
        ]);

    $response->assertStatus(201)
             ->assertJsonPath('data.title', 'Sampah menumpuk');
}
```
