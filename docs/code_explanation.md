# Penjelasan Code — SEMAR (Sistem Manajemen Pengajuan & Validasi)

> Dokumen ini menjelaskan arsitektur, alur kerja, dan cara penggunaan proyek SEMAR yang dibangun dengan Laravel 12.

---

## 1. Struktur Folder & Alasan

```
SemarApp/
├── app/
│   ├── Enums/                    # PHP 8.1 Backed Enums
│   │   ├── SubmissionStatus.php  # Status workflow: DRAFT, SUBMITTED, dll.
│   │   ├── DocType.php           # Tipe dokumen wajib: PROPOSAL, ICF, SURAT_PENGANTAR
│   │   ├── Recommendation.php    # Rekomendasi reviewer: APPROVE, REVISION, REJECT
│   │   └── DecisionType.php      # Keputusan akhir: APPROVED, RESUBMISSION, DISAPPROVED
│   ├── Http/Controllers/
│   │   ├── AuthController.php        # Login/Logout (custom, bukan Breeze)
│   │   ├── DashboardController.php   # Router dashboard per role
│   │   ├── SubmissionController.php  # CRUD pengajuan + upload dokumen
│   │   ├── AssignmentController.php  # Ketua assign reviewer
│   │   ├── ReviewController.php      # Reviewer submit review
│   │   ├── DecisionController.php    # Sekretariat keputusan akhir
│   │   ├── DocCheckController.php    # Sekretariat validasi dokumen
│   │   └── Admin/
│   │       └── UserController.php    # Admin kelola user/role
│   ├── Models/
│   │   ├── User.php                  # + HasRoles (spatie), submissions(), reviews()
│   │   ├── Submission.php            # Model utama: status, code generation, doc check
│   │   ├── SubmissionDocument.php    # Dokumen per pengajuan (unique per doc_type)
│   │   ├── Assignment.php            # Relasi submission <-> reviewer
│   │   ├── Review.php                # Review + rekomendasi dari reviewer
│   │   ├── Decision.php              # Keputusan akhir dari sekretariat
│   │   └── StatusHistory.php         # Audit trail perubahan status
│   ├── Policies/
│   │   └── SubmissionPolicy.php      # Authorization: create, view, update
│   └── Services/
│       └── WorkflowService.php       # State machine transisi status
├── database/
│   ├── migrations/                   # 7 migrasi domain + 1 add_fields_to_users
│   └── seeders/
│       ├── RolesAndPermissionsSeeder.php  # 5 role + 15 permission
│       └── DummyUsersSeeder.php          # 7 user dummy (1+ per role)
├── resources/
│   ├── css/app.css                   # Design tokens Tailwind 4 (SEMAR palette)
│   └── views/
│       ├── auth/login.blade.php
│       ├── components/
│       │   ├── layouts/app.blade.php         # Layout utama (sidebar + topbar + slot)
│       │   ├── layouts/partials/sidebar.blade.php
│       │   ├── layouts/partials/topbar.blade.php
│       │   ├── status-badge.blade.php        # Badge status seragam
│       │   └── metric-card.blade.php         # Kartu metrik dashboard
│       ├── dashboard/
│       │   ├── student.blade.php
│       │   ├── reviewer.blade.php
│       │   ├── ketua.blade.php
│       │   ├── sekretariat.blade.php
│       │   └── admin.blade.php
│       ├── submissions/ (index, create, show, edit)
│       ├── assignments/index.blade.php
│       ├── reviews/ (index, show)
│       ├── doccheck/ (index, show)
│       ├── decisions/ (index, show)
│       └── admin/users/index.blade.php
└── routes/web.php                    # Semua route + middleware role
```

**Alasan arsitektur:**
- **Enums** digunakan untuk menghindari magic string dan menyediakan label + badge class terpusat.
- **WorkflowService** memisahkan logika transisi status dari controller agar mudah diuji dan diubah.
- **Blade anonymous components** (`<x-layouts.app>`, `<x-status-badge>`, `<x-metric-card>`) digunakan karena lebih ringan dan tidak memerlukan class PHP.

---

## 2. Skema Tabel Utama & Relasi

