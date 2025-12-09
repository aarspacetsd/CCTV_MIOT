Dokumentasi API Manajemen User

<p align="center">
<img src="https://www.google.com/search?q=https://placehold.co/600x300/293445/FFFFFF%3Ftext%3DAPI%2BDocumentation" alt="Logo API" width="400"/>
</p>

Dokumentasi ini menyediakan panduan lengkap mengenai endpoint API yang tersedia untuk sistem otentikasi dan manajemen profil pengguna. API ini dirancang menggunakan standar RESTful untuk memudahkan integrasi dengan frontend atau aplikasi pihak ketiga.

Daftar Isi

Konfigurasi Dasar

Daftar Endpoint

Otentikasi (Auth)

Profil Pengguna

Struktur Response

Cara Pengujian

Konfigurasi Dasar

Sebelum menggunakan API, pastikan Anda menggunakan konfigurasi berikut:

Base URL: {{base_url_local}} (Contoh: http://localhost:8000)

Global Headers:
Setiap request harus menyertakan header berikut agar server merespons dalam format JSON.

Key

Value

Deskripsi

Accept

application/json

Memastikan response dalam format JSON

Content-Type

application/json

(Untuk POST/PUT/PATCH) Mengirim data dalam format JSON

Daftar Endpoint

Berikut adalah rincian endpoint yang tersedia dalam sistem ini.

Otentikasi (Auth)

1. Register (Pendaftaran)

Mendaftarkan pengguna baru ke dalam sistem.

URL: /api/register

Method: POST

Request Body:

{
    "name": "User Baru",
    "email": "userbaru@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}


Response (Error - Validasi):

{
    "message": "The email has already been taken.",
    "errors": {
        "email": [
            "The email has already been taken."
        ]
    }
}


2. Login (Masuk)

Melakukan otentikasi pengguna untuk mendapatkan token akses (Bearer Token).

URL: /api/login

Method: POST

Request Body:

{
    "email": "aa8072340@gmail.com",
    "password": "12345678"
}


Response (Sukses):

{
    "message": "Login berhasil",
    "user": {
        "id": 11,
        "name": "Hiya",
        "email": "aa8072340@gmail.com"
    },
    "token": "21|LV5orFLdXBu1oto82Im0V7I92Dx8h0BZ1YLgLkkf53bad22c",
    "token_type": "Bearer"
}


Response (Gagal - Kredensial Salah):

{
    "message": "Email atau password salah."
}


Profil Pengguna

Endpoint di bawah ini membutuhkan Header Authorization.
Authorization: Bearer <token_anda>

3. Lihat Profil (Profile)

Mengambil detail informasi pengguna yang sedang login.

URL: /api/profile

Method: GET

Response (Sukses):

{
    "message": "Profile retrieved successfully",
    "user": {
        "id": 11,
        "name": "Hiya",
        "email": "aa8072340@gmail.com",
        "created_at": "07/12/2025 20:47",
        "roles": [
            "user"
        ]
    }
}


4. Update Password

Memperbarui kata sandi pengguna saat ini.

URL: /api/password

Method: PUT

Request Body:

{
    "current_password": "password",
    "password": "password123",
    "password_confirmation": "password123"
}


Response (Sukses):

{
    "message": "Password updated successfully. Please log in again."
}


5. Update Profil

Memperbarui data diri (Nama/Email) pengguna.

URL: /api/profile

Method: PATCH

Request Body:

{
    "name": "Administrator12",
    "email": "admin@gmail.com"
}


Response (Sukses):

{
    "message": "Profile updated successfully",
    "user": {
        "id": 5,
        "name": "Administrator12",
        "email": "admin@gmail.com",
        "created_at": "17/08/2025 09:28"
    }
}

6. Get Image

{{base_url_local}}/api/images/41/history?date=2025-12-07&hour=00&minute=48&chunk=1

Method: GET
Bearer = True

Header key Accept Value Application/json

{
    "level": "gallery",
    "camera_id": 41,
    "filter": {
        "date": "2025-12-07",
        "hour": "00",
        "minute": "48",
        "chunk": "1"
    },
    "items": [
        {
            "id": 13667,
            "file_name": "004800_69346c5027408.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c5027408.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13668,
            "file_name": "004800_69346c5047f18.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c5047f18.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13669,
            "file_name": "004800_69346c5057e06.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c5057e06.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13670,
            "file_name": "004800_69346c5075975.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c5075975.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13671,
            "file_name": "004800_69346c5082a28.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c5082a28.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13672,
            "file_name": "004800_69346c50a3d38.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c50a3d38.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13673,
            "file_name": "004800_69346c50b6268.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c50b6268.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13674,
            "file_name": "004800_69346c50da323.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c50da323.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13675,
            "file_name": "004800_69346c50eb51b.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c50eb51b.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13666,
            "file_name": "004800_69346c5015df1.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004800_69346c5015df1.jpg",
            "captured_at": "2025-12-07 00:48:00"
        },
        {
            "id": 13681,
            "file_name": "004801_69346c5183166.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c5183166.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13685,
            "file_name": "004801_69346c51e4423.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c51e4423.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13684,
            "file_name": "004801_69346c51d6517.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c51d6517.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13683,
            "file_name": "004801_69346c51bf88c.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c51bf88c.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13682,
            "file_name": "004801_69346c51ad3c4.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c51ad3c4.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13680,
            "file_name": "004801_69346c5176590.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c5176590.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13679,
            "file_name": "004801_69346c515524a.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c515524a.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13678,
            "file_name": "004801_69346c5144b04.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c5144b04.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13677,
            "file_name": "004801_69346c51274c4.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c51274c4.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13676,
            "file_name": "004801_69346c511282b.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004801_69346c511282b.jpg",
            "captured_at": "2025-12-07 00:48:01"
        },
        {
            "id": 13686,
            "file_name": "004802_69346c522ab2e.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004802_69346c522ab2e.jpg",
            "captured_at": "2025-12-07 00:48:02"
        },
        {
            "id": 13687,
            "file_name": "004802_69346c524494f.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004802_69346c524494f.jpg",
            "captured_at": "2025-12-07 00:48:02"
        },
        {
            "id": 13688,
            "file_name": "004802_69346c5264855.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004802_69346c5264855.jpg",
            "captured_at": "2025-12-07 00:48:02"
        },
        {
            "id": 13689,
            "file_name": "004802_69346c527ba92.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004802_69346c527ba92.jpg",
            "captured_at": "2025-12-07 00:48:02"
        },
        {
            "id": 13690,
            "file_name": "004802_69346c528c9dd.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004802_69346c528c9dd.jpg",
            "captured_at": "2025-12-07 00:48:02"
        },
        {
            "id": 13691,
            "file_name": "004802_69346c52ace9e.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004802_69346c52ace9e.jpg",
            "captured_at": "2025-12-07 00:48:02"
        },
        {
            "id": 13692,
            "file_name": "004802_69346c52bd6d8.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004802_69346c52bd6d8.jpg",
            "captured_at": "2025-12-07 00:48:02"
        },
        {
            "id": 13693,
            "file_name": "004802_69346c52db020.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004802_69346c52db020.jpg",
            "captured_at": "2025-12-07 00:48:02"
        },
        {
            "id": 13694,
            "file_name": "004802_69346c52e8b86.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004802_69346c52e8b86.jpg",
            "captured_at": "2025-12-07 00:48:02"
        },
        {
            "id": 13695,
            "file_name": "004803_69346c531a4d0.jpg",
            "url": "/storage/camera_images/e09fb74e-acd2-4ea1-b753-2f41be690f04/2025-12-07/004803_69346c531a4d0.jpg",
            "captured_at": "2025-12-07 00:48:03"
        }
    ],
    "pagination": null
}

Struktur Response

API ini menggunakan format response standar JSON untuk semua permintaan.

Sukses (200 OK / 201 Created):
Akan selalu mengembalikan properti message dan data terkait (misalnya user atau token).

Gagal / Error (401 / 422):

401 Unauthorized: Token tidak valid atau kadaluarsa.

422 Unprocessable Entity: Validasi input gagal (disertai field errors).

Cara Pengujian

Anda dapat menggunakan Postman atau Insomnia untuk menguji endpoint ini.

Environment Setup: Buat variable base_url di Postman sesuai dengan URL lokal Anda.

Login Flow:

Lakukan request ke endpoint Login.

Salin token dari response.

Untuk endpoint Profile, pilih tab Auth -> Bearer Token -> Tempel token tersebut.

Lisensi

Dokumentasi dan kode sumber API ini dilisensikan di bawah Lisensi MIT.
