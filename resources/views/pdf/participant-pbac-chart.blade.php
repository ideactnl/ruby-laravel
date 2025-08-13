<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PBAC Chart PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            text-align: center;
            margin: 20px;
        }
        img {
            width: 100%;
            max-width: 650px;
        }
    </style>
</head>
<body>
    <h2>PBAC Chart Export</h2>
    <p>Preset: {{ ucfirst($preset) }}</p>

    @if($preset === 'custom')
        <p>From: {{ $startDate }} To: {{ $endDate }}</p>
    @endif

    <img src="{{ $imageBase64 }}" alt="PBAC Chart">
</body>
</html>
