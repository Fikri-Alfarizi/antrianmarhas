@extends('layouts.app')
@section('title', 'Loket Panggilan')

@section('styles')
<style>
/* CSS dari file UI Loket Panggilan (Hanya yang relevan untuk content) */
.page-header { margin-bottom: 32px; }
.page-title { font-size: 28px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; }
.page-subtitle { font-size: 14px; color: #64748b; margin: 0; font-weight: 500; }

.live-status-card { display: flex; align-items: center; gap: 20px; margin-bottom: 24px; padding: 24px; }
.live-icon { width: 52px; height: 52px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
.live-content { flex: 1; }
.live-content h2 { margin: 0 0 4px 0; font-size: 18px; font-weight: 700; color: #0f172a; }
.live-content p { margin: 0; font-size: 13px; color: #64748b; font-weight: 500; }
.live-badge { display: flex; align-items: center; gap: 6px; padding: 8px 14px; background: #dcfce7; border-radius: 10px; font-size: 12px; font-weight: 600; color: #16a34a; }
.live-dot { width: 7px; height: 7px; background: #16a34a; border-radius: 50%; animation: pulse 2s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }


/* --- Main Content Grid --- */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px;
    margin-bottom: 24px;
}

/* --- Status Loket (Card Kanan Atas) --- */
.status-loket-wrapper { 
    display: flex; 
    flex-direction: column; 
    gap: 20px; 
    padding: 24px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.status-icon-box { width: 60px; height: 60px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); transition: all 0.3s; }
.status-icon-box.closed { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25); }
.status-info { flex: 1; }
.status-label { font-size: 13px; color: #64748b; font-weight: 600; margin-bottom: 4px; }
.status-value { font-size: 18px; font-weight: 800; color: #16a34a; }
.status-value.closed { color: #dc2626; }
.btn-status { width: 100%; padding: 14px; border: none; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.25s; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25); }
.btn-status:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(239, 68, 68, 0.3); }
.btn-status.open { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); }
.btn-status.open:hover { box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3); }


/* --- Patient Status (Card Kiri Atas) --- */
.patient-status { min-height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; text-align: center; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 16px; padding: 32px; border: 1px solid #e2e8f0; }
.patient-status-number { font-size: 64px; font-weight: 800; color: #3b82f6; line-height: 1; }
.patient-status-text { font-size: 14px; color: #64748b; font-weight: 500; line-height: 1.6; }


/* --- Bottom Grid: Queue & Stats --- */
.bottom-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }

/* --- Queue Section (Card Kiri Bawah) --- */
.queue-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.queue-title { font-size: 17px; font-weight: 700; color: #0f172a; }
.queue-count { font-size: 12px; padding: 6px 12px; background: #fef3c7; border-radius: 8px; color: #f59e0b; font-weight: 600; }
.btn-call { width: 100%; padding: 18px; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.25s; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
.btn-call:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4); }
.btn-call:disabled { background: #94a3b8; box-shadow: none; cursor: not-allowed; transform: none; }

/* --- Statistics (Card Kanan Bawah) --- */
.stats-title { font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 20px; }
.stat-item { display: flex; align-items: center; gap: 14px; padding: 14px; background: #f8fafc; border-radius: 12px; margin-bottom: 10px; transition: all 0.2s; }
.stat-item:hover { background: #f1f5f9; transform: translateX(3px); }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; }
.stat-icon.primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.stat-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.stat-value { font-size: 22px; font-weight: 800; }
.stat-value.primary { color: #3b82f6; }
.stat-value.success { color: #10b981; }
.stat-value.warning { color: #f59e0b; }
.stat-value.danger { color: #ef4444; }


/* --- Loket Control Grid (Tombol di UI yang Anda kirim) --- */
.loket-control-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.loket-room-card { background: white; border: 2px solid #e2e8f0; border-radius: 14px; padding: 20px; cursor: pointer; transition: all 0.25s; text-align: center; }
.loket-room-card:hover { border-color: #3b82f6; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(59, 130, 246, 0.15); }
.loket-room-card.active { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-color: transparent; color: white; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3); }
.loket-room-number { font-size: 17px; font-weight: 700; margin-bottom: 8px; }
.loket-room-service { font-size: 12px; opacity: 0.8; font-weight: 500; margin-bottom: 8px; }
.loket-room-status { font-size: 11px; padding: 4px 12px; background: rgba(16, 185, 129, 0.12); border-radius: 8px; display: inline-block; font-weight: 600; color: #16a34a; }
.loket-room-card.active .loket-room-status { background: rgba(255, 255, 255, 0.2); color: white; }
.loket-room-card.status-tutup { opacity: 0.6; border: 2px solid #ef4444; }
.loket-room-card.status-tutup:hover { border-color: #ef4444; transform: none; box-shadow: none; }

/* --- Waiting Queue List --- */
.waiting-queue-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 300px;
    overflow-y: auto;
}

.queue-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    transition: all 0.2s;
}

.queue-item:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    transform: translateX(3px);
}

.queue-item-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.queue-item-number {
    font-size: 20px;
    font-weight: 800;
    color: #3b82f6;
    min-width: 40px;
    text-align: center;
    padding: 6px 10px;
    background: #dbeafe;
    border-radius: 8px;
}

.queue-item-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.queue-item-layanan {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
}

.queue-item-waktu {
    font-size: 11px;
    color: #64748b;
}

.queue-item-empty {
    text-align: center;
    color: #94a3b8;
    font-size: 13px;
    padding: 20px 10px;
}

/* --- Tracking & Messaging Panels --- */
.tracking-section { margin-top: 40px; }
.section-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 20px; }

.tracking-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px; }

.tracking-history-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.tracking-item {
    padding: 12px;
    border-left: 4px solid #3b82f6;
    background: #f8fafc;
    border-radius: 8px;
    margin-bottom: 10px;
    font-size: 13px;
    transition: all 0.2s;
}

.tracking-item:hover { background: #f1f5f9; transform: translateX(3px); }

.tracking-item.called { border-left-color: #3b82f6; }
.tracking-item.served { border-left-color: #10b981; }
.tracking-item.finished { border-left-color: #16a34a; }
.tracking-item.cancelled { border-left-color: #ef4444; }

.tracking-item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.tracking-item-antrian { font-weight: 700; color: #0f172a; }
.tracking-item-action { font-size: 11px; padding: 2px 8px; border-radius: 4px; background: #dbeafe; color: #1e40af; font-weight: 600; }
.tracking-item-action.served { background: #dcfce7; color: #166534; }
.tracking-item-action.finished { background: #ccfbf1; color: #164e63; }
.tracking-item-action.cancelled { background: #fee2e2; color: #991b1b; }
.tracking-item-time { font-size: 12px; color: #94a3b8; }

.staff-activity-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.staff-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
    margin-bottom: 8px;
    font-size: 13px;
}

.staff-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 14px;
}

.staff-info { flex: 1; }
.staff-name { font-weight: 700; color: #0f172a; }
.staff-status { font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 4px; }
.staff-status.active::before { content: '●'; color: #10b981; }
.staff-status.idle::before { content: '●'; color: #f59e0b; }

/* --- Messaging Modal --- */
.messaging-section {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 24px;
}

.message-form { display: flex; flex-direction: column; gap: 12px; }
.message-form-group { display: flex; flex-direction: column; }
.message-form-group label { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
.message-form-group select,
.message-form-group textarea {
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    transition: all 0.2s;
}

.message-form-group select:focus,
.message-form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.message-form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.btn-send { padding: 12px 24px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.25s; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
.btn-send:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4); }

/* --- Message Popup --- */
.message-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    z-index: 9999;
    max-width: 500px;
    min-width: 300px;
    text-align: center;
    animation: popupSlideIn 0.3s ease-out;
}

.message-popup.info { border-top: 4px solid #3b82f6; }
.message-popup.warning { border-top: 4px solid #f59e0b; }
.message-popup.error { border-top: 4px solid #ef4444; }
.message-popup.success { border-top: 4px solid #10b981; }

.message-popup-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.message-popup-title { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
.message-popup-text { font-size: 14px; color: #64748b; margin-bottom: 24px; }
.message-popup-close { font-size: 28px; cursor: pointer; position: absolute; top: 12px; right: 12px; color: #94a3b8; transition: all 0.2s; }
.message-popup-close:hover { color: #0f172a; }

@keyframes popupSlideIn {
    from {
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

.message-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9998;
}

/* --- Chat Widget (Bottom Right) --- */
.chat-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9000;
    font-family: inherit;
}

.chat-bubble {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    transition: all 0.3s ease;
    border: none;
    padding: 0;
    position: relative;
}

.chat-bubble:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.5);
}

.chat-bubble.active {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

.chat-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    border: 2px solid white;
}

.chat-container {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 400px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    max-height: 600px;
    animation: slideUp 0.3s ease-out;
}

.chat-container.active {
    display: flex;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chat-header {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    padding: 16px;
    border-radius: 16px 16px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
}

.chat-header-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s;
}

.chat-header-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

.chat-tabs {
    display: flex;
    border-bottom: 1px solid #e2e8f0;
}

.chat-tab {
    flex: 1;
    padding: 12px;
    border: none;
    background: white;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #94a3b8;
    transition: all 0.2s;
}

.chat-tab.active {
    color: #3b82f6;
    border-bottom: 2px solid #3b82f6;
    margin-bottom: -1px;
}

.chat-tab:hover {
    color: #0f172a;
}

.chat-content {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: none;
}

.chat-content.active {
    display: block;
}

.chat-footer {
    padding: 16px;
    border-top: 1px solid #e2e8f0;
    display: none;
    flex-direction: column;
    gap: 12px;
}

.chat-footer.active {
    display: flex;
}

.chat-footer-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.chat-footer-label {
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
}

.chat-footer-group select,
.chat-footer-group textarea {
    padding: 8px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 12px;
    font-family: inherit;
}

.chat-footer-group select:focus,
.chat-footer-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.chat-footer-group textarea {
    resize: none;
    min-height: 60px;
}

.chat-send-btn {
    padding: 8px 12px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
}

.chat-send-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.chat-empty {
    text-align: center;
    padding: 20px;
    color: #94a3b8;
    font-size: 13px;
}

.tracking-item-compact {
    padding: 8px;
    border-left: 3px solid #3b82f6;
    background: #f8fafc;
    border-radius: 6px;
    margin-bottom: 8px;
    font-size: 12px;
}

.tracking-item-compact.served { border-left-color: #10b981; }
.tracking-item-compact.finished { border-left-color: #16a34a; }
.tracking-item-compact.cancelled { border-left-color: #ef4444; }

.tracking-item-compact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3px;
}

.tracking-item-compact-antrian {
    font-weight: 700;
    color: #0f172a;
    font-size: 11px;
}

.tracking-item-compact-action {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 3px;
    background: #dbeafe;
    color: #1e40af;
    font-weight: 600;
}

.tracking-item-compact-action.served { background: #dcfce7; color: #166534; }
.tracking-item-compact-action.finished { background: #ccfbf1; color: #164e63; }
.tracking-item-compact-action.cancelled { background: #fee2e2; color: #991b1b; }

.tracking-item-compact-time {
    font-size: 11px;
    color: #94a3b8;
}

.staff-item-compact {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    background: #f8fafc;
    border-radius: 6px;
    margin-bottom: 8px;
    font-size: 12px;
}

.staff-avatar-compact {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 12px;
}

.staff-info-compact {
    flex: 1;
    min-width: 0;
}

.staff-name-compact {
    font-weight: 600;
    color: #0f172a;
    font-size: 12px;
}

.staff-status-compact {
    font-size: 11px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 3px;
}

.staff-status-compact.active::before { content: '●'; color: #10b981; }
.staff-status-compact.idle::before { content: '●'; color: #f59e0b; }

/* --- Responsive Adjustments --- */
@media (max-width: 1024px) {
    .content-grid,
    .bottom-grid,
    .tracking-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')

<div class="page-header">
    <h1 class="page-title">Loket Panggilan Antrian</h1>
    <p class="page-subtitle">‎ </p>
</div>

<div class="loket-control-grid" id="loketSelectionGrid">
    <p>Memuat Loket...</p>
</div>

<div class="content-grid">
    
    <div class="card patient-status" id="patientStatusCard">
        <div id="patientStatusContent">
            <i class="fa-regular fa-clock" style="font-size: 44px; color: #94a3b8; opacity: 0.6;"></i>
            <div class="patient-status-text">
                <strong>Tidak ada pasien yang sedang dipanggil</strong><br>
                Pilih loket dan klik tombol "Panggil Antrian" untuk memulai.
            </div>
        </div>
    </div>

    <div class="status-loket-wrapper">
        <div class="status-icon-box" id="loketStatusIcon">
            <i class="fa-solid fa-check"></i>
        </div>
        <div class="status-info">
            <div class="status-label" id="loketStatusLabel">Pilih Loket</div>
            <div class="status-value" id="loketStatusValue">Status</div>
        </div>
        <button class="btn-status" id="btnToggleStatus" disabled>
            Tutup Loket
        </button>
    </div>

</div>

<div class="bottom-grid">
    
    <div class="card">
        <div class="queue-header">
            <div class="queue-title">Antrian Berikutnya</div>
            <div class="queue-count" id="queueCount">0 Menunggu</div>
        </div>
        <button class="btn-call" id="btnCallNext" disabled>
            <i class="fa-solid fa-volume-high"></i>
            Panggil Antrian Selanjutnya
        </button>
        
        <!-- Daftar Antrian Menunggu -->
        <div class="waiting-queue-list" id="waitingQueueList" style="margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 15px;">
            <div style="text-align: center; color: #9ca3af; font-size: 14px;">
                Memuat data antrian...
            </div>
        </div>
    </div>

    <div class="card">
        <div class="stats-title">Statistik Loket Terpilih Hari Ini</div>
        
        <div class="stat-item">
            <div class="stat-icon primary">
                <i class="fa-regular fa-user"></i>
            </div>
            <div class="stat-value primary" id="statTotal">0</div>
        </div>

        <div class="stat-item">
            <div class="stat-icon success">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="stat-value success" id="statSelesai">0</div>
        </div>

        <div class="stat-item">
            <div class="stat-icon warning">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="stat-value warning" id="statMenunggu">0</div>
        </div>

        <div class="stat-item">
            <div class="stat-icon danger">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <div class="stat-value danger" id="statBatal">0</div>
        </div>
    </div>

</div>

<!-- CHAT WIDGET (Bottom Right) -->
<div class="chat-widget" id="chatWidget">
    <button class="chat-bubble" id="chatBubble" title="Buka Chat & Monitoring">
        <i class="fa-solid fa-envelope"></i>
        <span class="chat-badge" id="chatBadge" style="display: none;">0</span>
    </button>

    <div class="chat-container" id="chatContainer">
        <div class="chat-header">
            <h3>Monitoring & Chat</h3>
            <button class="chat-header-close" id="chatClose" title="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="chat-tabs">
            <button class="chat-tab active" data-tab="tracking" onclick="switchChatTab('tracking')">
                <i class="fa-solid fa-clock-rotate-left"></i> Tracking
            </button>
            <button class="chat-tab" data-tab="staff" onclick="switchChatTab('staff')">
                <i class="fa-solid fa-users"></i> Staff
            </button>
            <button class="chat-tab" data-tab="message" onclick="switchChatTab('message')">
                <i class="fa-solid fa-envelope"></i> Chat
            </button>
        </div>

        <!-- TAB 1: TRACKING -->
        <div class="chat-content active" id="tab-tracking">
            <div id="trackingHistoryContainerCompact" style="max-height: 300px; overflow-y: auto;">
                <p class="chat-empty">Memuat...</p>
            </div>
        </div>

        <!-- TAB 2: STAFF ACTIVITY -->
        <div class="chat-content" id="tab-staff">
            <div id="staffActivityContainerCompact" style="max-height: 300px; overflow-y: auto;">
                <p class="chat-empty">Memuat...</p>
            </div>
        </div>

        <!-- TAB 3: MESSAGE -->
        <div class="chat-content" id="tab-message">
            <div id="messageFormContainer" style="max-height: 300px; overflow-y: auto;">
                <form class="message-form" id="messageFormCompact" style="gap: 8px;">
                    @csrf
                    <div class="chat-footer-group">
                        <label class="chat-footer-label">Pilih Petugas *</label>
                        <select id="toUserIdCompact" name="to_user_id" required>
                            <option value="">-- Pilih Petugas --</option>
                        </select>
                    </div>

                    <div class="chat-footer-group">
                        <label class="chat-footer-label">Tipe Pesan</label>
                        <select id="messageTypeCompact" name="message_type">
                            <option value="info">Info</option>
                            <option value="warning">Peringatan</option>
                            <option value="error">Error</option>
                            <option value="success">Sukses</option>
                        </select>
                    </div>

                    <div class="chat-footer-group">
                        <label class="chat-footer-label">Pesan *</label>
                        <textarea id="messageTextCompact" name="message" placeholder="Ketik pesan..." required></textarea>
                    </div>

                    <button type="submit" class="chat-send-btn">
                        <i class="fa-solid fa-paper-plane"></i> Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@vite(['resources/js/bootstrap.js'])

<script>
    // --- Variabel Global & URL ---
    const URL_GET_DATA = "{{ route('admin.pusat-kontrol.data') }}";
    const URL_TOGGLE_STATUS = "{{ route('admin.pusat-kontrol.toggle-status', ['loket' => ':id']) }}";
    const URL_PANGGIL = "{{ route('admin.pusat-kontrol.panggil', ['loket' => ':id']) }}";
    const URL_SELESAI = "{{ route('admin.pusat-kontrol.selesai', ['loket' => ':id']) }}";
    const URL_SEND_MESSAGE = "{{ route('admin.pusat-kontrol.message-send') }}";
    const URL_TRACKING_HISTORY = "{{ route('admin.pusat-kontrol.tracking-history') }}";
    const URL_STAFF_LIST = "{{ route('admin.pusat-kontrol.staff-list') }}";
    const URL_STAFF_ACTIVITY = "{{ route('admin.pusat-kontrol.staff-activity') }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // --- Elemen UI ---
    const loketSelectionGrid = document.getElementById('loketSelectionGrid');
    const loketStatusLabel = document.getElementById('loketStatusLabel');
    const loketStatusValue = document.getElementById('loketStatusValue');
    const loketStatusIcon = document.getElementById('loketStatusIcon');
    const btnToggleStatus = document.getElementById('btnToggleStatus');
    const patientStatusContent = document.getElementById('patientStatusContent');
    const btnCallNext = document.getElementById('btnCallNext');
    const queueCount = document.getElementById('queueCount');
    const statTotal = document.getElementById('statTotal');
    const statSelesai = document.getElementById('statSelesai');
    const statMenunggu = document.getElementById('statMenunggu');
    const statBatal = document.getElementById('statBatal');
    
    // Chat Widget Elements
    const chatBubble = document.getElementById('chatBubble');
    const chatClose = document.getElementById('chatClose');
    const chatContainer = document.getElementById('chatContainer');
    const toUserIdCompact = document.getElementById('toUserIdCompact');
    const messageFormCompact = document.getElementById('messageFormCompact');
    const trackingHistoryContainerCompact = document.getElementById('trackingHistoryContainerCompact');
    const staffActivityContainerCompact = document.getElementById('staffActivityContainerCompact');
    
    // --- State Lokal ---
    let selectedLoketId = null;
    let allLoketsData = [];

    // --- Fungsi Helper ---

    /**
     * Membuat card loket di grid atas
     */
    function createLoketSelectionCard(loket) {
        const isActive = loket.id === selectedLoketId;
        const statusClass = loket.status === 'tutup' ? 'status-tutup' : 'status-buka';
        const statusText = loket.status === 'tutup' ? 'Tutup' : 'Aktif';

        return `
            <div class="loket-room-card ${isActive ? 'active' : ''} ${statusClass}" 
                 data-loket-id="${loket.id}" 
                 onclick="selectLoket(${loket.id})">
                <div class="loket-room-number">${loket.nama_loket}</div>
                <div class="loket-room-service">${loket.layanan}</div>
                <span class="loket-room-status">${statusText}</span>
            </div>
        `;
    }

    /**
     * Memperbarui panel status loket dan statistik
     */
    /**
     * Update daftar antrian yang menunggu
     */
    function updateWaitingQueueList(loket) {
        const container = document.getElementById('waitingQueueList');
        
        if (!loket || !loket.waiting_count || loket.waiting_count === 0) {
            container.innerHTML = `
                <div class="queue-item-empty">
                    <i class="fa-solid fa-bell-concierge" style="font-size: 20px; margin-bottom: 8px; display: block;"></i>
                    Tidak ada antrian menunggu
                </div>
            `;
            return;
        }

        // Fetch waiting queue data
        fetch(`{{ route('admin.pusat-kontrol.waiting-queue', ':loket') }}`.replace(':loket', loket.id))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    let html = '';
                    data.data.forEach((antrian, index) => {
                        html += `
                            <div class="queue-item">
                                <div class="queue-item-left">
                                    <div class="queue-item-number">${antrian.kode_antrian}</div>
                                    <div class="queue-item-info">
                                        <div class="queue-item-layanan">${antrian.layanan || 'N/A'}</div>
                                        <div class="queue-item-waktu">Menunggu sejak ${antrian.waktu_ambil || '-'}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="queue-item-empty">
                            <i class="fa-solid fa-bell-concierge" style="font-size: 20px; margin-bottom: 8px; display: block;"></i>
                            Tidak ada antrian menunggu
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching waiting queue:', error);
                container.innerHTML = `
                    <div class="queue-item-empty">
                        Error memuat data antrian
                    </div>
                `;
            });
    }

    function updatePanel(loket) {
        if (!loket) {
            // Reset panel jika tidak ada loket terpilih
            loketStatusLabel.textContent = 'Pilih Loket';
            loketStatusValue.textContent = 'Pilih Loket';
            loketStatusValue.classList.remove('closed');
            loketStatusIcon.classList.remove('closed');
            loketStatusIcon.innerHTML = '<i class="fa-solid fa-list"></i>';
            btnToggleStatus.disabled = true;
            btnCallNext.disabled = true;
            patientStatusContent.innerHTML = `
                <i class="fa-regular fa-clock" style="font-size: 44px; color: #94a3b8; opacity: 0.6;"></i>
                <div class="patient-status-text">
                    <strong>Tidak ada pasien yang sedang dipanggil</strong><br>
                    Pilih loket dan klik tombol "Panggil Antrian" untuk memulai.
                </div>
            `;
            // Reset Stats
            statTotal.textContent = statSelesai.textContent = statBatal.textContent = statMenunggu.textContent = 0;
            queueCount.textContent = '0 Menunggu';
            return;
        }

        // --- Status Loket ---
        const isOpen = loket.status === 'aktif';
        loketStatusLabel.textContent = `${loket.nama_loket} - ${loket.layanan}`;
        loketStatusValue.textContent = isOpen ? 'SEDANG BUKA' : 'TUTUP SEMENTARA';
        btnToggleStatus.textContent = isOpen ? 'Tutup Loket' : 'Buka Loket';

        if (isOpen) {
            loketStatusValue.classList.remove('closed');
            loketStatusIcon.classList.remove('closed');
            loketStatusIcon.innerHTML = '<i class="fa-solid fa-check"></i>';
            btnToggleStatus.classList.remove('open');
        } else {
            loketStatusValue.classList.add('closed');
            loketStatusIcon.classList.add('closed');
            loketStatusIcon.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            btnToggleStatus.classList.add('open');
        }

        // --- Patient Status ---
        if (loket.antrian) {
            const status = loket.antrian.status;
            const kode = loket.antrian.kode_antrian;
            
            let statusText;
            if (status === 'dipanggil') {
                statusText = `Antrian: <strong style="color: #3b82f6;">${kode}</strong> sedang dipanggil.`;
            } else if (status === 'dilayani') {
                statusText = `Antrian: <strong style="color: #16a34a;">${kode}</strong> sedang dilayani.`;
            } else {
                statusText = 'Antrian: **' + kode + '** - Status tidak diketahui';
            }

            patientStatusContent.innerHTML = `
                <div class="patient-status-number">${kode}</div>
                <div class="patient-status-text">
                    <strong>${statusText}</strong><br>
                    ${loket.antrian.catatan || 'Tidak ada catatan.'}
                </div>
            `;
        } else {
            patientStatusContent.innerHTML = `
                <i class="fa-regular fa-clock" style="font-size: 44px; color: #94a3b8; opacity: 0.6;"></i>
                <div class="patient-status-text">
                    <strong>Tidak ada pasien yang sedang dipanggil/dilayani.</strong><br>
                    ${loket.waiting_count > 0 ? `Tersedia ${loket.waiting_count} antrian menunggu.` : 'Tidak ada antrian menunggu.'}
                </div>
            `;
        }
        
        // --- Queue & Stats ---
        const canCall = isOpen && loket.waiting_count > 0;
        btnCallNext.disabled = !canCall;
        
        statTotal.textContent = loket.stats.total_antrian || 0;
        statSelesai.textContent = loket.stats.selesai || 0;
        statMenunggu.textContent = loket.waiting_count || 0;
        statBatal.textContent = loket.stats.batal || 0;
        queueCount.textContent = (loket.waiting_count || 0) + ' Menunggu';
        btnToggleStatus.disabled = false;
        
        // --- Update waiting queue list ---
        updateWaitingQueueList(loket);
    }

    /**
     * Dipanggil saat loket baru dipilih
     */
    function selectLoket(loketId) {
        selectedLoketId = loketId;
        // Update tampilan grid untuk menandai yang aktif
        document.querySelectorAll('.loket-room-card').forEach(card => {
            card.classList.remove('active');
            if (parseInt(card.getAttribute('data-loket-id')) === loketId) {
                card.classList.add('active');
            }
        });
        
        // Update panel info
        const loket = allLoketsData.find(l => l.id === loketId);
        updatePanel(loket);
    }

    /**
     * Merender semua kartu loket
     */
    function renderControlGrid(lokets) {
        allLoketsData = lokets; // Simpan data global
        if (lokets.length === 0) {
            loketSelectionGrid.innerHTML = '<p>Belum ada loket yang dibuat di Manajemen Loket.</p>';
            updatePanel(null); // Reset panel
            return;
        }

        let html = '';
        lokets.forEach(loket => {
            html += createLoketSelectionCard(loket);
        });
        loketSelectionGrid.innerHTML = html;

        // Cek jika ada loket yang sedang aktif, pertahankan seleksi
        if (selectedLoketId) {
            const selectedLoket = lokets.find(l => l.id === selectedLoketId);
            updatePanel(selectedLoket);
            // Re-select card agar tetap aktif
            document.querySelector(`.loket-room-card[data-loket-id="${selectedLoketId}"]`)?.classList.add('active');
        } else {
            // Jika belum ada yang dipilih, pilih loket pertama secara default
            selectLoket(lokets[0].id);
        }
    }

    /**
     * Render daftar staff untuk dropdown pesan
     */
    async function renderStaffList() {
        try {
            const response = await fetch(URL_STAFF_LIST);
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                toUserIdCompact.innerHTML = '<option value="">-- Pilih Petugas --</option>';
                
                data.data.forEach(staff => {
                    const option = document.createElement('option');
                    option.value = staff.id;
                    option.textContent = staff.name;
                    toUserIdCompact.appendChild(option);
                });
            } else {
                toUserIdCompact.innerHTML = '<option value="">Tidak ada petugas tersedia</option>';
            }
        } catch (error) {
            console.error('Error fetching staff list:', error);
            toUserIdCompact.innerHTML = '<option value="">Error memuat petugas</option>';
        }
    }

    /**
     * Render tracking history (Compact Version)
     */
    async function updateTrackingHistory() {
        try {
            const response = await fetch(URL_TRACKING_HISTORY);
            const data = await response.json();

            if (data.success) {
                if (data.data.length === 0) {
                    trackingHistoryContainerCompact.innerHTML = '<p class="chat-empty">Belum ada riwayat panggilan.</p>';
                    return;
                }

                let html = '';
                data.data.forEach(tracking => {
                    const actionClass = tracking.action;
                    const actionLabel = {
                        'called': 'Dipanggil',
                        'served': 'Dilayani',
                        'finished': 'Selesai',
                        'cancelled': 'Dibatalkan'
                    }[tracking.action] || tracking.action;

                    html += `
                        <div class="tracking-item-compact ${actionClass}">
                            <div class="tracking-item-compact-header">
                                <span class="tracking-item-compact-antrian">${tracking.antrian_kode}</span>
                                <span class="tracking-item-compact-action ${actionClass}">${actionLabel}</span>
                            </div>
                            <div style="font-size: 11px; color: #64748b;">
                                ${tracking.loket_nama}
                            </div>
                            <div class="tracking-item-compact-time">${tracking.waktu_relatif}</div>
                        </div>
                    `;
                });
                trackingHistoryContainerCompact.innerHTML = html;
            }
        } catch (error) {
            console.error('Error fetching tracking history:', error);
            trackingHistoryContainerCompact.innerHTML = '<p style="color: red; font-size: 12px;">Gagal memuat.</p>';
        }
    }

    /**
     * Render staff activity (Compact Version)
     */
    async function updateStaffActivity() {
        try {
            const response = await fetch(URL_STAFF_ACTIVITY);
            const data = await response.json();

            if (data.success) {
                if (data.data.length === 0) {
                    staffActivityContainerCompact.innerHTML = '<p class="chat-empty">Tidak ada petugas aktif.</p>';
                    return;
                }

                let html = '';
                data.data.forEach(staff => {
                    const avatarChar = staff.user_name.charAt(0).toUpperCase();
                    const statusClass = staff.status === 'active' ? 'active' : 'idle';

                    html += `
                        <div class="staff-item-compact">
                            <div class="staff-avatar-compact">${avatarChar}</div>
                            <div class="staff-info-compact">
                                <div class="staff-name-compact">${staff.user_name}</div>
                                <div class="staff-status-compact ${statusClass}">
                                    ${staff.activity}
                                </div>
                            </div>
                        </div>
                    `;
                });
                staffActivityContainerCompact.innerHTML = html;
            }
        } catch (error) {
            console.error('Error fetching staff activity:', error);
            staffActivityContainerCompact.innerHTML = '<p style="color: red; font-size: 12px;">Gagal memuat.</p>';
        }
    }

    // --- Aksi Tombol (Menggunakan fungsi POST) ---
    async function postAksi(url, aksi = 'aksi') {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
            });
            const data = await response.json();
            if (!data.success) {
                alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                return false;
            }
            fetchControlData(); // Refresh data setelah aksi
            return true;
        } catch (error) {
            console.error(`Gagal ${aksi}:`, error);
            alert(`Gagal melakukan ${aksi}`);
            return false;
        }
    }

    /**
     * Tampilkan popup pesan (untuk notifikasi dari Pusher)
     */
    function showMessagePopup(message, type = 'info') {
        const iconMap = {
            'info': '<i class="fa-solid fa-circle-info" style="color: #3b82f6;"></i>',
            'warning': '<i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i>',
            'error': '<i class="fa-solid fa-circle-xmark" style="color: #ef4444;"></i>',
            'success': '<i class="fa-solid fa-circle-check" style="color: #10b981;"></i>'
        };

        const titleMap = {
            'info': 'Informasi',
            'warning': 'Peringatan',
            'error': 'Error',
            'success': 'Sukses'
        };

        const overlay = document.createElement('div');
        overlay.className = 'message-popup-overlay';

        const popup = document.createElement('div');
        popup.className = `message-popup ${type}`;
        popup.innerHTML = `
            <span class="message-popup-close" onclick="this.parentElement.parentElement.remove(); this.parentElement.remove();">&times;</span>
            <div class="message-popup-icon">${iconMap[type]}</div>
            <div class="message-popup-title">${titleMap[type]}</div>
            <div class="message-popup-text">${message}</div>
        `;

        document.body.appendChild(overlay);
        document.body.appendChild(popup);

        // Auto close after 5 seconds
        setTimeout(() => {
            overlay.remove();
            popup.remove();
        }, 5000);
    }

    // --- Event Listeners ---
    btnCallNext.addEventListener('click', () => {
        if (!selectedLoketId) return alert('Pilih loket terlebih dahulu.');
        let url = URL_PANGGIL.replace(':id', selectedLoketId);
        postAksi(url, 'memanggil antrian');
    });

    btnToggleStatus.addEventListener('click', () => {
        if (!selectedLoketId) return alert('Pilih loket terlebih dahulu.');
        let url = URL_TOGGLE_STATUS.replace(':id', selectedLoketId);
        postAksi(url, 'toggle status loket');
    });

    // Chat Widget Event Listeners
    chatBubble.addEventListener('click', () => {
        chatContainer.classList.toggle('active');
        chatBubble.classList.toggle('active');
    });

    chatClose.addEventListener('click', () => {
        chatContainer.classList.remove('active');
        chatBubble.classList.remove('active');
    });

    // Switch Chat Tabs
    function switchChatTab(tabName) {
        // Remove active from all tabs and contents
        document.querySelectorAll('.chat-tab').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.chat-content').forEach(content => content.classList.remove('active'));

        // Add active to selected tab and content
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
        document.getElementById(`tab-${tabName}`).classList.add('active');
    }

    messageFormCompact.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!toUserIdCompact.value) {
            alert('Pilih petugas terlebih dahulu.');
            return;
        }

        const messageText = document.getElementById('messageTextCompact').value.trim();
        if (!messageText) {
            alert('Pesan tidak boleh kosong.');
            return;
        }

        try {
            const response = await fetch(URL_SEND_MESSAGE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    to_user_id: toUserIdCompact.value,
                    message: messageText,
                    message_type: document.getElementById('messageTypeCompact').value
                })
            });

            const data = await response.json();
            if (data.success) {
                showMessagePopup('Pesan berhasil dikirim!', 'success');
                messageFormCompact.reset();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Gagal mengirim pesan');
        }
    });

    // --- Fungsi Fetch Data (Dipertahankan) ---
    async function fetchControlData() {
        try {
            const response = await fetch(URL_GET_DATA);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            if (data.success) {
                renderControlGrid(data.lokets);
                renderStaffList(data.lokets);
            } else {
                console.error('API returned success: false', data);
            }
        } catch (error) {
            console.error("Gagal mengambil data loket:", error);
            loketSelectionGrid.innerHTML = '<p style="color: red;">Gagal memuat data. Error: ' + error.message + '</p>';
        }
    }

    // ============================================================
    // INISIALISASI
    // ============================================================
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Ambil data pertama kali
        fetchControlData();
        updateTrackingHistory();
        updateStaffActivity();
        
        // 2. Set Polling fallback (10 detik untuk data, 15 detik untuk tracking)
        setInterval(fetchControlData, 10000);
        setInterval(updateTrackingHistory, 15000);
        setInterval(updateStaffActivity, 15000);

        // 3. Listener Real-time (Echo)
        if (typeof Echo !== 'undefined') {
            Echo.channel('antrian-channel')
                .listen('.antrian.dipanggil', (e) => {
                    console.log('Event [antrian.dipanggil] diterima:', e);
                    fetchControlData();
                    updateTrackingHistory();
                })
                .listen('.loket.status.updated', (e) => {
                    console.log('Event [loket.status.updated] diterima:', e);
                    fetchControlData();
                })
                .listen('.loket.toggle.status', (e) => {
                    console.log('Event [loket.toggle.status] diterima:', e);
                    fetchControlData();
                });

            Echo.channel('antrian-tracking')
                .listen('.antrian.tracking.updated', (e) => {
                    console.log('Event [antrian.tracking.updated] diterima:', e);
                    updateTrackingHistory();
                });

            // Listen to private messages channel
            Echo.private('admin-messages.' + "{{ Auth::id() }}")
                .listen('.admin.message.sent', (e) => {
                    console.log('Pesan diterima dari:', e.from_admin);
                    showMessagePopup(`Pesan dari ${e.from_admin}: ${e.message}`, 'info');
                });
        } else {
            console.warn('Echo (Pusher) tidak ditemukan. Halaman ini menggunakan polling.');
        }
    });

</script>
@endsection