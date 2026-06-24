# Dokumentasi API

Base URL: `http://localhost:8000/api/v1`

## Autentikasi

Semua endpoint kecuali register, login, dan daftar layanan memerlukan autentikasi token JWT.

Sertakan token dalam header Authorization:
```
Authorization: Bearer {your_token}
```

---

## Endpoints Autentikasi

### 1. Registrasi User

**POST** `/auth/register`

Mendaftarkan user baru dan mendapatkan token JWT.

**Request Body:**
```json
{
  "name": "string (wajib)",
  "email": "string (wajib, email, unik)",
  "password": "string (wajib, min:8)",
  "password_confirmation": "string (wajib, harus sama dengan password)",
  "role": "string (opsional, admin|citizen, default: citizen)",
  "phone": "string (opsional, max:20)",
  "address": "string (opsional, max:1000)"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "citizen",
      "phone": "081234567890",
      "address": "Jl. Merdeka No. 1",
      "created_at": "2026-06-24T03:37:04.000000Z",
      "updated_at": "2026-06-24T03:37:04.000000Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

---

### 2. Login

**POST** `/auth/login`

Autentikasi user dan mendapatkan token JWT.

**Request Body:**
```json
{
  "email": "string (wajib, email)",
  "password": "string (wajib)"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "citizen",
      "phone": "081234567890",
      "address": "Jl. Merdeka No. 1",
      "created_at": "2026-06-24T03:37:04.000000Z",
      "updated_at": "2026-06-24T03:37:04.000000Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

---

### 3. Logout

**POST** `/auth/logout`

Membatalkan token JWT yang sedang aktif.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Logout berhasil"
}
```

---

### 4. Lihat Profil

**GET** `/auth/profile`

Mengambil informasi profil user yang terautentikasi.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "citizen",
    "phone": "081234567890",
    "address": "Jl. Merdeka No. 1",
    "created_at": "2026-06-24T03:37:04.000000Z",
    "updated_at": "2026-06-24T03:37:04.000000Z"
  }
}
```

---

### 5. Refresh Token

**POST** `/auth/refresh`

Mendapatkan token JWT baru (refresh token yang sedang aktif).

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Token berhasil diperbarui",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}
```

---

## Endpoints Layanan Public

### 6. Daftar Semua Jenis Layanan

**GET** `/services`

Mengambil daftar semua jenis layanan publik yang tersedia.

**Headers:**
```
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Daftar layanan berhasil diambil",
  "data": [
    {
      "id": 1,
      "name": "Surat Keterangan Domisili",
      "description": "Surat keterangan domisili untuk keperluan administrasi",
      "estimated_days": 3,
      "created_at": "2026-06-24T04:33:45.000000Z",
      "updated_at": "2026-06-24T04:33:45.000000Z"
    }
  ]
}
```

---

### 7. Ajukan Permintaan Layanan

**POST** `/services/request`

Membuat permintaan layanan baru (butuh autentikasi).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "service_type_id": "integer (wajib, ID dari jenis layanan)",
  "description": "string (wajib, max:2000, deskripsi permintaan)",
  "attachment_url": "string (opsional, URL lampiran, max:500)"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Permintaan layanan berhasil dibuat",
  "data": {
    "id": 1,
    "user_id": 4,
    "service_type_id": 1,
    "status": "pending",
    "description": "Saya membutuhkan surat keterangan domisili untuk keperluan kerja",
    "attachment_url": "https://example.com/attachment.pdf",
    "created_at": "2026-06-24T04:34:19.000000Z",
    "updated_at": "2026-06-24T04:34:19.000000Z",
    "service_type": {
      "id": 1,
      "name": "Surat Keterangan Domisili",
      "description": "Surat keterangan domisili untuk keperluan administrasi",
      "estimated_days": 3
    },
    "user": {
      "id": 4,
      "name": "Test User",
      "email": "test@example.com",
      "role": "citizen"
    }
  }
}
```

