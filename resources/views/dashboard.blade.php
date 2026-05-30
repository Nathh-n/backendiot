<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Kursi Roda</title>
    <style>
        body { background-color: #1a202c; color: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: center; padding-top: 50px; }
        .card { background-color: #2d3748; padding: 40px; border-radius: 15px; width: 300px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        h1 { color: #63b3ed; font-size: 24px; }
        .bpm-display { font-size: 80px; font-weight: bold; color: #fc8181; margin: 20px 0; }
        .status { font-size: 14px; color: #a0aec0; }
        .heartbeat { display: inline-block; animation: pump 1s infinite; color: #fc8181;}
        @keyframes pump { 0% { transform: scale(1); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
    </style>
</head>
<body>

    <div class="card">
        <h1>SYS.CYBER-COM</h1>
        <p>Status Sensor: <span id="status-text" style="color:#68d391;">Menunggu Data...</span></p>
        
        <div class="bpm-display">
            <span id="bpm-value">--</span> 
            <span style="font-size: 20px;">BPM</span>
        </div>
        
        <p class="status"><span class="heartbeat">❤</span> Live Monitoring</p>
    </div>

    <script>
        // Fungsi JS untuk mengambil data dari API setiap 1 detik
        setInterval(function() {
            fetch('/api/latest-bpm')
                .then(response => response.json())
                .then(data => {
                    if(data && data.bpm > 0) {
                        document.getElementById('bpm-value').innerText = data.bpm;
                        document.getElementById('status-text').innerText = "Membaca Jari ✔";
                    } else {
                        document.getElementById('bpm-value').innerText = "--";
                        document.getElementById('status-text').innerText = "Jari Dilepas";
                    }
                })
                .catch(error => console.log("Gagal mengambil data"));
        }, 1000); // 1000 ms = 1 detik
    </script>

</body>
</html>