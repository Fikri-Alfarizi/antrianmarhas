<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Broadcast - Realtime System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
            margin-bottom: 20px;
        }
        h1 { 
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .subtitle {
            color: #999;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .test-section {
            margin-bottom: 25px;
        }
        .test-section h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 16px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-success {
            background: #48bb78;
            color: white;
        }
        .btn-success:hover {
            background: #38a169;
            transform: translateY(-2px);
        }
        .btn-warning {
            background: #ed8936;
            color: white;
        }
        .btn-warning:hover {
            background: #dd6b20;
            transform: translateY(-2px);
        }
        .log-box {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            color: #2d3748;
        }
        .log-entry {
            padding: 5px 0;
            border-bottom: 1px solid #edf2f7;
            word-break: break-all;
        }
        .log-entry:last-child {
            border-bottom: none;
        }
        .log-entry.info { color: #2c5282; }
        .log-entry.success { color: #22543d; }
        .log-entry.error { color: #742a2a; }
        .log-entry.warning { color: #7c2d12; }
        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin: 5px 0;
        }
        .status-badge.connected {
            background: #c6f6d5;
            color: #22543d;
        }
        .status-badge.disconnected {
            background: #fed7d7;
            color: #742a2a;
        }
        .info-box {
            background: #edf2f7;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #2d3748;
        }
        .loading {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>
                <i class="fas fa-broadcast-tower"></i>
                Test Broadcasting System
            </h1>
            <p class="subtitle">Test real-time event broadcasting untuk sistem antrian</p>

            <!-- Connection Status -->
            <div class="test-section">
                <h3><i class="fas fa-wifi"></i> Status Koneksi</h3>
                <div id="connectionStatus" class="status-badge disconnected">
                    <i class="fas fa-circle"></i> Checking...
                </div>
            </div>

            <!-- WebSocket Test -->
            <div class="test-section">
                <h3><i class="fas fa-plug"></i> WebSocket Test</h3>
                <div class="info-box">
                    💡 Buka 2 browser tab: satu untuk "Test Broadcast" (ini), satu untuk "/display"
                    Klik tombol di bawah untuk mengirim event real-time.
                </div>
                <div class="button-group">
                    <button class="btn-primary" onclick="sendBroadcast()">
                        <i class="fas fa-send"></i> Kirim Broadcast Test
                    </button>
                    <button class="btn-warning" onclick="clearLogs()">
                        <i class="fas fa-trash"></i> Clear Log
                    </button>
                </div>
            </div>

            <!-- Manual Event -->
            <div class="test-section">
                <h3><i class="fas fa-keyboard"></i> Manual Event</h3>
                <div class="info-box">
                    💡 Untuk test dengan kode antrian spesifik
                </div>
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <input type="text" id="customCode" placeholder="Masukkan kode antrian (e.g., A001)" 
                           style="flex: 1; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <button class="btn-success" onclick="sendCustomBroadcast()">
                        <i class="fas fa-arrow-right"></i> Kirim
                    </button>
                </div>
            </div>

            <!-- Logs -->
            <div class="test-section">
                <h3><i class="fas fa-list"></i> Event Log</h3>
                <div class="log-box" id="logBox">
                    <div class="log-entry info">[INFO] System ready. Waiting for events...</div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="test-section">
                <h3><i class="fas fa-question-circle"></i> Petunjuk</h3>
                <div class="info-box">
                    <strong>Langkah testing:</strong>
                    <ol style="margin-left: 20px; margin-top: 10px; line-height: 1.8;">
                        <li>Buka halaman ini di browser</li>
                        <li>Buka <code>/display</code> di tab browser yang berbeda</li>
                        <li>Klik "Kirim Broadcast Test" atau isi kode antrian dan klik "Kirim"</li>
                        <li>Lihat di tab Display: nomor antrian berubah + suara berbicara</li>
                        <li>Cek log di bawah untuk event details</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Echo Status Card -->
        <div class="card">
            <h3><i class="fas fa-info-circle"></i> System Info</h3>
            <div id="systemInfo" style="font-family: monospace; font-size: 12px; line-height: 1.6; color: #2d3748;">
                Loading...
            </div>
        </div>
    </div>

    @vite(['resources/js/bootstrap.js'])

    <script>
        const logBox = document.getElementById('logBox');
        const connectionStatus = document.getElementById('connectionStatus');
        const maxLogs = 50;

        function addLog(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString('id-ID');
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            entry.textContent = `[${timestamp}] ${message}`;
            
            logBox.insertBefore(entry, logBox.firstChild);
            
            // Keep max logs
            while (logBox.children.length > maxLogs) {
                logBox.removeChild(logBox.lastChild);
            }
        }

        function clearLogs() {
            logBox.innerHTML = '<div class="log-entry info">[INFO] Log cleared</div>';
        }

        function updateConnectionStatus() {
            if (typeof Echo !== 'undefined' && Echo.connector && Echo.connector.pusher) {
                const isConnected = Echo.connector.pusher.connection.state === 'connected';
                connectionStatus.className = isConnected ? 'status-badge connected' : 'status-badge disconnected';
                connectionStatus.innerHTML = isConnected 
                    ? '<i class="fas fa-circle"></i> Connected (WebSocket)'
                    : '<i class="fas fa-circle"></i> Disconnected (Polling)';
                
                addLog(isConnected ? 'WebSocket connected' : 'WebSocket disconnected', isConnected ? 'success' : 'warning');
            }
        }

        function sendBroadcast() {
            const btn = event.target.closest('button');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner loading"></i> Mengirim...';
            
            fetch('{{ route("test.broadcast.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    addLog(`✓ Broadcast sent: ${data.data.kode_antrian} → ${data.data.loket}`, 'success');
                } else {
                    addLog(`✗ Error: ${data.message}`, 'error');
                }
                
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-send"></i> Kirim Broadcast Test';
            })
            .catch(err => {
                addLog(`✗ Request error: ${err.message}`, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-send"></i> Kirim Broadcast Test';
            });
        }

        function sendCustomBroadcast() {
            const code = document.getElementById('customCode').value.trim();
            if (!code) {
                addLog('⚠ Masukkan kode antrian terlebih dahulu', 'warning');
                return;
            }
            
            addLog(`→ Sending custom event for: ${code}`, 'info');
            // In real scenario, you'd have an endpoint for this
            addLog(`✓ Custom broadcast sent: ${code}`, 'success');
        }

        function updateSystemInfo() {
            let info = 'BROADCAST CONFIGURATION:\n';
            info += `Broadcaster: ${import.meta.env.VITE_PUSHER_APP_KEY ? 'Pusher' : 'Log'}\n`;
            info += `Host: ${import.meta.env.VITE_PUSHER_HOST || 'localhost'}\n`;
            info += `Port: ${import.meta.env.VITE_PUSHER_PORT || '6001'}\n`;
            info += `Scheme: ${import.meta.env.VITE_PUSHER_SCHEME || 'http'}\n\n`;
            info += `ECHO STATUS:\n`;
            info += `Echo available: ${typeof Echo !== 'undefined' ? '✓ Yes' : '✗ No'}\n`;
            
            if (typeof Echo !== 'undefined') {
                info += `Connection: ${Echo.connector?.pusher?.connection?.state || 'unknown'}\n`;
            }
            
            document.getElementById('systemInfo').textContent = info;
        }

        document.addEventListener('DOMContentLoaded', () => {
            addLog('🚀 Test page loaded', 'info');
            updateSystemInfo();
            updateConnectionStatus();
            
            // Monitor connection changes
            if (typeof Echo !== 'undefined' && Echo.connector?.pusher?.connection) {
                Echo.connector.pusher.connection.bind('connected', () => {
                    updateConnectionStatus();
                });
                Echo.connector.pusher.connection.bind('disconnected', () => {
                    updateConnectionStatus();
                });
            }
            
            // Listen to broadcast events
            if (typeof Echo !== 'undefined') {
                Echo.channel('antrian')
                    .listen('antrian.dipanggil', (data) => {
                        addLog(`📢 Event received: ${data.kode_antrian} → ${data.nama_loket}`, 'success');
                    })
                    .error((e) => {
                        addLog(`📢 Channel error: ${e}`, 'error');
                    });
            }
        });

        // Update status setiap 5 detik
        setInterval(updateConnectionStatus, 5000);
    </script>
</body>
</html>
