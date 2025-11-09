@extends('layouts.app')

@section('content')
<div style="padding: 20px;">
    <style>
        .print-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .print-container h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .search-box input {
            flex: 1;
            min-width: 250px;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .search-box button {
            padding: 12px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .search-box button:hover {
            background: #2980b9;
        }
        
        #searchResults {
            display: none;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            position: absolute;
            width: calc(100% - 30px);
            z-index: 10;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .search-result-item {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .search-result-item:hover {
            background: #f0f0f0;
        }
        
        .search-result-item strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
        }
        
        .search-result-item small {
            color: #888;
        }
        
        .selected-antrian {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        
        .selected-antrian h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        
        .antrian-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-item {
            background: white;
            padding: 10px;
            border-radius: 4px;
        }
        
        .info-item label {
            font-weight: bold;
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 5px;
        }
        
        .info-item span {
            font-size: 16px;
            color: #333;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .action-buttons button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-reprint {
            background: #27ae60;
            color: white;
        }
        
        .btn-reprint:hover {
            background: #229954;
        }
        
        .btn-clear {
            background: #95a5a6;
            color: white;
        }
        
        .btn-clear:hover {
            background: #7f8c8d;
        }
        
        #printArea {
            display: none;
            font-family: 'Courier New', Courier, monospace;
            text-align: center;
            width: 280px;
            font-size: 10pt;
            line-height: 1.4;
            padding: 20px;
            background: white;
        }
        
        #printArea h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
        }
        
        #printArea p {
            margin: 5px 0;
        }
        
        #printArea h1 {
            font-size: 28pt;
            font-weight: bold;
            margin: 10px 0;
        }
        
        #printArea img {
            max-width: 180px;
            margin-top: 10px;
        }
        
        #printArea .footer {
            margin-top: 15px;
            font-style: italic;
            font-size: 9pt;
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            
            #printArea,
            #printArea * {
                visibility: visible;
            }
            
            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .history-table th {
            background: #ecf0f1;
            padding: 12px;
            text-align: left;
            color: #333;
            border-bottom: 2px solid #bdc3c7;
        }
        
        .history-table td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .history-table tr:hover {
            background: #f9f9f9;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            background: #e8f4f8;
            color: #3498db;
        }
        
        .loading {
            display: none;
            text-align: center;
            color: #3498db;
        }
        
        .error-msg {
            color: #e74c3c;
            padding: 10px;
            background: #f8d7da;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        
        .success-msg {
            color: #27ae60;
            padding: 10px;
            background: #d4edda;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
    
    <div class="print-container">
        <h1>
            <i class="fas fa-print" style="color: #3498db;"></i>
            Print Ulang Struk Antrian
        </h1>
        
        <div class="error-msg" id="errorMsg"></div>
        <div class="success-msg" id="successMsg"></div>
        
        <div class="search-box" style="position: relative;">
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Cari nomor antrian (contoh: A0001, A0002...)"
                autocomplete="off"
            >
            <button onclick="searchAntrian()">
                <i class="fas fa-search"></i> Cari
            </button>
            <div id="searchResults"></div>
        </div>
        
        <div id="selectedAntrianDiv" style="display: none;">
            <div class="selected-antrian">
                <h3><i class="fas fa-check-circle"></i> Antrian Ditemukan</h3>
                <div class="antrian-info">
                    <div class="info-item">
                        <label>Nomor Antrian</label>
                        <span id="antrianCode" style="font-size: 24px; font-weight: bold; color: #3498db;">-</span>
                    </div>
                    <div class="info-item">
                        <label>Layanan</label>
                        <span id="antrianLayanan">-</span>
                    </div>
                    <div class="info-item">
                        <label>Status</label>
                        <span id="antrianStatus">-</span>
                    </div>
                    <div class="info-item">
                        <label>Waktu Ambil</label>
                        <span id="antrianWaktu">-</span>
                    </div>
                </div>
                
                <div id="printHistoryDiv" style="background: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #ecf0f1;">
                    <label style="font-weight: bold; color: #888; font-size: 12px; text-transform: uppercase; display: block; margin-bottom: 8px;">
                        <i class="fas fa-history"></i> Riwayat Cetak
                    </label>
                    <div id="printHistoryContent">Memuat...</div>
                </div>
                
                <div class="action-buttons">
                    <button class="btn-reprint" onclick="reprintAntrian()">
                        <i class="fas fa-print"></i> Cetak Ulang
                    </button>
                    <button class="btn-clear" onclick="clearSelection()">
                        <i class="fas fa-times"></i> Hapus Pilihan
                    </button>
                </div>
            </div>
        </div>
        
        <div id="printArea"></div>
        
        <div style="margin-top: 40px; border-top: 2px solid #ecf0f1; padding-top: 30px;">
            <h2><i class="fas fa-history"></i> Riwayat Cetak Terbaru</h2>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Nomor Antrian</th>
                        <th>Layanan</th>
                        <th>Jumlah Cetak</th>
                        <th>Terakhir Dicetak</th>
                        <th>Dicetak Oleh</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    @forelse($printHistories as $history)
                    <tr>
                        <td><strong>{{ $history->kode_antrian }}</strong></td>
                        <td>{{ $history->antrian->layanan->nama_layanan ?? 'N/A' }}</td>
                        <td><span class="badge">{{ $history->print_count }}x</span></td>
                        <td>{{ $history->last_printed_at->format('d-m-Y H:i') }}</td>
                        <td>{{ $history->printed_by ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #888; padding: 20px;">
                            <i class="fas fa-inbox"></i> Belum ada riwayat cetak
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($printHistories->count() > 0)
            <div style="text-align: center; margin-top: 20px;">
                {{ $printHistories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    let selectedAntrianId = null;
    let pengaturanData = null;
    
    function searchAntrian() {
        const query = document.getElementById('searchInput').value.trim();
        
        if (!query || query.length < 2) {
            showError('Minimal 2 karakter');
            return;
        }
        
        fetch(`{{ route('admin.print.search') }}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                const resultsDiv = document.getElementById('searchResults');
                
                if (data.error) {
                    showError(data.error);
                    resultsDiv.style.display = 'none';
                    return;
                }
                
                if (data.length === 0) {
                    showError('Antrian tidak ditemukan');
                    resultsDiv.style.display = 'none';
                    return;
                }
                
                resultsDiv.innerHTML = data.map(item => `
                    <div class="search-result-item" onclick="selectAntrian(${item.id}, '${item.kode_antrian}', '${item.layanan}', '${item.status}', '${item.waktu_ambil}')">
                        <strong>${item.kode_antrian}</strong>
                        <small>${item.layanan} • Status: ${item.status}</small>
                        <small style="display: block; color: #aaa;">${item.waktu_ambil}</small>
                    </div>
                `).join('');
                
                resultsDiv.style.display = 'block';
            })
            .catch(err => {
                showError('Gagal mencari antrian');
                console.error(err);
            });
    }
    
    function selectAntrian(id, code, layanan, status, waktu) {
        selectedAntrianId = id;
        document.getElementById('searchInput').value = code;
        document.getElementById('searchResults').style.display = 'none';
        
        document.getElementById('antrianCode').textContent = code;
        document.getElementById('antrianLayanan').textContent = layanan;
        document.getElementById('antrianStatus').textContent = status;
        document.getElementById('antrianWaktu').textContent = waktu;
        
        document.getElementById('selectedAntrianDiv').style.display = 'block';
        
        // Get print history
        fetch(`{{ route('admin.print.history') }}?antrian_id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('printHistoryContent').innerHTML = 'Belum pernah dicetak';
                } else {
                    document.getElementById('printHistoryContent').innerHTML = `
                        <div style="font-size: 14px;">
                            <div style="margin-bottom: 5px;"><strong>Jumlah Cetak:</strong> ${data.print_count}x</div>
                            <div style="margin-bottom: 5px;"><strong>Pertama Dicetak:</strong> ${data.first_printed_at}</div>
                            <div style="margin-bottom: 5px;"><strong>Terakhir Dicetak:</strong> ${data.last_printed_at}</div>
                            <div><strong>Dicetak Oleh:</strong> ${data.printed_by || 'System'}</div>
                        </div>
                    `;
                }
            })
            .catch(err => {
                document.getElementById('printHistoryContent').innerHTML = 'Gagal memuat riwayat';
            });
    }
    
    function clearSelection() {
        selectedAntrianId = null;
        document.getElementById('searchInput').value = '';
        document.getElementById('selectedAntrianDiv').style.display = 'none';
        document.getElementById('searchResults').style.display = 'none';
        clearMessages();
    }
    
    function reprintAntrian() {
        if (!selectedAntrianId) {
            showError('Pilih antrian terlebih dahulu');
            return;
        }
        
        fetch('{{ route('admin.print.reprint') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ antrian_id: selectedAntrianId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                populatePrintArea(data);
                showSuccess(`Cetak ke-${data.print_count} • Siap dicetak`);
                setTimeout(() => window.print(), 500);
            } else {
                showError(data.message || 'Gagal mencetak');
            }
        })
        .catch(err => {
            showError('Gagal berkomunikasi dengan server');
            console.error(err);
        });
    }
    
    function populatePrintArea(data) {
        const { antrian, layanan, pengaturan, qr_code, print_count } = data;
        
        let html = `
            <h3>${pengaturan.nama_instansi}</h3>
            <p>${pengaturan.alamat}</p>
            <hr>
            <p>Nomor Antrian Anda:</p>
            <h1>${antrian.kode_antrian}</h1>
            <p>Layanan: <strong>${layanan}</strong></p>
        `;
        
        if (qr_code) {
            html += `<img src="${qr_code}" alt="QR Code">`;
        }
        
        html += `
            <hr>
            <p>Tanggal & Waktu: ${new Date(antrian.waktu_ambil).toLocaleString('id-ID')}</p>
            <p class="footer">Cetak ke-${print_count}</p>
        `;
        
        document.getElementById('printArea').innerHTML = html;
    }
    
    function showError(msg) {
        clearMessages();
        const elem = document.getElementById('errorMsg');
        elem.textContent = msg;
        elem.style.display = 'block';
    }
    
    function showSuccess(msg) {
        clearMessages();
        const elem = document.getElementById('successMsg');
        elem.textContent = msg;
        elem.style.display = 'block';
    }
    
    function clearMessages() {
        document.getElementById('errorMsg').style.display = 'none';
        document.getElementById('successMsg').style.display = 'none';
    }
    
    // Search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchAntrian();
        }
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.matches('#searchInput')) {
            document.getElementById('searchResults').style.display = 'none';
        }
    });
</script>
@endsection