---

### 8. Lihat Status Permintaan Layanan

**GET** `/services/request/{id}`

Melihat detail permintaan layanan (hanya user yang membuat permintaan yang dapat melihat).

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Detail permintaan layanan",
  "data": {
    "id": 1,
    "user_id": 4,
    "service_type_id": 1,
    "status": "pending",
    "description": "Saya membutuhkan surat keterangan domisili untuk keperluan kerja",
    "attachment_url": "https://example.com/attachment.pdf",
    "created_at": "2026-06-24T04:34:19.000000Z",
    "updated_at": "2026-06-24T04:34:19.000000Z",
    "service_type": {
      "id": 1,
      "name": "Surat Keterangan Domisili",
      "description": "Surat keterangan domisili untuk keperluan administrasi",
      "estimated_days": 3
    },
    "user": {
      "id": 4,
      "name": "Test User",
      "email": "test@example.com",
      "role": "citizen"
    }
  }
}
```

**Response (404 Not Found):**
```json
{
  "success": false,
  "message": "Permintaan layanan tidak ditemukan"
}
```

---

### 9. Update Status Permintaan Layanan (Admin Only)

**PUT** `/services/request/{id}/status`

Mengubah status permintaan layanan (hanya admin yang dapat mengakses).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "status": "string (wajib, pending|processing|done|rejected)"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Status permintaan layanan berhasil diperbarui",
  "data": {
    "id": 1,
    "user_id": 4,
    "service_type_id": 1,
    "status": "processing",
    "description": "Saya membutuhkan surat keterangan domisili untuk keperluan kerja",
    "attachment_url": "https://example.com/attachment.pdf",
    "created_at": "2026-06-24T04:34:19.000000Z",
    "updated_at": "2026-06-24T04:35:03.000000Z",
    "service_type": {
      "id": 1,
      "name": "Surat Keterangan Domisili",
      "description": "Surat keterangan domisili untuk keperluan administrasi",
      "estimated_days": 3
    },
    "user": {
      "id": 4,
      "name": "Test User",
      "email": "test@example.com",
      "role": "citizen"
    }
  }
}
```

**Response (403 Forbidden):**
```json
{
  "success": false,
  "message": "Akses ditolak. Hanya admin yang dapat mengubah status."
}
```

---

### 10. Daftar Semua Permintaan Layanan (Admin Only)

**GET** `/services/requests`

Mengambil daftar semua permintaan layanan (hanya admin yang dapat mengakses).

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters (opsional):**
- `status` - Filter by status (pending/processing/done/rejected)
- `user_id` - Filter by user ID

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Daftar semua permintaan layanan",
  "data": [
    {
      "id": 1,
      "user_id": 4,
      "service_type_id": 1,
      "status": "processing",
      "description": "Saya membutuhkan surat keterangan domisili untuk keperluan kerja",
      "attachment_url": "https://example.com/attachment.pdf",
      "created_at": "2026-06-24T04:34:19.000000Z",
      "updated_at": "2026-06-24T04:35:03.000000Z",
      "service_type": {
        "id": 1,
        "name": "Surat Keterangan Domisili",
        "description": "Surat keterangan domisili untuk keperluan administrasi",
        "estimated_days": 3
      },
      "user": {
        "id": 4,
        "name": "Test User",
        "email": "test@example.com",
        "role": "citizen"
      }
    }
  ]
}
```

**Response (403 Forbidden):**
```json
{
  "success": false,
  "message": "Akses ditolak. Hanya admin yang dapat melihat semua permintaan."
}
```

---

## Model Data

### User

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| id | integer | Identifikasi unik |
| name | string | Nama lengkap user (max: 255) |
| email | string | Alamat email user (unik, max: 255) |
| password | string | Password ter-hash (min: 8 karakter) |
| password_hash | string | Hash password tambahan (opsional) |
| role | enum | Role user: `admin` atau `citizen` (default: citizen) |
| phone | string | Nomor telepon (opsional, max: 20) |
| address | text | Alamat user (opsional, max: 1000) |
| created_at | timestamp | Timestamp pembuatan |
| updated_at | timestamp | Timestamp update terakhir |

### ServiceType

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| id | integer | Identifikasi unik |
| name | string | Nama jenis layanan (max: 255) |
| description | text | Deskripsi layanan |
| estimated_days | integer | Estimasi waktu pengerjaan (hari) |
| created_at | timestamp | Timestamp pembuatan |
| updated_at | timestamp | Timestamp update terakhir |

### ServiceRequest

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| id | integer | Identifikasi unik |
| user_id | integer | ID user yang membuat permintaan |
| service_type_id | integer | ID jenis layanan |
| status | enum | Status: `pending`, `processing`, `done`, `rejected` (default: pending) |
| description | text | Deskripsi permintaan (max: 2000) |
| attachment_url | string | URL lampiran (opsional, max: 500) |
| created_at | timestamp | Timestamp pembuatan |
| updated_at | timestamp | Timestamp update terakhir |

---

## Format Error Response

Semua endpoint mengembalikan format error yang konsisten:

**Validasi Gagal (422):**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "nama_field": ["Pesan error"]
  }
}
```

