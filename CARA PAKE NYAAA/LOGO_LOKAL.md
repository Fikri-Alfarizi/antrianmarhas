# Cara Menambahkan/Mengganti Logo Lokal

1. Letakkan file logo Anda (format: .png, .jpg, .jpeg, .gif) di folder:
   
   `public/logo/`

2. Upload logo melalui halaman pengaturan admin. File akan otomatis disimpan di folder tersebut.

3. Pastikan nama file logo yang tersimpan di database hanya nama file (misal: `logo_1700000000.png`).

4. Semua tampilan akan otomatis mengambil logo dari `/logo/{nama_file}`.

5. Tidak perlu lagi menggunakan storage:link atau imgBB/Cloudinary.

6. Jika ingin mengganti logo, upload logo baru melalui pengaturan. Logo lama akan dihapus otomatis.

**Catatan:**
- Pastikan file logo tidak terlalu besar (maksimal 2MB).
- Jika logo tidak muncul, pastikan file benar-benar ada di `public/logo/` dan nama file sesuai di database.
