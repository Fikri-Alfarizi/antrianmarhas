<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pengaturan;
use App\Models\AudioSetting;
use Illuminate\Support\Facades\DB;

class ResetDataKeepSettings extends Command
{
    protected $signature = 'db:reset-keep-settings';
    
    protected $description = 'Reset database tapi preserve Pengaturan (termasuk logo) dan Audio Settings';

    public function handle()
    {
        // Backup Pengaturan dan AudioSettings sebelum reset
        $pengaturan = Pengaturan::find(1);
        $audioSettings = DB::table('audio_settings')->where('id', 1)->first();
        
        $this->info('Backing up Pengaturan dan Audio Settings...');
        
        // Jalankan migrate fresh tanpa seed
        $this->call('migrate:fresh', ['--seed' => false]);
        
        $this->info('Database reset complete. Restoring settings...');
        
        // Restore Pengaturan
        if ($pengaturan) {
            $pengaturanData = $pengaturan->toArray();
            unset($pengaturanData['updated_at']); // Remove timestamps for fresh insert
            Pengaturan::create($pengaturanData);
            $this->info('✓ Pengaturan (termasuk logo) restored');
        } else {
            // Jika tidak ada backup, buat default
            Pengaturan::create([
                'id' => 1,
                'logo' => null,
                'nama_instansi' => 'RSUD Marhas Medika',
                'alamat' => 'Jl. Kesehatan No. 123, Jakarta Selatan',
                'telepon' => '021-12345678',
                'deskripsi' => 'Rumah Sakit Umum Daerah yang melayani dengan sepenuh hati'
            ]);
            $this->info('✓ Default Pengaturan created');
        }
        
        // Restore Audio Settings
        if ($audioSettings) {
            $audioData = (array) $audioSettings;
            unset($audioData['updated_at']);
            DB::table('audio_settings')->insert($audioData);
            $this->info('✓ Audio Settings restored');
        } else {
            // Default audio settings
            AudioSetting::create([
                'id' => 1,
                'tipe' => 'text-to-speech',
                'bahasa' => 'id',
                'volume' => 80,
                'aktif' => true,
                'format_pesan' => 'Nomor antrian {nomor} silakan menuju ke {lokasi}',
                'voice_url' => null,
            ]);
            $this->info('✓ Default Audio Settings created');
        }
        
        // Jalankan seeder untuk data lainnya
        $this->call('db:seed', [
            '--class' => 'Database\Seeders\LayananSeeder',
            '--force' => true,
        ]);
        $this->call('db:seed', [
            '--class' => 'Database\Seeders\LoketSeeder',
            '--force' => true,
        ]);
        $this->call('db:seed', [
            '--class' => 'Database\Seeders\UserSeeder',
            '--force' => true,
        ]);
        $this->call('db:seed', [
            '--class' => 'Database\Seeders\AntrianSeeder',
            '--force' => true,
        ]);
        
        $this->info('✓ Other seeders completed');
        $this->info('Database reset selesai dengan Pengaturan & Audio Settings terjaga!');
    }
}

