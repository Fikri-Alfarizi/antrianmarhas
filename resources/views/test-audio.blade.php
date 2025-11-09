<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Audio - Antrian</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .section h2 {
            color: #333;
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section h2 i {
            font-size: 20px;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        button {
            width: 100%;
            padding: 14px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 10px;
        }
        button:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        button:active {
            transform: translateY(0);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .info {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #28a745;
        }
        .info h3 {
            color: #28a745;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .info p {
            color: #555;
            font-size: 13px;
            line-height: 1.6;
        }
        .console {
            background: #1e1e1e;
            color: #00ff00;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 15px;
        }
        .console-line {
            padding: 2px 0;
        }
        .console-line.success { color: #00ff00; }
        .console-line.error { color: #ff6b6b; }
        .console-line.info { color: #4dabf7; }
        .console-line.warn { color: #ffd43b; }
        .instructions {
            background: #e7f3ff;
            border-left: 4px solid #4dabf7;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .instructions h3 {
            color: #0066cc;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .instructions ol {
            margin-left: 20px;
            color: #333;
            font-size: 13px;
            line-height: 1.6;
        }
        .instructions li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔊 Test Audio System</h1>
        <p class="subtitle">Test broadcast antrian dengan trigger audio di display</p>

        <div class="instructions">
            <h3>📋 Cara Penggunaan:</h3>
            <ol>
                <li>Buka halaman display di tab/monitor lain: <code>http://localhost:8000/display</code></li>
                <li>Masukkan kode antrian (contoh: A001, B025)</li>
                <li>Masukkan nama loket (contoh: Loket 1, Loket 2)</li>
                <li>Klik tombol "🔊 Broadcast & Play Audio"</li>
                <li>Dengarkan di display: BEEP + voice Indonesia</li>
            </ol>
        </div>

        <div class="section">
            <h2>📡 Broadcast Test</h2>
            <input type="text" id="kodeAntrian" placeholder="Kode Antrian (contoh: A001)" value="A001">
            <input type="text" id="namaLoket" placeholder="Nama Loket (contoh: Loket 1)" value="Loket 1">
            <button onclick="broadcastAudio()">🔊 Broadcast & Play Audio</button>
            <button class="btn-secondary" onclick="testLocalAudio()">🎵 Test Audio Lokal</button>
        </div>

        <div class="section">
            <h2>🧪 Test Web Speech API</h2>
            <input type="text" id="testText" placeholder="Text untuk dibaca" value="Nomor antrian A satu satu lima, dimohon menuju loket satu">
            <select id="language" style="margin-bottom: 10px;">
                <option value="id-ID">🇮🇩 Bahasa Indonesia</option>
                <option value="en-US">🇺🇸 English (USA)</option>
                <option value="en-GB">🇬🇧 English (UK)</option>
                <option value="es-ES">🇪🇸 Español</option>
            </select>
            <button onclick="testWebSpeech()">🗣️ Test Web Speech</button>
        </div>

        <div class="section">
            <h2>📊 Console Output</h2>
            <div class="console" id="console">
                <div class="console-line info">[INIT] Console ready...</div>
            </div>
        </div>

        <div class="info">
            <h3>ℹ️ Info Sistem</h3>
            <p>
                <strong>Status Audio:</strong> <span id="audioStatus">Checking...</span><br>
                <strong>Browser:</strong> <span id="browserInfo">-</span><br>
                <strong>Web Speech Support:</strong> <span id="webSpeechSupport">Checking...</span>
            </p>
        </div>
    </div>

    <!-- Import bootstrap.js untuk akses Echo -->
    @vite(['resources/js/bootstrap.js'])

    <script>
        const consoleEl = document.getElementById('console');

        function logConsole(message, type = 'info') {
            const line = document.createElement('div');
            line.className = `console-line ${type}`;
            line.textContent = message;
            consoleEl.appendChild(line);
            consoleEl.scrollTop = consoleEl.scrollHeight;
        }

        function logInfo(msg) { logConsole(`[INFO] ${msg}`, 'info'); }
        function logSuccess(msg) { logConsole(`[SUCCESS] ${msg}`, 'success'); }
        function logError(msg) { logConsole(`[ERROR] ${msg}`, 'error'); }
        function logWarn(msg) { logConsole(`[WARN] ${msg}`, 'warn'); }

        // Initialize
        window.addEventListener('load', () => {
            logInfo('Page loaded');
            logInfo(`Browser: ${navigator.userAgent.substring(0, 50)}...`);
            document.getElementById('browserInfo').textContent = navigator.userAgent.substring(0, 40) + '...';
            
            if (window.speechSynthesis) {
                logSuccess('Web Speech API tersedia');
                document.getElementById('webSpeechSupport').textContent = '✅ Yes';
            } else {
                logError('Web Speech API tidak tersedia');
                document.getElementById('webSpeechSupport').textContent = '❌ No';
            }
        });

        // Beep sound
        function playBeep() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

                oscillator.frequency.value = 600;
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);

                logSuccess('Beep dimainkan');
            } catch (e) {
                logError(`Beep error: ${e.message}`);
            }
        }

        // Web Speech
        function testWebSpeech() {
            const text = document.getElementById('testText').value;
            const lang = document.getElementById('language').value;

            if (!window.speechSynthesis) {
                logError('Web Speech API tidak tersedia di browser ini');
                return;
            }

            try {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = lang;
                utterance.rate = 0.8;
                utterance.pitch = 0.95;
                utterance.volume = 0.8;

                utterance.onstart = () => logInfo(`🗣️ Speaking in ${lang}...`);
                utterance.onend = () => logSuccess('Web Speech completed');
                utterance.onerror = (e) => logError(`Web Speech error: ${e.error}`);

                window.speechSynthesis.speak(utterance);
                logInfo(`Text: "${text}"`);
            } catch (e) {
                logError(`Web Speech error: ${e.message}`);
            }
        }

        // Test audio lokal
        function testLocalAudio() {
            logInfo('Testing local audio system...');
            playBeep();

            setTimeout(() => {
                logInfo('Starting Web Speech in 1 second...');
                setTimeout(() => {
                    testWebSpeech();
                }, 1000);
            }, 600);
        }

        // Broadcast
        function broadcastAudio() {
            const kode = document.getElementById('kodeAntrian').value.trim();
            const loket = document.getElementById('namaLoket').value.trim();

            if (!kode) {
                logError('Kode antrian harus diisi');
                return;
            }
            if (!loket) {
                logError('Nama loket harus diisi');
                return;
            }

            logInfo(`🚀 Broadcasting: ${kode} → ${loket}`);

            fetch('{{ route("test.broadcast.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    kode_antrian: kode,
                    nama_loket: loket,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    logSuccess(`Broadcast sent! Event name: ${data.event}`);
                    logInfo('Dengarkan di halaman display...');
                    
                    // Also play local audio
                    setTimeout(() => {
                        logInfo('Playing local audio test...');
                        testLocalAudio();
                    }, 500);
                } else {
                    logError(`Broadcast failed: ${data.message}`);
                }
            })
            .catch(error => {
                logError(`Request error: ${error.message}`);
            });
        }

        logSuccess('Test page initialized');
    </script>
</body>
</html>