```
users (1) ──────< submissions (N)
                     │
                     ├──< submission_documents (N)    [unique: submission_id + doc_type]
                     ├──< assignments (N)             [unique: submission_id + reviewer_id]
                     ├──< reviews (N)                 [unique: submission_id + reviewer_id]
                     ├──< decisions (N)
                     └──< status_histories (N)

users (reviewer) ──< assignments
                 ──< reviews
users (sekretariat) ──< decisions
```

| Tabel | Kolom Kunci | Keterangan |
|-------|-------------|------------|
| `submissions` | `code` (unique), `status`, `student_id`, `submitted_at` | Pengajuan utama |
| `submission_documents` | `submission_id`, `doc_type` (unique pair), `file_path` | 3 dokumen wajib |
| `assignments` | `submission_id`, `reviewer_id` (unique pair), `due_at` | Penugasan reviewer |
| `reviews` | `submission_id`, `reviewer_id` (unique pair), `recommendation` | Hasil review |
| `decisions` | `submission_id`, `decided_by`, `decision` | Keputusan akhir |
| `status_histories` | `from_status`, `to_status`, `changed_by` | Audit trail |

---

## 3. Alur RBAC (spatie/laravel-permission)

### Roles
| Role | Keterangan |
|------|------------|
| `student` | Membuat pengajuan, upload dokumen, submit |
| `reviewer` | Review pengajuan yang ditugaskan |
| `ketua` | Assign reviewer ke pengajuan |
| `sekretariat` | Cek dokumen + keputusan akhir |
| `admin` | Kelola user dan role |

### Permissions
Setiap role memiliki permission spesifik (lihat `RolesAndPermissionsSeeder`):
- **student**: `submission.create`, `submission.view_own`, `submission.update_own_draft`, `submission.submit_own`, `document.upload_own`, `document.delete_own`, `document.view_own`
- **reviewer**: `submission.view_assigned`, `review.create`, `review.update_own`, `review.submit_own`
- **ketua**: `submission.view_all`, `assignment.manage`
- **sekretariat**: `submission.view_all`, `doccheck.manage`, `decision.make`
- **admin**: `user.manage`, `role.manage`, `permission.manage`, `audit.view`

### Proteksi Route (middleware)
```php
Route::middleware('role:ketua')          // Hanya ketua
Route::middleware('role:reviewer')       // Hanya reviewer
Route::middleware('role:sekretariat')    // Hanya sekretariat
Route::middleware('role:admin')          // Hanya admin
```

### Policy (SubmissionPolicy)
- `create()` → cek permission `submission.create`
- `view()` → cek role + kepemilikan / assignment
- `update()` → cek role student + status DRAFT/RESUBMISSION

### Proteksi IDOR
- Student: `$submission->student_id !== $user->id` → abort(403)
- Reviewer: `assignments()->where('reviewer_id', $user->id)->exists()` → abort(403)
- Sekretariat/Ketua: dilindungi middleware `role:sekretariat` / `role:ketua`

---

## 4. Alur Workflow Submission

```
DRAFT ──submit──> SUBMITTED ──sekretariat cek──> DOC_CHECK ──ketua assign──> ASSIGNED
                      │                              │
                      │ (return)                      │ (return)
                      └──────> DRAFT <────────────────┘

ASSIGNED ──reviewer submit review──> UNDER_REVIEW ──semua review masuk──> PENDING_DECISION
                                                                              │
                                                        ┌─────────────────────┼─────────────────────┐
                                                        │                     │                     │
                                                    APPROVED           RESUBMISSION           DISAPPROVED
                                                                          │
                                                                          └──student resubmit──> SUBMITTED
```

**Aturan validasi:**
1. Student TIDAK bisa submit jika 3 dokumen (Proposal, ICF, Surat Pengantar) belum lengkap.
2. Reviewer HANYA bisa merekomendasikan (Approve/Revision/Reject), TIDAK membuat keputusan final.
3. Ketua HANYA assign reviewer + set deadline, TIDAK membuat keputusan.
4. Sekretariat yang membuat keputusan final (Approved/Resubmission/Disapproved).

---

## 5. Komponen UI Blade

