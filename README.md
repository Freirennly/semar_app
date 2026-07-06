# SEMAR (Sistem Manajemen Etik Riset)

SEMAR adalah platform berbasis web untuk manajemen proses komite etik penelitian. Sistem ini dirancang untuk memudahkan pengajuan proposal, peninjauan etik, dan penerbitan *ethical clearance*.

## Fitur Utama

- **Authentication & Authorization (RBAC)**: Sistem otentikasi aman dengan Role-Based Access Control terintegrasi.
- **Landing Page**: Informasi publik, About, dan SOP.
- **Proposal Submission Management**: Pengelolaan berkas dan dokumen pengajuan riset.
- **Document Management**: Templat dokumen dinamis dan manajemen file.
- **Reviewer Assignment**: Penugasan reviewer oleh sekretariat.
- **Ethical Review Workflow**: Alur kerja tinjauan etik komprehensif.
- **Decision Management**: Pengambilan keputusan komite.
- **Ethical Clearance Generation**: Pembuatan sertifikat EC secara otomatis.
- **Dashboard Berbasis Role**: Antarmuka disesuaikan dengan role pengguna (Student, Sekretariat, Reviewer, Ketua, Admin).
- **Notification System**: Notifikasi terpusat untuk setiap perubahan status.
- **Activity Logging**: Pencatatan aktivitas sistem.

## Arsitektur Sistem & Teknologi yang Digunakan

- **Framework**: Laravel 12 (PHP 8.x)
- **Database**: MySQL
- **Styling**: Tailwind CSS
- **Interaktivitas Frontend**: Alpine.js
- **Akses Kontrol**: Spatie Laravel Permission

## Instalasi & Konfigurasi

### 1. Kloning Repository
```bash
git clone https://github.com/Freirennly/semar_app.git
cd semar_app
```

### 2. Instalasi Dependensi
```bash
composer install
npm install
npm run build
```

### 3. Konfigurasi Environment
Duplikasi `.env.example` menjadi `.env` dan atur kredensial database.
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Migrasi Database dan Seeder
Jalankan migrasi untuk membangun skema database beserta data awal (Role, Permission, Akun dummy, Template).
```bash
php artisan migrate:fresh --seed
```

### 5. Menjalankan Aplikasi
```bash
php artisan serve
```
Akses aplikasi melalui `http://localhost:8000`.

## Struktur Folder Penting
- `app/`: Berisi logika utama aplikasi (Controllers, Models, Services, Policies).
- `resources/views/`: Berisi tampilan blade (UI).
- `routes/`: Konfigurasi rute web dan API.
- `database/`: Migrasi dan seeder database.

## Workflow Git
Pengembangan berjalan mengikuti standar berikut:
`feature/*` -> `develop` -> (Pull Request) -> `main` -> Tag Release -> GitHub Release.

## Role Pengguna
- **Admin**: Akses penuh mengelola data referensi dan pengaturan sistem.
- **Student**: Peneliti yang mengajukan proposal.
- **Sekretariat**: Verifikator dokumen awal dan pengelola penugasan reviewer.
- **Reviewer**: Peninjau substansi etik riset.
- **Ketua**: Pemberi keputusan akhir (Ethical Clearance / Revision).

## License
Di bawah [MIT License](LICENSE).

## Author
Tim Pengembang SEMAR.
