# SEMAR Design System
Version: 1.0
Project: SEMAR – Sistem Manajemen Ethical Clearance Penelitian
Status: Official Design System
Authoritative Document

---

# 1. Design Philosophy

SEMAR merupakan Enterprise Academic Information System yang digunakan untuk mengelola seluruh proses pengajuan Ethical Clearance penelitian mulai dari pengajuan proposal, verifikasi administrasi, review reviewer, keputusan sekretariat, penandatanganan ketua, hingga penerbitan sertifikat.

Karakter utama sistem adalah:

- Professional
- Academic
- Clean
- Calm
- Flat
- Modern Enterprise
- High Readability
- Data Oriented

SEMAR bukan Startup Dashboard.

SEMAR bukan Landing Page.

SEMAR bukan Marketing Website.

SEMAR adalah Enterprise Workflow System.

Seluruh keputusan desain harus mendukung produktivitas pengguna dalam bekerja selama berjam-jam tanpa menyebabkan visual fatigue.

---

# 2. Core Principles

Seluruh halaman wajib mengikuti prinsip berikut.

## 2.1 Flat First

Gunakan Flat Design.

Hindari efek dekoratif yang tidak memiliki fungsi.

Tidak menggunakan:

- Glassmorphism
- Heavy Gradient
- Neon
- Glow
- Skeuomorphism
- Floating Decorations

---

## 2.2 Information First

Informasi lebih penting daripada dekorasi.

Jika terdapat pilihan antara:

lebih indah

atau

lebih mudah dibaca

maka pilih yang lebih mudah dibaca.

---

## 2.3 Enterprise Before Creativity

Gunakan layout yang stabil.

Pengguna harus dapat menemukan informasi tanpa berpikir.

Jangan mengubah posisi komponen penting antar halaman.

---

## 2.4 Consistency

Semua halaman memiliki pola yang sama.

Header

↓

Breadcrumb

↓

Page Title

↓

Action Toolbar

↓

Content

↓

Table / Form

---

## 2.5 Accessibility

Semua warna wajib memiliki kontras yang baik.

Seluruh aksi penting harus dapat dilakukan menggunakan keyboard.

Focus state wajib terlihat.

---

# 3. Brand Identity

Brand Name

SEMAR

Full Name

Sistem Manajemen Ethical Clearance Penelitian

Visual Personality

Professional

Academic

Modern

Elegant

Simple

Reliable

Minimal

---

# 4. Color System

## Primary

Indigo Nightfall

#463EE3

Digunakan untuk:

- Primary Button
- Active Sidebar
- Active Navigation
- Hyperlink
- Focus State
- Progress

---

## Primary Hover

#352BCC

Hover button.

---

## Primary Surface

Pale Lilac

#E6E6FA

Digunakan sebagai:

- Active menu
- Table Header
- Selected Row
- Soft Background

---

## Secondary

Sky Blue

#87CEEB

Digunakan hanya untuk:

- Informational Component
- Timeline
- Progress Indicator

Bukan warna utama.

---

## Accent

Powder Blue

#B0E0E6

Digunakan sebagai:

- Divider
- Secondary Highlight
- Border Accent

---

## Canvas

Pearl White

#F5F5F5

Background utama aplikasi.

---

## Surface

#FFFFFF

Seluruh Card.

Form.

Table.

Modal.

---

# 5. Text Colors

Primary

#111827

Secondary

#4B5563

Caption

#9CA3AF

Danger

#991B1B

Success

#166534

Warning

#854D0E

Info

#1E3A8A