### `<x-layouts.app>`
Layout utama dengan sidebar, topbar, flash messages.
```blade
<x-layouts.app :title="'Dashboard'">
    {{-- konten halaman --}}
</x-layouts.app>
```

### `<x-status-badge>`
Badge status seragam (non-rainbow, menggunakan palette SEMAR).
```blade
<x-status-badge :status="$submission->status" />
<x-status-badge status="DRAFT" />
```

### `<x-metric-card>`
Kartu metrik dashboard dengan border-left berwarna.
```blade
<x-metric-card label="Total Pengajuan" :value="5" color="primary" />
```
Warna tersedia: `primary`, `info`, `warning`, `danger`, `success`, `muted`.

---

## 6. Design System (Color Palette)

| Token | Hex | Penggunaan |
|-------|-----|------------|
| `--color-primary` | `#463EE3` | Tombol, link, accent |
| `--color-bg` | `#F5F5F5` | Background halaman |
| `--color-surface` | `#FFFFFF` | Card/surface utama |
| `--color-soft-surface` | `#E6E6FA` | Highlight section, hover, badge bg |
| `--color-info` | `#87CEEB` | Informasi, status info |
| `--color-info-soft` | `#B0E0E6` | Background info soft |
| `--color-text` | `#111827` | Teks utama |
| `--color-text-secondary` | `#6B7280` | Teks sekunder |

**Aturan:** Tidak ada gradient, tidak ada neon/dark-futuristic, tidak ada warna di luar palette.

---

## 7. Cara Menjalankan Proyek

```bash
# 1. Install dependensi
composer install
npm install

# 2. Siapkan environment
cp .env.example .env
php artisan key:generate

# 3. Konfigurasi .env
# DB_DATABASE=semar_db
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Jalankan migrasi + seeder
php artisan migrate:fresh --seed

# 5. Buat symlink storage
php artisan storage:link

# 6. Build assets
npm run build

# 7. Jalankan server
php artisan serve
```

Buka `http://127.0.0.1:8000/login` dan login dengan akun dummy (password: `password`).

---

## 8. Checklist Manual Testing per Role

### Student (`student@semar.test`)
- [ ] Login berhasil, redirect ke dashboard student
- [ ] Buat pengajuan baru (judul, jenis, abstrak)
- [ ] Upload 3 dokumen PDF (Proposal, ICF, Surat Pengantar)
- [ ] Tombol Submit disabled saat dokumen belum lengkap
- [ ] Submit pengajuan setelah dokumen lengkap
- [ ] Lihat detail pengajuan (tab Details, Dokumen, History)
- [ ] Tidak bisa akses pengajuan milik student lain

### Sekretariat (`sekretariat@semar.test`)
- [ ] Login berhasil, redirect ke dashboard sekretariat
- [ ] Lihat antrian "Cek Dokumen" untuk pengajuan SUBMITTED
- [ ] Terima dokumen (approve) → status jadi DOC_CHECK
- [ ] Kembalikan ke student (return) dengan catatan → status jadi DRAFT
- [ ] Lihat antrian "Keputusan" untuk pengajuan PENDING_DECISION
- [ ] Buat keputusan akhir (Approved/Resubmission/Disapproved)

### Ketua (`ketua@semar.test`)
- [ ] Login berhasil, redirect ke dashboard ketua
- [ ] Lihat pengajuan yang perlu assign reviewer
- [ ] Assign reviewer ke pengajuan (pilih reviewer + deadline)
- [ ] Hapus penugasan reviewer
- [ ] Tidak bisa membuat keputusan akhir

### Reviewer (`reviewer@semar.test`)
- [ ] Login berhasil, redirect ke dashboard reviewer
- [ ] Lihat daftar review yang ditugaskan
- [ ] Buka detail pengajuan, lihat dokumen
- [ ] Isi catatan review + pilih rekomendasi
- [ ] Submit review
- [ ] Tidak bisa mengubah review setelah submit
- [ ] Tidak bisa akses pengajuan yang tidak ditugaskan

### Admin (`admin@semar.test`)
- [ ] Login berhasil, redirect ke dashboard admin
- [ ] Lihat daftar user dengan role
- [ ] Tambah user baru dengan role
- [ ] Hapus user
