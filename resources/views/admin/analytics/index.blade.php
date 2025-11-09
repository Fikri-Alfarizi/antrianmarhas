@extends('layouts.app')

@section('content')
<div style="padding: 20px; background: #f5f5f5; min-height: 100vh;">
    <style>
        .analytics-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .analytics-header h1 {
            font-size: 32px;
            color: #333;
            margin: 0;
            flex: 1;
        }
        
        .analytics-header button, .analytics-header select {
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .analytics-header button:hover {
            background: #2980b9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #888;
            font-size: 13px;
            text-transform: uppercase;
            margin: 0 0 10px 0;
            letter-spacing: 1px;
        }
        
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #3498db;
        }
        
        .stat-card.success .number { color: #27ae60; }
        .stat-card.danger .number { color: #e74c3c; }
        .stat-card.info .number { color: #9b59b6; }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .chart-container h3 {
            color: #333;
            margin: 0 0 20px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-container canvas {
            max-height: 400px;
        }
        
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        
        .table-container h3 {
            color: #333;
            margin: 0 0 20px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-container th {
            background: #ecf0f1;
            padding: 12px;
            text-align: left;
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid #bdc3c7;
        }
        
        .table-container td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .table-container tr:hover {
            background: #f9f9f9;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        
        .date-filters {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .date-filters input {
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
    
    <div class="analytics-header">
        <h1><i class="fas fa-chart-line" style="color: #3498db; margin-right: 10px;"></i> Analytics & Reports</h1>
        <button onclick="exportReport()">
            <i class="fas fa-download"></i> Export Report
        </button>
    </div>
    
    <!-- Daily Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3><i class="fas fa-clipboard-list"></i> Total Antrian Hari Ini</h3>
            <div class="number">{{ $dailyStats['total_antrian'] }}</div>
        </div>
        
        <div class="stat-card success">
            <h3><i class="fas fa-check-circle"></i> Selesai</h3>
            <div class="number">{{ $dailyStats['selesai'] }}</div>
        </div>
        
        <div class="stat-card danger">
            <h3><i class="fas fa-times-circle"></i> Batal</h3>
            <div class="number">{{ $dailyStats['batal'] }}</div>
        </div>
        
        <div class="stat-card info">
            <h3><i class="fas fa-percentage"></i> Completion Rate</h3>
            <div class="number">{{ $dailyStats['completion_rate'] }}%</div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-container">
            <h3><i class="fas fa-clock"></i> Peak Hours</h3>
            <canvas id="hourlyChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h3><i class="fas fa-calendar"></i> Weekly Trend</h3>
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>
    
    <!-- Service Stats -->
    <div class="table-container">
        <h3><i class="fas fa-server"></i> Service Performance</h3>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Total</th>
                    <th>Completed</th>
                    <th>Cancelled</th>
                    <th>Avg Service Time (min)</th>
                </tr>
            </thead>
            <tbody id="serviceTableBody">
                @foreach($serviceStats as $service)
                <tr>
                    <td><strong>{{ $service['nama_layanan'] }}</strong></td>
                    <td>{{ $service['total'] }}</td>
                    <td><span class="badge badge-success">{{ $service['selesai'] }}</span></td>
                    <td><span class="badge badge-danger">{{ $service['batal'] }}</span></td>
                    <td>{{ $service['avg_service_time'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Loket Stats -->
    <div class="table-container" style="margin-top: 20px;">
        <h3><i class="fas fa-door-open"></i> Counter Performance</h3>
        <table>
            <thead>
                <tr>
                    <th>Counter</th>
                    <th>Service</th>
                    <th>Total</th>
                    <th>Completed</th>
                    <th>Avg Service Time (min)</th>
                    <th>Efficiency</th>
                </tr>
            </thead>
            <tbody id="loketTableBody">
                @foreach($loketStats as $loket)
                <tr>
                    <td><strong>{{ $loket['nama_loket'] }}</strong></td>
                    <td>{{ $loket['layanan'] }}</td>
                    <td>{{ $loket['total'] }}</td>
                    <td><span class="badge badge-success">{{ $loket['selesai'] }}</span></td>
                    <td>{{ $loket['avg_service_time'] }}</td>
                    <td>
                        <span class="badge" style="background: {{ $loket['efficiency'] >= 80 ? '#d4edda' : '#fff3cd' }}; color: {{ $loket['efficiency'] >= 80 ? '#155724' : '#856404' }};">
                            {{ $loket['efficiency'] }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Top Services -->
    <div class="charts-grid" style="margin-top: 20px;">
        <div class="chart-container">
            <h3><i class="fas fa-fire"></i> Top Services</h3>
            <canvas id="topServicesChart"></canvas>
        </div>
        
        <div class="table-container">
            <h3><i class="fas fa-list"></i> Most Visited Services</h3>
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topServices as $service)
                    <tr>
                        <td>{{ $service['layanan'] }}</td>
                        <td><strong>{{ $service['count'] }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Month Stats -->
    <div class="stats-grid" style="margin-top: 20px;">
        <div class="stat-card">
            <h3><i class="fas fa-calendar-alt"></i> Month: {{ $monthStats['month'] }}</h3>
            <div class="number">{{ $monthStats['total_antrian'] }}</div>
        </div>
        
        <div class="stat-card success">
            <h3><i class="fas fa-check-circle"></i> Month Completed</h3>
            <div class="number">{{ $monthStats['total_selesai'] }}</div>
        </div>
        
        <div class="stat-card info">
            <h3><i class="fas fa-average"></i> Avg per Day</h3>
            <div class="number">{{ $monthStats['avg_per_day'] }}</div>
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-percentage"></i> Month Rate</h3>
            <div class="number">{{ $monthStats['completion_rate'] }}%</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Hourly Chart
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    const hourlyData = {!! json_encode($hourlyData) !!};
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: Array.from({length: 24}, (_, i) => i + ':00'),
            datasets: [{
                label: 'Queue Count',
                data: hourlyData,
                backgroundColor: '#3498db',
                borderColor: '#2980b9',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    
    // Weekly Chart
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    const weeklyData = {!! json_encode($weeklyData) !!};
    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: weeklyData.map(d => d.date),
            datasets: [{
                label: 'Daily Queue Count',
                data: weeklyData.map(d => d.count),
                borderColor: '#27ae60',
                backgroundColor: 'rgba(39, 174, 96, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    
    // Top Services Chart
    const topServicesCtx = document.getElementById('topServicesChart').getContext('2d');
    const topServices = {!! json_encode($topServices) !!};
    new Chart(topServicesCtx, {
        type: 'doughnut',
        data: {
            labels: topServices.map(s => s.layanan),
            datasets: [{
                data: topServices.map(s => s.count),
                backgroundColor: [
                    '#3498db',
                    '#27ae60',
                    '#f39c12',
                    '#e74c3c',
                    '#9b59b6'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    
    // Export function
    function exportReport() {
        const startDate = prompt('Start Date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
        if (!startDate) return;
        
        const endDate = prompt('End Date (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
        if (!endDate) return;
        
        window.location.href = `{{ route('admin.analytics.export') }}?start_date=${startDate}&end_date=${endDate}`;
    }
</script>
@endsection