Pure Black (#000000) dilarang.

---

# 6. Typography

## UI Font

Plus Jakarta Sans

Dipakai untuk:

Dashboard

Sidebar

Table

Form

Button

Notification

Navigation

---

## Reading Font

Lora

Dipakai hanya untuk:

Proposal

Abstract

Reading Mode

Review Text

Long Notes

---

# 7. Font Scale

Display

48

H1

36

H2

24

H3

18

Body

15

Academic Reading

16

Caption

12

---

# 8. Layout

Sidebar

260 px

Fixed

---

Header

64 px

Fixed

---

Content

Max Width

1440 px

Centered

---

Content Padding

Desktop

32 px

Tablet

24 px

Mobile

16 px

---

# 9. Grid

Gunakan 8 Point Grid.

Spacing hanya boleh kelipatan:

4

8

16

24

32

40

48

64

Jangan menggunakan spacing acak.

---

# 10. Border Radius

Small

4

Medium

8

Large

12

Pill

9999

---

# 11. Border

Gunakan:

1px solid #E6E6FA

Tidak menggunakan border hitam.

---

# 12. Shadow

Shadow hanya digunakan untuk:

Modal

Dropdown

Popover

Card penting

Shadow ringan.

Tidak menggunakan shadow besar.

---

# 13. Icon

Gunakan satu icon library.

Direkomendasikan:

Lucide

atau

Heroicons

atau

Tabler Icons

Jangan mencampur beberapa library.

Ukuran:

16

20

24

Warna mengikuti warna teks.

---

# 14. Motion

Durasi:

150–200 ms

Gunakan:

ease

Jangan menggunakan bounce.

Tidak ada animasi yang mengganggu pekerjaan.

---

# 15. Images

Dashboard tidak menggunakan ilustrasi besar.

Tidak ada mascot.

Tidak ada emoji.

Tidak ada gambar dekoratif.

---

# 16. Empty State

Gunakan:

Judul

Deskripsi

CTA

Tidak menggunakan ilustrasi besar.

---

# 17. Loading

Gunakan Skeleton.

Bukan Spinner penuh.

Loading harus mempertahankan layout.

---

# 18. Error State

Error menggunakan:

Card

Border merah tipis

Background merah sangat muda

Tidak menggunakan popup yang mengganggu.

---

# 19. Responsive Rules

Desktop adalah prioritas.

Tablet tetap lengkap.

Mobile menggunakan:

Stack Layout

Collapse Sidebar

Scrollable Table

---

# 20. Design Goals

Pengguna mampu menggunakan SEMAR selama lebih dari 6 jam tanpa mengalami kelelahan visual.

Setiap halaman harus terasa:

Ringan.

Profesional.

Tenang.

Rapi.

Terstruktur.

Mudah dipindai.
# 21. Component System

Semua komponen harus memiliki perilaku visual yang konsisten.

Komponen tidak boleh memiliki gaya sendiri-sendiri.

Satu komponen = satu standar.

---

# 22. Cards

Card digunakan sebagai wadah informasi.

Jangan menggunakan card hanya untuk dekorasi.

Struktur card:

Header

↓

Body

↓

Footer (opsional)

---

Padding

24 px

Border Radius

12 px

Background

White

Border

1 px Pale Lilac

Shadow

Sangat ringan.

Hover

Tidak berubah warna drastis.

---

# 23. Buttons

Button merupakan komponen aksi.

Tidak digunakan sebagai dekorasi.

## Primary

Background

#463EE3

Text

White

Hover

#352BCC

Disabled

Opacity 50%

Cursor not allowed

---

## Secondary

Background

White

Border

Primary

Text

Primary

Hover

Pale Lilac

---

## Danger

Background

Danger

Text White

Hover

Lebih gelap.

---

## Ghost

Tidak memiliki background.

Hover hanya memberi Pale Lilac.

---

Height

40 px

Padding

10 x 16

Radius

8

---

# 24. Icon Button

Ukuran

40 x 40

Radius

8

Hover

Pale Lilac

Tidak berbentuk lingkaran.

---

# 25. Form Layout

Gunakan satu kolom.

Jika desktop lebar,

maksimal dua kolom.

Lebih dari dua kolom dilarang.

---

Label

Selalu di atas input.

Tidak menggunakan placeholder sebagai label.

---

Help Text

Di bawah input.

Font Caption.

---

Validation Error

Di bawah input.

Merah.

Tidak menggunakan popup.

---

# 26. Text Input

Height

40 px

Radius

8

Border

Light Gray

Focus

Primary Border

Primary Ring

---

Placeholder

Abu muda.

---

Readonly

Background sedikit abu.

---

Disabled

Tidak bisa diklik.

---

# 27. Textarea

Minimal

120 px

Resize Vertical

Gunakan Lora hanya jika isinya merupakan dokumen akademik.

Selain itu tetap Plus Jakarta Sans.

---

# 28. Select

Menggunakan style yang sama dengan Text Input.

Arrow sederhana.

Tidak menggunakan animasi.

---

# 29. Checkbox

Square.

Radius

4 px

Ukuran

18 px

---

# 30. Radio Button

Diameter

18 px

Spacing

8 px

---

# 31. Toggle

Digunakan hanya untuk:

Status

Publish

Enable

Disable

Bukan untuk pilihan umum.

---

# 32. Date Picker

Format

DD MMM YYYY

Contoh

30 Jun 2026

---

# 33. Search Bar

Selalu berada di kiri toolbar.

Icon Search.

Placeholder

Cari...

---

# 34. Toolbar

Urutan

Search

↓

Filter

↓

Sort

↓

Export

↓

Primary Action

Primary Action selalu berada paling kanan.

---

# 35. Breadcrumb

Selalu tampil.

Contoh

Dashboard

/

Pengajuan

/

Proposal

/

Review

---

# 36. Status Badge

Pill

Radius

9999

Tinggi

28 px

Selalu memiliki bullet kecil.

Contoh

● Disetujui

● Ditolak

● Review

---

Status

NEW

Abu

---

PROCESS

Biru

---

ON REVIEW

Kuning

---

REVISION

Merah

---

REVISED

Biru muda

---

APPROVED

Hijau

---

WAITING SIGNATURE

Ungu

---

DONE

Hijau tua

---

REJECTED

Merah

---

# 37. Alert

Empat jenis.

Success

Warning

Danger

Info

Tidak fullscreen.

Tidak popup.

---

# 38. Notification

Gunakan card sederhana.

Belum dibaca

Primary Surface

Sudah dibaca

White

---

# 39. Modal

Lebar

640 px

Radius

12

Header

Body

Footer

Footer selalu memiliki:

Cancel

Primary Action

---

# 40. Drawer

Gunakan hanya jika data terlalu panjang.

Lebih disarankan Modal.

---

# 41. Divider

1 px

Pale Lilac

Margin

24 px

---

# 42. Tabs

Gunakan maksimal 6.

Lebih dari itu gunakan Sidebar Internal.

---

# 43. Accordion

Digunakan untuk:

Advanced Settings

Riwayat

Lampiran

Dokumen Lama

Bukan informasi utama.

---

# 44. Pagination

Selalu di kanan bawah.

Gunakan Laravel Default.

Tambahkan:

Jumlah data

Halaman aktif

---

# 45. Data Table

Komponen terpenting SEMAR.

Harus mudah dipindai.

---

Header

Pale Lilac

Uppercase

12 px

Weight 600

---

Row Height

52 px

---

Hover

Surface Hover

---

Selected

Primary Surface

---

Alignment

Text kiri

Angka kanan

Tanggal tengah atau kiri

Status tengah

Aksi kanan

---

Tidak menggunakan zebra stripe.

---

# 46. Table Toolbar

Selalu terdiri dari:

Search

Filter

Sort

Export

Tambah Data

---

# 47. Action Column

Gunakan Icon Button.

Urutan

View

Edit

Download

Delete

Aksi destruktif selalu paling kanan.

---

# 48. Upload Component

Area upload sederhana.

Border putus-putus.

Primary Border.

Klik atau Drag File.

Tidak menggunakan ilustrasi besar.

---

# 49. File Preview

Gunakan daftar.

Icon File

Nama

Ukuran

Tanggal

Download

Delete

---

# 50. Timeline

Timeline digunakan hanya untuk workflow.

Vertikal.

Status aktif menggunakan Primary.

Status selesai menggunakan Success.

Status berikutnya abu.

Tidak menggunakan animasi.

---

# 51. Activity Log

Urut terbaru.

Format

Tanggal

↓

User

↓

Aktivitas

↓

Status

↓

Detail

---

# 52. Empty Table

Icon sederhana.

Judul.

Deskripsi.

Button.

Tidak menggunakan ilustrasi besar.

---

# 53. Loading Table

Gunakan Skeleton Row.

Jangan mengubah tinggi tabel saat loading.

---

# 54. Confirmation Dialog

Selalu berisi:

Judul

Deskripsi

Dampak aksi

Cancel

Confirm

---

# 55. Toast

Muncul kanan atas.

Durasi

3 detik.

Tidak menutupi konten.

---

# 56. Progress Indicator

Gunakan hanya bila benar-benar diperlukan.

Contoh

Upload

Generate Certificate

Import Data

Jangan menggunakan progress dekoratif.

---

# 57. Charts

Chart bukan komponen utama SEMAR.

Gunakan hanya jika benar-benar membantu pengambilan keputusan.

Lebih baik tabel daripada chart yang tidak informatif.
# 58. Dashboard Philosophy

Dashboard SEMAR bukan media presentasi.

Dashboard adalah ruang kerja.

Setiap elemen harus membantu pengguna mengambil keputusan atau menyelesaikan pekerjaan.

Jika sebuah widget tidak membantu pekerjaan pengguna secara langsung, maka widget tersebut tidak boleh ditampilkan.

Prioritaskan:

Information Density

Readability

Decision Support

Workflow Visibility

dibandingkan

Visual Decoration

---

# 59. Dashboard Layout

Semua dashboard mengikuti struktur berikut.

App Header

↓

Breadcrumb

↓

Page Title

↓

Executive Summary

↓

Workflow Panel

↓

Operational Table

↓

Supporting Information

Tidak ada section lain di luar struktur tersebut kecuali benar-benar diperlukan.

---

# 60. Executive Summary

Selalu berada paling atas.

Menggunakan KPI Card sederhana.

Tidak menggunakan icon besar.

Tidak menggunakan emoji.

Tidak menggunakan gradient.

Tidak menggunakan background berwarna.

Card hanya memiliki:

Label

↓

Nilai

↓

Keterangan singkat

---

Contoh

Total Proposal

245

+18 bulan ini

---

Proposal Diproses

27

Sedang berjalan

---

Perlu Tindakan

5

Menunggu keputusan

---

Selesai

213

Sertifikat diterbitkan

---

# 61. KPI Rules

Jumlah KPI maksimal

6

Minimal

4

Tidak boleh memenuhi satu layar penuh.

KPI harus dapat dipahami dalam waktu kurang dari lima detik.

---

# 62. Workflow Panel

Komponen terpenting dashboard.

Menggunakan horizontal workflow.

Contoh

Proposal Baru

↓

Verifikasi

↓

Review

↓

Keputusan

↓

Tanda Tangan

↓

Selesai

Setiap tahap menampilkan jumlah proposal.

Klik setiap tahap akan membuka halaman terkait.

---

# 63. Charts

SEMAR bukan Business Intelligence Dashboard.

Grafik hanya digunakan jika memberikan informasi yang tidak dapat dibaca lebih cepat melalui tabel.

Maksimal dua grafik dalam satu dashboard.

---

Grafik yang diperbolehkan

Line Chart

Bar Chart Horizontal

Stacked Bar

---

Grafik yang tidak diperbolehkan

Radar

Gauge

Donut lebih dari satu

3D Chart

Area Gradient

Bubble

Polar

Speedometer

---

# 64. Chart Style

Flat.

Tanpa gradient.

Tanpa shadow.

Tanpa efek transparan.

Menggunakan warna brand.

---

# 65. Dashboard Table

Dashboard harus selalu memiliki tabel operasional.

Ini adalah komponen utama.

Contoh

Proposal Terbaru

Proposal Menunggu Review

Proposal Menunggu Keputusan

Proposal Menunggu Tanda Tangan

Tabel lebih penting daripada grafik.

---

# 66. Recent Activity

Gunakan Timeline sederhana.

Tampilkan maksimal

10 aktivitas.

Urutan terbaru.

Format

Jam

↓

User

↓

Aktivitas

↓

Proposal

---

# 67. Notification Panel

Dashboard tidak perlu menampilkan seluruh notifikasi.

Cukup:

5 terbaru

+

Button

"Lihat Semua"

---

# 68. Dashboard Filter

Dashboard hanya memiliki filter global.

Contoh

Periode

Fakultas

Program Studi

Jenis Penelitian

Status

Filter tidak boleh tersembunyi.

---

# 69. Dashboard Responsiveness

Desktop

4 KPI per baris

Tablet

2 KPI

Mobile

1 KPI

Workflow berubah menjadi vertical stack.

---

# 70. Dashboard Performance

Dashboard tidak boleh memuat seluruh data.

Gunakan:

Aggregate

Count

Latest

Limit

Dashboard tidak boleh melakukan query yang berat.

---

# 71. Admin Dashboard

Fokus utama

Monitoring Sistem

Widget wajib

Total Proposal

Proposal Aktif

Proposal Selesai

Proposal Ditolak

Workflow Overview

Proposal Terbaru

Aktivitas Sistem

Tidak menampilkan informasi yang tidak memiliki nilai operasional.

---

# 72. Secretariat Dashboard

Fokus utama

Pekerjaan hari ini.

Widget

Proposal Baru

Dokumen Belum Diverifikasi

Reviewer Belum Ditugaskan

Review Sudah Lengkap

Keputusan Menunggu

Proposal Revisi

Tabel

Proposal yang membutuhkan tindakan.

---

# 73. Reviewer Dashboard

Dashboard reviewer hanya menjawab tiga pertanyaan.

Apa yang harus saya review?

Kapan deadline?

Apa yang sudah selesai?

Widget

Perlu Direview

Mendekati Deadline

Selesai Direview

Tabel

Daftar Review

Urut deadline terdekat.

---

# 74. Student Dashboard

Mahasiswa tidak membutuhkan statistik.

Mahasiswa membutuhkan status.

Widget

Proposal Terakhir

Status Saat Ini

Progress Workflow

Notifikasi

Timeline

Riwayat Pengajuan

---

# 75. Chairman Dashboard

Ketua hanya melakukan penandatanganan.

Dashboard berisi

Menunggu Tanda Tangan

Sudah Ditandatangani

Riwayat

Tidak memerlukan grafik.

---

# 76. Dashboard Actions

Semua dashboard memiliki Primary Action.

Contoh

Ajukan Proposal

↓

Assign Reviewer

↓

Buat Keputusan

↓

Tandatangani

Primary Action selalu berada kanan atas.

---

# 77. Workflow Timeline

Workflow ditampilkan horizontal.

Menggunakan 6 tahap.

Proposal

↓

Verifikasi

↓

Review

↓

Keputusan

↓

Tanda Tangan

↓

Selesai

Status

Selesai

Aktif

Belum

dibedakan menggunakan warna.

---

# 78. Data Priority

Prioritas informasi.

1

Pekerjaan yang harus dilakukan.

2

Status proposal.

3

Deadline.

4

Riwayat aktivitas.

5

Statistik.

Jangan membalik urutan ini.

---

# 79. Empty Dashboard

Jika belum ada data.

Tampilkan

Judul

Deskripsi

Primary Action

Contoh

Belum ada proposal.

Ajukan proposal pertama Anda.

Button

Ajukan Proposal

---

# 80. Dashboard Anti-pattern

Dilarang menggunakan

Gradient Background

Emoji

Card bertumpuk

Animasi berlebihan

Grafik dekoratif

Progress palsu

Chart tanpa insight

Glassmorphism

Background bergambar

Floating Card

Hero Banner

Dashboard SEMAR harus terasa seperti sistem akademik profesional yang digunakan setiap hari oleh institusi, bukan template admin generik.

# 81. Page Pattern

Seluruh halaman SEMAR wajib mengikuti pola layout yang sama.

Jangan membuat layout berbeda antar modul.

Urutan halaman selalu:

Breadcrumb

↓

Page Header

↓

Page Description (opsional)

↓

Action Toolbar

↓

Main Content

↓

Secondary Content (opsional)

---

# 82. Breadcrumb

Selalu tampil.

Contoh

Dashboard

/

Pengajuan

/

Proposal

/

Review

Breadcrumb tidak boleh dihilangkan.

---

# 83. Page Header

Terdiri dari

Title

Subtitle

Primary Action

Contoh

Review Proposal

Lakukan penilaian proposal sesuai pedoman etik penelitian.

[ Submit Review ]

---

# 84. Content Width

Form

maksimal

960 px

Reading Mode

720 px

Dashboard

Full Width

Table

Full Width

---

# 85. Student Pages

Mahasiswa selalu berorientasi pada proses.

Urutan informasi

Status Proposal

↓

Progress Workflow

↓

Informasi Proposal

↓

Dokumen

↓

Catatan Reviewer

↓

Riwayat

↓

Aksi

Mahasiswa tidak membutuhkan statistik.

---

# 86. Submission Detail

Layout

Header

↓

Status Card

↓

Workflow Timeline

↓

Proposal Information

↓

Uploaded Documents

↓

Reviewer Notes

↓

Revision History

↓

Action Button

Informasi proposal menggunakan dua kolom.

Dokumen menggunakan tabel.

---

# 87. Proposal Information

Gunakan Description List.

Contoh

Kode

Jenis

Judul

Peneliti

Tanggal

Status

Jangan menggunakan card terpisah untuk setiap field.

---

# 88. Uploaded Documents

Gunakan tabel.

Kolom

Nama

Jenis

Ukuran

Tanggal

Aksi

Tidak menggunakan gallery.

---

# 89. Reviewer Notes

Urut berdasarkan Revision Round.

Contoh

Revision Round 1

Reviewer A

Reviewer B

Catatan Mahasiswa

Revision Round 2

Reviewer A

Reviewer B

Catatan Mahasiswa

Setiap round dipisahkan jelas.

---

# 90. Revision Form

Muncul hanya saat status

REVISION_REQUIRED

Form

Catatan Revisi

↓

Upload Dokumen

↓

Submit

Tidak menggunakan popup.

---

# 91. Reviewer Pages

Reviewer hanya memiliki satu fokus.

Menilai proposal.

Layout

Proposal Information

↓

Proposal Documents

↓

Riwayat Review Sendiri

↓

Catatan Revisi Mahasiswa

↓

Review Form

Sidebar internal dilarang.

---

# 92. Reading Mode

Saat reviewer membaca proposal.

Sidebar dapat disembunyikan.

Lebar maksimum

720 px

Font

Lora

Line Height

1.8

Tidak ada elemen yang mengganggu.

---

# 93. Review Form

Urutan

Recommendation

↓

Notes

↓

Attachment

↓

Submit Review

Submit berada paling bawah.

---

# 94. Recommendation

Menggunakan Radio Button.

APPROVE

REVISION

REJECT

Bukan dropdown.

---

# 95. Secretariat Pages

Sekretariat berorientasi pada pekerjaan.

Layout

Proposal Summary

↓

Document Verification

↓

Reviewer Assignment

↓

Review Result

↓

Decision

↓

History

Semua informasi berada dalam satu halaman.

---

# 96. Reviewer Assignment

Gunakan tabel.

Kolom

Reviewer

Deadline

Status

Submitted At

Action

Status Assignment berbeda dengan Status Proposal.

---

# 97. Review Result

Dikelompokkan berdasarkan Revision Round.

Setiap reviewer memiliki card sederhana.

Nama

↓

Rekomendasi

↓

Catatan

↓

Lampiran

Tidak menggunakan accordion jika hanya dua reviewer.

---

# 98. Decision Form

Selalu berada di bawah hasil review.

Pilihan

Approve

Revision

Reject

↓

Notes

↓

Submit Decision

Tidak menggunakan wizard.

---

# 99. Chairman Pages

Ketua hanya memiliki satu tugas.

Menandatangani.

Layout

Proposal Summary

↓

Ethical Clearance Draft

↓

Verification

↓

Sign Button

Informasi lain disembunyikan.

---

# 100. Admin Pages

Admin berorientasi pada pengelolaan.

Layout

Toolbar

↓

Table

↓

Pagination

↓

Modal

Admin tidak menggunakan halaman form panjang jika dapat menggunakan modal.

---

# 101. CRUD Pattern

Index

↓

Create

↓

Edit

↓

Delete

Semua module mengikuti pola yang sama.

---

# 102. Table Pages

Urutan

Search

↓

Filter

↓

Export

↓

Primary Action

↓

Table

↓

Pagination

Tidak ada widget lain.

---

# 103. Detail Pages

Gunakan section.

Information

↓

Documents

↓

Workflow

↓

History

↓

Activity Log

---

# 104. History

Riwayat menggunakan Timeline.

Tanggal

↓

User

↓

Aktivitas

↓

Catatan

---

# 105. Activity Log

Tidak menggunakan tabel.

Gunakan Timeline Vertikal.

Lebih mudah dibaca.

---

# 106. Notification Page

Urutan

Belum Dibaca

↓

Sudah Dibaca

Setiap notifikasi

Title

↓

Description

↓

Date

↓

Open

---

# 107. Report Pages

Report bukan Dashboard.

Report menggunakan

Filter

↓

Summary

↓

Table

↓

Export

Grafik hanya jika diperlukan.

---

# 108. Settings

Gunakan tab.

General

Workflow

Notification

Template

System

Tidak menggunakan halaman panjang.

---

# 109. Mobile Pattern

Card boleh berubah menjadi stack.

Table menjadi horizontal scroll.

Action menjadi full width.

---

# 110. Anti Pattern

Dilarang

Nested Card lebih dari dua level.

Form di dalam Modal di dalam Drawer.

Accordion di dalam Accordion.

Card di dalam Card lebih dari dua lapis.

Table di dalam Card di dalam Table.

Layout harus tetap sederhana.

# 111. AI Implementation Rules

Dokumen ini merupakan standar mutlak.

Seluruh AI Assistant yang digunakan untuk mengembangkan SEMAR wajib mengikuti seluruh aturan pada Design System ini.

Tidak diperbolehkan membuat interpretasi visual sendiri.

Jika terjadi konflik antara preferensi AI dan Design System, maka Design System selalu menjadi prioritas.

---

# 112. Design Priority

Urutan prioritas.

1.
Usability

2.
Readability

3.
Consistency

4.
Workflow

5.
Performance

6.
Aesthetics

Keindahan visual tidak boleh mengorbankan usability.

---

# 113. Design Consistency

Semua halaman harus terlihat berasal dari sistem yang sama.

AI tidak boleh membuat:

halaman A bergaya modern

halaman B bergaya startup

halaman C bergaya material

halaman D bergaya dashboard template

Semua harus memiliki identitas visual yang sama.

---

# 114. Component Reuse

AI wajib menggunakan komponen yang sudah ada.

Jika terdapat Button Primary,

gunakan Button Primary tersebut.

Jangan membuat Button Primary baru.

Jika terdapat Card,

gunakan Card yang sama.

Tidak membuat variasi baru.

---

# 115. Color Rules

Gunakan hanya warna yang terdapat pada Design System.

Tidak membuat warna baru.

Tidak membuat variasi opacity tanpa alasan.

Tidak membuat palette tambahan.

---

# 116. Typography Rules

Heading

Plus Jakarta Sans

Body UI

Plus Jakarta Sans

Academic Reading

Lora

AI tidak boleh mengganti font.

---

# 117. Layout Rules

Gunakan layout yang telah ditentukan.

Sidebar

↓

Header

↓

Breadcrumb

↓

Content

↓

Footer

Tidak membuat layout alternatif.

---

# 118. White Space

Gunakan whitespace sebagai pemisah utama.

Jangan menggunakan garis berlebihan.

Jangan menggunakan background berbeda hanya untuk memisahkan section.

---

# 119. Card Rules

Card digunakan hanya jika memang diperlukan.

Card bukan dekorasi.

Maksimal dua level nesting.

Contoh

Card

↓

Section

Bukan

Card

↓

Card

↓

Card

↓

Card

---

# 120. Table Rules

Jika data lebih dari lima baris,

gunakan tabel.

Jangan menggunakan card list.

Enterprise System lebih cocok menggunakan tabel.

---

# 121. Form Rules

Semua form mengikuti pola.

Label

↓

Input

↓

Help Text

↓

Validation

↓

Action

Tidak mengubah urutan.

---

# 122. Workflow Rules

Workflow harus selalu terlihat.

Pengguna harus mengetahui posisi proposal saat ini.

Workflow tidak boleh disembunyikan.

---

# 123. Notification Rules

Semua notifikasi menggunakan format yang sama.

Title

↓

Description

↓

Time

↓

Action

---

# 124. Status Rules

Status menggunakan Badge.

Tidak menggunakan warna teks saja.

Tidak menggunakan icon saja.

Tidak menggunakan emoji.

---

# 125. Responsive Rules

Desktop merupakan prioritas utama.

Tablet kedua.

Mobile ketiga.

AI tidak boleh mendesain mobile terlebih dahulu.

---

# 126. Accessibility Rules

Semua tombol dapat diakses keyboard.

Focus terlihat jelas.

Kontras memenuhi WCAG AA.

---

# 127. Animation Rules

Animasi hanya digunakan untuk:

Dropdown

Modal

Tooltip

Toast

Loading

Selain itu tidak diperlukan.

---

# 128. Performance Rules

AI harus menghindari:

DOM terlalu dalam

Nested div berlebihan

Component berulang

Loop yang tidak perlu

Query di Blade

---

# 129. Laravel Rules

Gunakan:

Blade Component

Laravel Route

Policy

Gate

Eloquent Relationship

Form Request

Collection

Resource Controller

Hindari query langsung di View.

---

# 130. Tailwind Rules

Gunakan utility seperlunya.

Jika style sering digunakan,

buat Blade Component.

Jangan menyalin class Tailwind ratusan karakter.

---

# 131. Naming Rules

Gunakan nama yang konsisten.

Contoh

Proposal

Reviewer

Secretary

Decision

Revision

Workflow

Tidak mencampur istilah.

---

# 132. UX Rules

Satu halaman,

satu tujuan.

Jangan memasukkan terlalu banyak aksi.

---

# 133. Professional Tone

Interface harus terasa seperti:

Government System

University Enterprise

Research Information System

Bukan:

Startup

Crypto Dashboard

Portfolio

Landing Page

Marketing Website

---

# 134. Forbidden Design

AI DILARANG menghasilkan:

Gradient Background

Glassmorphism

Neumorphism

Heavy Shadow

Glow

Hero Banner

Floating Widget

Animated Background

Particle Effect

Glass Card

3D Card

Emoji

Sticker

Illustration besar

Avatar dekoratif

Wallpaper

Background Pattern

Mesh Gradient

Blob Shape

Ribbon

Floating FAB

Rounded berlebihan

Gradient Border

Chart 3D

Gauge

Radar Chart

Donut Chart bertumpuk

Pie Chart lebih dari satu

Dashboard Template

Statistic Card dengan Icon Raksasa

Progress Ring

Decorative Divider

Wave

Abstract Shape

---

# 135. Preferred Design

AI lebih memilih:

Flat Design

Enterprise Layout

Whitespace

Simple Card

Professional Table

Timeline

Workflow

Description List

Status Badge

Minimal Chart

High Readability

Simple Interaction

---

# 136. Final Principle

SEMAR bukan dibuat untuk menarik perhatian pengguna.

SEMAR dibuat agar pekerjaan administrasi Ethical Clearance dapat diselesaikan lebih cepat, lebih nyaman, dan lebih akurat.

Seluruh keputusan desain harus mendukung tujuan tersebut.

Apabila terdapat dua alternatif desain dengan kualitas visual yang sama, pilih desain yang:

lebih sederhana,

lebih mudah dipelajari,

lebih cepat digunakan,

lebih mudah dipelihara,

dan lebih konsisten dengan keseluruhan sistem.