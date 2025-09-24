<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PBAC Chart Export</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
            line-height: 1.6;
            background: #ffffff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding: 15px 0;
            border-bottom: 3px solid #3b82f6;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .header h1 {
            font-size: 32px;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 8px 0;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .header .subtitle {
            font-size: 18px;
            color: #64748b;
            margin: 0;
            font-weight: 500;
        }
        
        .date-info {
            background: #f1f5f9;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        
        .date-info h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            color: #334155;
            font-weight: bold;
        }
        
        .date-info p {
            margin: 2px 0;
            font-size: 16px;
            color: #64748b;
            font-weight: 500;
        }
        
        .chart-container {
            text-align: center;
            margin: 10px 0;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border: 1px solid #e5e7eb;
        }
        
        .chart-image {
            width: 100%;
            max-width: none;
            height: auto;
            min-height: 400px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
        
        .legend-section {
            margin-top: 25px;
            margin-bottom: 20px;
            padding: 20px 30px;
            background: #ffffff;
            border-radius: 0;
            border: none;
        }
        
        .legend-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px 35px;
            justify-content: flex-start;
            align-items: center;
            max-width: 100%;
            line-height: 1.5;
        }
        
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 0;
            background: transparent;
            border-radius: 0;
            border: none;
            box-shadow: none;
        }
        
        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 0;
            flex-shrink: 0;
            border: none;
        }
        
        .legend-label {
            font-size: 18px;
            color: #374151;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .footer {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
        
        .footer p {
            margin: 8px 0;
            font-weight: 500;
        }
        
        /* Print optimizations */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .chart-image {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
                image-rendering: pixelated;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PBAC Chart Export</h1>
        <div class="subtitle">Ruby NU PBAC Tracking</div>
    </div>

    <div class="date-info">
        <h3>Export Details</h3>
        <p><strong>Preset:</strong> {{ ucfirst($preset) }}</p>
        @if($preset === 'custom')
            <p><strong>Date Range:</strong> {{ $startDate }} to {{ $endDate }}</p>
        @endif
        <p><strong>Generated:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="legend-section">
        <div class="legend-container">
            <div class="legend-item">
                <div class="legend-color" style="background-color: #DC2626;"></div>
                <span class="legend-label">Blood Loss</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #F59E0B;"></div>
                <span class="legend-label">Pain</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #22C55E;"></div>
                <span class="legend-label">Impact</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #10B981;"></div>
                <span class="legend-label">General Health</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #8B5CF6;"></div>
                <span class="legend-label">Mood</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #0EA5E9;"></div>
                <span class="legend-label">Stool/Urine</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #6366F1;"></div>
                <span class="legend-label">Sleep</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #EAB308;"></div>
                <span class="legend-label">Diet</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #FB923C;"></div>
                <span class="legend-label">Exercise</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #F472B6;"></div>
                <span class="legend-label">Sex</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #64748B;"></div>
                <span class="legend-label">Notes</span>
            </div>
        </div>
        <div class="legend-container" style="margin-top: 15px;">
            <div class="legend-item">
                <div class="legend-color" style="background-color: #60a5fa;"></div>
                <span class="legend-label">Trend</span>
            </div>
        </div>
    </div>

    <div class="chart-container">
        <img src="{{ $imageBase64 }}" alt="PBAC Chart" class="chart-image">
    </div>

    <div class="footer">
        <p>This chart represents your PBAC data over the selected time period.</p>
        <p>Each metric is color-coded according to the legend above for easy identification and analysis.</p>
    </div>
</body>
</html>
