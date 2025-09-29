<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reverb Test Page</title>
    <!-- Memuat Vite untuk mengambil file JS yang sudah dikompilasi -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body style="font-family: sans-serif; padding: 2rem;">

    <h1>Reverb Test Page</h1>
    <p>Buka Developer Console (F12) untuk melihat log dari WebSocket.</p>
    <p>Status: <strong id="status">Menunggu koneksi...</strong></p>
    <div id="log"
        style="background: #f4f4f4; border: 1px solid #ddd; padding: 1rem; margin-top: 1rem; height: 300px; overflow-y: scroll;">
    </div>

    <script type="module">
        // Pastikan script ini berjalan setelah bootstrap.js dimuat oleh Vite

        const channelId = 'camera-status-dnCkZDGx1i32S5JZ';
        const statusEl = document.getElementById('status');
        const logEl = document.getElementById('log');

        function addLog(message, type = 'info') {
            const p = document.createElement('p');
            p.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            p.style.color = type === 'error' ? 'red' : 'green';
            logEl.appendChild(p);
            logEl.scrollTop = logEl.scrollHeight;
        }

        // Menunggu Echo siap
        setTimeout(() => {
            if (window.Echo) {
                statusEl.textContent = 'Menghubungkan ke channel: ' + channelId;
                addLog('Echo siap. Mencoba terhubung...');

                window.Echo.channel(channelId)
                    .listen('.camera.online', (event) => {
                        statusEl.textContent = 'Event Diterima!';
                        statusEl.style.color = 'blue';

                        console.log('Event "camera.online" diterima:', event);
                        addLog('BERHASIL! Event "camera.online" diterima.');
                        addLog('Data: ' + JSON.stringify(event));
                    });

                addLog('Berhasil subscribe ke channel: ' + channelId);
                statusEl.textContent = 'Terhubung & Mendengarkan event .camera.online';

            } else {
                statusEl.textContent = 'Error: Laravel Echo tidak ditemukan.';
                statusEl.style.color = 'red';
                addLog('Gagal: window.Echo tidak terdefinisi.', 'error');
            }
        }, 1000); // Beri waktu 1 detik agar bootstrap.js selesai dimuat
    </script>
</body>

</html>
