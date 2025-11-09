@extends('layouts.app')

@section('content')
<div style="padding: 20px;">
    <style>
        .settings-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 900px;
        }
        
        .settings-container h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .settings-section {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .settings-section:last-child {
            border-bottom: none;
        }
        
        .settings-section h2 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="time"],
        .form-group input[type="number"],
        .form-group input[type="color"],
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .color-preview {
            display: inline-block;
            width: 40px;
            height: 40px;
            border-radius: 5px;
            border: 2px solid #ddd;
            margin-left: 10px;
            vertical-align: middle;
        }
        
        .closed-days-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .closed-day-checkbox {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .closed-day-checkbox:hover {
            background: #f0f0f0;
        }
        
        .closed-day-checkbox input {
            margin-right: 8px;
        }
        
        .info-box {
            background: #ecf0f1;
            border-left: 4px solid #3498db;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #555;
        }
        
        .submit-btn {
            background: #27ae60;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .submit-btn:hover {
            background: #229954;
        }
    </style>
    
    <div class="settings-container">
        <h1>
            <i class="fas fa-sliders-h" style="color: #3498db;"></i>
            Pengaturan Lanjutan
        </h1>
        
        <form action="{{ route('admin.advanced-settings.update') }}" method="POST">
            @csrf
            
            <!-- Queue Timeout Settings -->
            <div class="settings-section">
                <h2><i class="fas fa-hourglass-end"></i> Pengaturan Timeout Antrian</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="queue_timeout_minutes">Timeout Antrian (menit)</label>
                        <input type="number" id="queue_timeout_minutes" name="queue_timeout_minutes" 
                               value="{{ $settings->queue_timeout_minutes ?? 30 }}" min="5" max="240" required>
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i> Antrian akan dibatalkan otomatis jika melampaui waktu ini
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px; margin-top: 25px;">
                            <input type="checkbox" name="auto_cancel_timeout" 
                                   {{ $settings->auto_cancel_timeout ? 'checked' : '' }}>
                            <span>Otomatis Batalkan Antrian yang Timeout</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Display Settings -->
            <div class="settings-section">
                <h2><i class="fas fa-palette"></i> Pengaturan Tampilan</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="theme_color">Warna Utama</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="color" id="theme_color" name="theme_color" 
                                   value="{{ $settings->theme_color ?? '#3498db' }}" required>
                            <span id="themeColorValue">{{ $settings->theme_color ?? '#3498db' }}</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="secondary_color">Warna Sekunder</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="color" id="secondary_color" name="secondary_color" 
                                   value="{{ $settings->secondary_color ?? '#2c3e50' }}" required>
                            <span id="secondaryColorValue">{{ $settings->secondary_color ?? '#2c3e50' }}</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_refresh_seconds">Refresh Display (detik)</label>
                        <input type="number" id="display_refresh_seconds" name="display_refresh_seconds" 
                               value="{{ $settings->display_refresh_seconds ?? 5 }}" min="1" max="60" required>
                    </div>
                </div>
            </div>
            
            <!-- Notification Settings -->
            <div class="settings-section">
                <h2><i class="fas fa-bell"></i> Pengaturan Notifikasi</h2>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="email_notification_enabled" 
                               {{ $settings->email_notification_enabled ? 'checked' : '' }}>
                        <span>Aktifkan Notifikasi Email</span>
                    </label>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email_notification_recipient">Email Penerima</label>
                        <input type="email" id="email_notification_recipient" name="email_notification_recipient" 
                               value="{{ $settings->email_notification_recipient ?? '' }}" placeholder="admin@example.com">
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="sms_notification_enabled" 
                               {{ $settings->sms_notification_enabled ? 'checked' : '' }}>
                        <span>Aktifkan Notifikasi SMS</span>
                    </label>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="sms_notification_number">Nomor HP Penerima</label>
                        <input type="tel" id="sms_notification_number" name="sms_notification_number" 
                               value="{{ $settings->sms_notification_number ?? '' }}" placeholder="+62812345678">
                    </div>
                </div>
            </div>
            
            <!-- Working Hours -->
            <div class="settings-section">
                <h2><i class="fas fa-clock"></i> Jam Kerja & Hari Libur</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="working_hours_start">Jam Buka</label>
                        <input type="time" id="working_hours_start" name="working_hours_start" 
                               value="{{ $settings->working_hours_start ? $settings->working_hours_start->format('H:i') : '08:00' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="working_hours_end">Jam Tutup</label>
                        <input type="time" id="working_hours_end" name="working_hours_end" 
                               value="{{ $settings->working_hours_end ? $settings->working_hours_end->format('H:i') : '17:00' }}" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-calendar-times"></i> Hari Libur</label>
                    <div class="closed-days-grid">
                        @foreach($closedDays as $key => $label)
                        <label class="closed-day-checkbox">
                            <input type="checkbox" name="closed_days[]" value="{{ $key }}"
                                   {{ in_array($key, $settings->closed_days ?? []) ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Advanced Features -->
            <div class="settings-section">
                <h2><i class="fas fa-cogs"></i> Fitur Lanjutan</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="max_queue_per_loket">Maksimal Antrian per Loket</label>
                        <input type="number" id="max_queue_per_loket" name="max_queue_per_loket" 
                               value="{{ $settings->max_queue_per_loket ?? 100 }}" min="10" max="500" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="auto_assign_loket" 
                               {{ $settings->auto_assign_loket ? 'checked' : '' }}>
                        <span>Auto Assign Loket ke Layanan yang Tersedia</span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="enable_customer_feedback" 
                               {{ $settings->enable_customer_feedback ? 'checked' : '' }}>
                        <span>Aktifkan Feedback Pelanggan</span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="maintenance_mode" 
                               {{ $settings->maintenance_mode ? 'checked' : '' }}>
                        <span style="color: #e74c3c; font-weight: bold;">Mode Pemeliharaan (Sistem Offline)</span>
                    </label>
                    <div class="info-box" style="margin-top: 10px; border-left-color: #e74c3c; color: #c0392b;">
                        <i class="fas fa-exclamation-triangle"></i> Sistem tidak dapat diakses publik saat mode pemeliharaan aktif
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>

<script>
    // Update color display
    document.getElementById('theme_color').addEventListener('change', function() {
        document.getElementById('themeColorValue').textContent = this.value;
    });
    
    document.getElementById('secondary_color').addEventListener('change', function() {
        document.getElementById('secondaryColorValue').textContent = this.value;
    });
</script>
@endsection
