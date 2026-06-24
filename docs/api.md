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

**Catatan:** Sistem akan otomatis mengirim notifikasi ke user ketika status permintaan layanan berubah.

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

## Endpoints Laporan Warga

### 11. Kirim Laporan Masalah

**POST** `/reports`

Mengirim laporan masalah di lingkungan (butuh autentikasi).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "category": "string (wajib, infrastructure|environment|social|other)",
  "title": "string (wajib, max:255)",
  "description": "string (wajib, max:2000)",
  "location": "string (wajib, max:500)",
  "image_url": "string (opsional, URL gambar, max:500)"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Laporan berhasil dikirim",
  "data": {
    "id": 1,
    "user_id": 4,
    "category": "infrastructure",
    "title": "Jalan Rusak di RT 05",
    "description": "Jalan di depan rumah warga rusak parah dan perlu perbaikan segera",
    "location": "Jl. Merdeka No. 15, RT 05/RW 03",
    "image_url": "https://example.com/road-damage.jpg",
    "status": "open",
    "created_at": "2026-06-24T06:08:41.000000Z",
    "updated_at": "2026-06-24T06:08:41.000000Z",
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

### 12. Daftar Laporan

**GET** `/reports`

Mengambil daftar laporan (admin lihat semua, user hanya miliknya).

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters (opsional):**
- `category` - Filter by category (infrastructure/environment/social/other)
- `status` - Filter by status (open/in_progress/resolved)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Daftar laporan berhasil diambil",
  "data": [
    {
      "id": 1,
      "user_id": 4,
      "category": "infrastructure",
      "title": "Jalan Rusak di RT 05",
      "description": "Jalan di depan rumah warga rusak parah dan perlu perbaikan segera",
      "location": "Jl. Merdeka No. 15, RT 05/RW 03",
      "image_url": "https://example.com/road-damage.jpg",
      "status": "open",
      "created_at": "2026-06-24T06:08:41.000000Z",
      "updated_at": "2026-06-24T06:08:41.000000Z",
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

---

### 13. Detail Laporan

**GET** `/reports/{id}`

Melihat detail laporan (user hanya bisa melihat laporan miliknya, admin bisa melihat semua).

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Detail laporan",
  "data": {
    "id": 1,
    "user_id": 4,
    "category": "infrastructure",
    "title": "Jalan Rusak di RT 05",
    "description": "Jalan di depan rumah warga rusak parah dan perlu perbaikan segera",
    "location": "Jl. Merdeka No. 15, RT 05/RW 03",
    "image_url": "https://example.com/road-damage.jpg",
    "status": "open",
    "created_at": "2026-06-24T06:08:41.000000Z",
    "updated_at": "2026-06-24T06:08:41.000000Z",
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
  "message": "Laporan tidak ditemukan"
}
```

---

### 14. Update Laporan

**PUT** `/reports/{id}`

Mengupdate laporan (user hanya bisa update laporan miliknya, admin bisa update semua).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body (semua field opsional):**
```json
{
  "category": "string (opsional, infrastructure|environment|social|other)",
  "title": "string (opsional, max:255)",
  "description": "string (opsional, max:2000)",
  "location": "string (opsional, max:500)",
  "image_url": "string (opsional, URL gambar, max:500)",
  "status": "string (opsional, open|in_progress|resolved)"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Laporan berhasil diperbarui",
  "data": {
    "id": 1,
    "user_id": 4,
    "category": "infrastructure",
    "title": "Jalan Rusak di RT 05",
    "description": "Jalan di depan rumah warga rusak parah dan perlu perbaikan segera - sedang dalam proses perbaikan",
    "location": "Jl. Merdeka No. 15, RT 05/RW 03",
    "image_url": "https://example.com/road-damage.jpg",
    "status": "in_progress",
    "created_at": "2026-06-24T06:08:41.000000Z",
    "updated_at": "2026-06-24T06:09:21.000000Z",
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
  "message": "Laporan tidak ditemukan"
}
```

---

## Endpoints Notifikasi

### 15. Daftar Notifikasi

**GET** `/notifications`

Mengambil daftar notifikasi pengguna yang terautentikasi.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters (opsional):**
- `is_read` - Filter by read status (true/false)
- `type` - Filter by type (service_request/report/system)

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Daftar notifikasi berhasil diambil",
  "unread_count": 1,
  "data": [
    {
      "id": 1,
      "user_id": 4,
      "message": "Permintaan layanan Surat Keterangan Domisili telah diperbarui menjadi status: Selesai",
      "type": "service_request",
      "is_read": false,
      "reference_id": 1,
      "reference_type": "service_request",
      "created_at": "2026-06-24T06:22:07.000000Z",
      "updated_at": "2026-06-24T06:22:07.000000Z"
    }
  ]
}
```

**Response Fields:**
- `unread_count` - Jumlah notifikasi yang belum dibaca
- `type` - Jenis notifikasi (service_request, report, system)
- `is_read` - Status pembacaan (true/false)
- `reference_id` - ID dari data yang direferensikan
- `reference_type` - Tipe data yang direferensikan (service_request, report)

---

### 16. Tandai Notifikasi sebagai Dibaca

**PUT** `/notifications/{id}/read`

Menandai notifikasi tertentu sebagai sudah dibaca.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Notifikasi berhasil ditandai sebagai dibaca",
  "data": {
    "id": 1,
    "user_id": 4,
    "message": "Permintaan layanan Surat Keterangan Domisili telah diperbarui menjadi status: Selesai",
    "type": "service_request",
    "is_read": true,
    "reference_id": 1,
    "reference_type": "service_request",
    "created_at": "2026-06-24T06:22:07.000000Z",
    "updated_at": "2026-06-24T06:22:07.000000Z"
  }
}
```

**Response (404 Not Found):**
```json
{
  "success": false,
  "message": "Notifikasi tidak ditemukan"
}
```

---

### 17. Tandai Semua Notifikasi sebagai Dibaca

**PUT** `/notifications/read-all`

Menandai semua notifikasi user sebagai sudah dibaca.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Semua notifikasi berhasil ditandai sebagai dibaca"
}
```

---

## Endpoints Dashboard (Admin Only)

### 18. Statistik Ringkasan Dashboard

**GET** `/dashboard/stats`

Mengambil statistik ringkasan untuk dashboard admin.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Statistik dashboard berhasil diambil",
  "data": {
    "users": {
      "total": 3,
      "citizens": 2,
      "admins": 1,
      "new_this_month": 3
    },
    "reports": {
      "total": 1,
      "open": 0,
      "in_progress": 1,
      "resolved": 0,
      "this_month": 1
    },
    "service_requests": {
      "total": 1,
      "pending": 0,
      "processing": 0,
      "done": 1,
      "rejected": 0,
      "this_month": 1
    },
    "notifications": {
      "total": 1,
      "unread": 1
    }
  }
}
```

**Response Fields:**
- `users.total` - Total semua user
- `users.citizens` - Total user citizen
- `users.admins` - Total user admin
- `users.new_this_month` - User baru bulan ini
- `reports.total` - Total semua laporan
- `reports.open` - Laporan terbuka
- `reports.in_progress` - Laporan sedang diproses
- `reports.resolved` - Laporan selesai
- `reports.this_month` - Laporan bulan ini
- `service_requests.total` - Total semua permintaan layanan
- `service_requests.pending` - Permintaan menunggu
- `service_requests.processing` - Permintaan sedang diproses
- `service_requests.done` - Permintaan selesai
- `service_requests.rejected` - Permintaan ditolak
- `service_requests.this_month` - Permintaan bulan ini
- `notifications.total` - Total semua notifikasi
- `notifications.unread` - Notifikasi belum dibaca

**Response (403 Forbidden):**
```json
{
  "success": false,
  "message": "Akses ditolak. Hanya admin yang dapat mengakses dashboard."
}
```

---

### 19. Rekapitulasi Laporan per Kategori

**GET** `/dashboard/reports/summary`

Mengambil rekapitulasi laporan berdasarkan kategori.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Rekapitulasi laporan per kategori berhasil diambil",
  "data": {
    "summary": [
      {
        "category": "infrastructure",
        "category_label": "Infrastruktur",
        "total": 1
      }
    ],
    "detailed": [
      {
        "category": "infrastructure",
        "category_label": "Infrastruktur",
        "total": 1,
        "status_breakdown": [
          {
            "status": "open",
            "status_label": "Terbuka",
            "count": 0
          },
          {
            "status": "in_progress",
            "status_label": "Sedang Diproses",
            "count": 1
          },
          {
            "status": "resolved",
            "status_label": "Selesai",
            "count": 0
          }
        ]
      },
      {
        "category": "environment",
        "category_label": "Lingkungan",
        "total": 0,
        "status_breakdown": [
          {
            "status": "open",
            "status_label": "Terbuka",
            "count": 0
          },
          {
            "status": "in_progress",
            "status_label": "Sedang Diproses",
            "count": 0
          },
          {
            "status": "resolved",
            "status_label": "Selesai",
            "count": 0
          }
        ]
      },
      {
        "category": "social",
        "category_label": "Sosial",
        "total": 0,
        "status_breakdown": [
          {
            "status": "open",
            "status_label": "Terbuka",
            "count": 0
          },
          {
            "status": "in_progress",
            "status_label": "Sedang Diproses",
            "count": 0
          },
          {
            "status": "resolved",
            "status_label": "Selesai",
            "count": 0
          }
        ]
      },
      {
        "category": "other",
        "category_label": "Lainnya",
        "total": 0,
        "status_breakdown": [
          {
            "status": "open",
            "status_label": "Terbuka",
            "count": 0
          },
          {
            "status": "in_progress",
            "status_label": "Sedang Diproses",
            "count": 0
          },
          {
            "status": "resolved",
            "status_label": "Selesai",
            "count": 0
          }
        ]
      }
    ],
    "total_reports": 1
  }
}
```

**Response Fields:**
- `summary` - Ringkasan total per kategori
- `detailed` - Detail breakdown per kategori dan status
- `detailed[].status_breakdown` - Breakdown status untuk setiap kategori
- `total_reports` - Total semua laporan

**Response (403 Forbidden):**
```json
{
  "success": false,
  "message": "Akses ditolak. Hanya admin yang dapat mengakses dashboard."
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

### Report

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| id | integer | Identifikasi unik |
| user_id | integer | ID user yang membuat laporan |
| category | enum | Kategori: `infrastructure`, `environment`, `social`, `other` |
| title | string | Judul laporan (max: 255) |
| description | text | Deskripsi laporan (max: 2000) |
| location | string | Lokasi masalah (max: 500) |
| image_url | string | URL gambar (opsional, max: 500) |
| status | enum | Status: `open`, `in_progress`, `resolved` (default: open) |
| created_at | timestamp | Timestamp pembuatan |
| updated_at | timestamp | Timestamp update terakhir |

### Notification

| Field | Tipe | Deskripsi |
|-------|------|-----------|
| id | integer | Identifikasi unik |
| user_id | integer | ID user yang menerima notifikasi |
| message | text | Isi pesan notifikasi |
| type | string | Tipe notifikasi (service_request, report, system) |
| is_read | boolean | Status pembacaan (false = belum dibaca, true = sudah dibaca) |
| reference_id | integer | ID dari data yang direferensikan |
| reference_type | string | Tipe data yang direferensikan (service_request, report) |
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
  "message": "Laporan tidak ditemukan"
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
  "message": "Akses ditolak. Hanya admin yang dapat mengakses dashboard."
}
```

---

## Sistem Notifikasi Otomatis

Sistem akan otomatis mengirim notifikasi ketika:

1. **Status Permintaan Layanan Berubah** - Notifikasi dikirim ke user yang membuat permintaan layanan ketika admin mengubah status (pending → processing → done/rejected)

2. **Format Notifikasi:**
   - Message: "Permintaan layanan [Nama Layanan] telah diperbarui menjadi status: [Status]"
   - Type: `service_request`
   - Reference: ID dan tipe permintaan layanan

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

### Contoh: Kirim Laporan Masalah

1. **Method:** POST
2. **URL:** `http://localhost:8000/api/v1/reports`
3. **Headers:**
   - Content-Type: application/json
   - Accept: application/json
   - Authorization: Bearer {token_dari_login}
4. **Body (raw JSON):**
```json
{
  "category": "infrastructure",
  "title": "Jalan Rusak di RT 05",
  "description": "Jalan di depan rumah warga rusak parah dan perlu perbaikan segera",
  "location": "Jl. Merdeka No. 15, RT 05/RW 03",
  "image_url": "https://example.com/road-damage.jpg"
}
```

### Contoh: Lihat Daftar Laporan

1. **Method:** GET
2. **URL:** `http://localhost:8000/api/v1/reports`
3. **Headers:**
   - Accept: application/json
   - Authorization: Bearer {token_dari_login}

### Contoh: Lihat Notifikasi

1. **Method:** GET
2. **URL:** `http://localhost:8000/api/v1/notifications`
3. **Headers:**
   - Accept: application/json
   - Authorization: Bearer {token_dari_login}

### Contoh: Lihat Statistik Dashboard (Admin)

1. **Method:** GET
2. **URL:** `http://localhost:8000/api/v1/dashboard/stats`
3. **Headers:**
   - Accept: application/json
   - Authorization: Bearer {admin_token}

### Contoh: Lihat Rekapitulasi Laporan per Kategori (Admin)

1. **Method:** GET
2. **URL:** `http://localhost:8000/api/v1/dashboard/reports/summary`
3. **Headers:**
   - Accept: application/json
   - Authorization: Bearer {admin_token}

### Contoh: Tandai Notifikasi sebagai Dibaca

1. **Method:** PUT
2. **URL:** `http://localhost:8000/api/v1/notifications/1/read`
3. **Headers:**
   - Accept: application/json
   - Authorization: Bearer {token_dari_login}

### Contoh: Update Status Laporan (Admin)

1. **Method:** PUT
2. **URL:** `http://localhost:8000/api/v1/reports/1`
3. **Headers:**
   - Content-Type: application/json
   - Accept: application/json
   - Authorization: Bearer {admin_token}
4. **Body (raw JSON):**
```json
{
  "status": "in_progress",
  "description": "Jalan di depan rumah warga rusak parah dan perlu perbaikan segera - sedang dalam proses perbaikan"
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
- Untuk laporan: user hanya bisa melihat dan mengupdate laporan miliknya sendiri
- Admin dapat melihat dan mengupdate semua laporan
- Kategori laporan: infrastructure (infrastruktur), environment (lingkungan), social (sosial), other (lainnya)
- Status laporan: open (terbuka), in_progress (sedang diproses), resolved (selesai)
- Notifikasi otomatis dikirim ketika status permintaan layanan berubah
- User dapat melihat daftar notifikasi dan menandai sebagai dibaca
- Notifikasi menyimpan referensi ke data terkait (service_request atau report)
- Dashboard hanya dapat diakses oleh admin
- Dashboard menyediakan statistik lengkap: users, reports, service requests, dan notifications