**Tidak Ditemukan (404):**
```json
{
  "success": false,
  "message": "Permintaan layanan tidak ditemukan"
}
```

**Tidak Terautentikasi (401):**
```json
{
  "success": false,
  "message": "Tidak terautentikasi"
}
```

**Akses Ditolak (403):**
```json
{
  "success": false,
  "message": "Akses ditolak. Hanya admin yang dapat mengubah status."
}
```

---

## Testing dengan Postman

### Contoh: Registrasi user baru

1. **Method:** POST
2. **URL:** `http://localhost:8000/api/v1/auth/register`
3. **Headers:**
   - Content-Type: application/json
   - Accept: application/json
4. **Body (raw JSON):**
```json
{
  "name": "Admin User",
  "email": "admin@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "admin",
  "phone": "081234567890",
  "address": "Jl. Admin No. 1"
}
```

### Contoh: Login

1. **Method:** POST
2. **URL:** `http://localhost:8000/api/v1/auth/login`
3. **Headers:**
   - Content-Type: application/json
   - Accept: application/json
4. **Body (raw JSON):**
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

### Contoh: Lihat Daftar Layanan

1. **Method:** GET
2. **URL:** `http://localhost:8000/api/v1/services`
3. **Headers:**
   - Accept: application/json

### Contoh: Buat Permintaan Layanan (setelah login)

1. **Method:** POST
2. **URL:** `http://localhost:8000/api/v1/services/request`
3. **Headers:**
   - Content-Type: application/json
   - Accept: application/json
   - Authorization: Bearer {token_dari_login}
4. **Body (raw JSON):**
```json
{
  "service_type_id": 1,
  "description": "Saya membutuhkan surat keterangan domisili untuk keperluan kerja",
  "attachment_url": "https://example.com/attachment.pdf"
}
```

### Contoh: Update Status Permintaan (Admin)

1. **Method:** PUT
2. **URL:** `http://localhost:8000/api/v1/services/request/1/status`
3. **Headers:**
   - Content-Type: application/json
   - Accept: application/json
   - Authorization: Bearer {admin_token}
4. **Body (raw JSON):**
```json
{
  "status": "processing"
}
```

---

## Catatan

- Token JWT berlaku selama 1 jam (3600 detik)
- Gunakan endpoint `/auth/refresh` untuk mendapatkan token baru sebelum expired
- Password di-hash menggunakan bcrypt
- Semua response dalam format JSON
- API mengikuti konvensi RESTful
- Hanya admin yang dapat mengubah status permintaan layanan
- User hanya dapat melihat permintaan layanan miliknya sendiri
- Admin dapat melihat semua permintaan layanan dengan filter