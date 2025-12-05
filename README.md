# Sistem Pengajuan Kredit Kendaraan

Sistem web untuk pengajuan dan persetujuan kredit kendaraan yang dibangun dengan Laravel dan MySQL, dikonfigurasi untuk deployment di Railway.

## 🚀 Fitur Utama

- **Sistem Pengajuan Kredit**: Interface untuk sales mengajukan kredit konsumen
- **Dashboard Approval**: Interface untuk atasan marketing menyetujui atau menolak pengajuan
- **Upload KTP**: Upload dan penyimpanan gambar KTP dalam format Base64
- **Validasi Ketat**: Validasi NIK, harga kendaraan, dan data konsumen
- **Responsive Design**: Interface responsif menggunakan Tailwind CSS
- **Security Headers**: Implementasi security headers untuk keamanan aplikasi
- **Railway Ready**: Konfigurasi lengkap untuk deployment di Railway

## 📋 Fitur Utama

### Form Pengajuan (Sales)
- Input data konsumen (nama, NIK, jenis kendaraan, harga)
- Upload foto KTP dengan validasi
- Validasi NIK unik (tidak boleh duplikat)
- Validasi harga kendaraan (Rp 10 juta - Rp 2 miliar)

### Dashboard Approval (Atasan Marketing)
- View semua pengajuan dengan status
- Filter berdasarkan status (Submitted, Approved, Rejected)
- Search berdasarkan nama atau NIK
- Approve/reject pengajuan dengan catatan
- View gambar KTP dalam modal
- Statistics dashboard

## 🛠️ Tech Stack

- **Backend**: PHP 8.2, Laravel 11
- **Database**: MySQL
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Deployment**: Railway dengan Docker
- **Storage**: Base64 image storage (Railway compatible)

## 📦 Installation

### Local Development

1. **Clone repository**
```bash
git clone <repository-url>
cd credit-system
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database** 
Update `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=credit_system
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Start development server**
```bash
php artisan serve
```

Visit http://localhost:8000

### Railway Deployment

1. **Prepare Railway Project**
   - Create new project di Railway
   - Add MySQL database service
   - Connect GitHub repository

2. **Configure Environment Variables**
   - Set variabel berikut di Railway dashboard:
   ```
   APP_NAME="Credit System"
   APP_ENV=production
   APP_KEY=(generated automatically)
   APP_DEBUG=false
   APP_URL=https://your-app.railway.app
   
   DB_CONNECTION=mysql
   DB_HOST=(from Railway MySQL service)
   DB_PORT=3306
   DB_DATABASE=(from Railway MySQL service)
   DB_USERNAME=(from Railway MySQL service)
   DB_PASSWORD=(from Railway MySQL service)
   ```

3. **Deploy**
   - Push code ke GitHub
   - Railway akan otomatis build dan deploy menggunakan Dockerfile

## 📁 Struktur Project

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── CreditController.php
│   │   ├── Middleware/
│   │   │   └── SecurityHeaders.php
│   │   └── Requests/
│   │       └── CreditApplicationRequest.php
│   └── Models/
│       └── CreditApplication.php
├── database/
│   └── migrations/
│       └── 2024_01_01_000001_create_credit_applications_table.php
├── resources/
│   └── views/
│       └── dashboard.blade.php
├── routes/
│   └── web.php
├── docker/
│   └── apache.conf
├── Dockerfile
├── railway-start.sh
└── README.md
```

## 🔒 Fitur Keamanan

- **CSRF Protection**: Token CSRF untuk semua form
- **Input Validation**: Validasi ketat untuk semua input
- **Security Headers**: X-Frame-Options, CSP, XSS Protection
- **File Upload Security**: Validasi tipe dan ukuran file
- **Database Security**: Prepared statements, input sanitization

## 📊 Database Schema

### Table: credit_applications

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT(20) | Primary key |
| customer_name | VARCHAR(255) | Nama konsumen |
| nik | VARCHAR(16) | NIK konsumen (unique) |
| vehicle_type | VARCHAR(100) | Jenis kendaraan |
| vehicle_price | DECIMAL(15,2) | Harga kendaraan |
| ktp_image_base64 | TEXT | Gambar KTP dalam Base64 |
| status | VARCHAR(50) | Status pengajuan |
| notes | TEXT | Catatan approval/reject |
| approved_at | TIMESTAMP | Waktu approval |
| approved_by | VARCHAR(255) | Nama yang menyetujui |
| created_at | TIMESTAMP | Waktu pengajuan |
| updated_at | TIMESTAMP | Waktu update terakhir |

## 🎯 API Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/` | Dashboard utama |
| POST | `/submit` | Submit pengajuan kredit |
| POST | `/approve/{id}` | Approve pengajuan |
| POST | `/reject/{id}` | Reject pengajuan |
| GET | `/health` | Health check endpoint |

## 📱 Screenshots

### Form Pengajuan
- Interface clean untuk input data konsumen
- Upload KTP dengan drag & drop
- Validasi real-time

### Dashboard Approval
- Table dengan pagination
- Filter dan search functionality
- Modal untuk view KTP
- Statistics cards

## 🚨 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Pastikan MySQL service running
   - Check database credentials
   - Verify network connectivity

2. **File Upload Error**
   - Check file size (max 2MB)
   - Verify image format (JPG/PNG)
   - Ensure proper permissions

3. **Railway Deployment Issues**
   - Check environment variables
   - Verify Dockerfile syntax
   - Monitor build logs

## 📞 Support

Untuk pertanyaan atau issue:
1. Check dokumentasi ini
2. Review error logs
3. Contact development team

## 📄 License

MIT License

---

**Built with ❤️ untuk kemudahan pengajuan kredit kendaraan**