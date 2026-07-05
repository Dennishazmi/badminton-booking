@extends('layouts.admin')

@section('title', 'QR Scanner — Admin')

@section('content')

<div class="mb-5">
    <div style="font-family:var(--font-display); font-size:40px; letter-spacing:1px; line-height:1;">QR SCANNER</div>
    <div style="color:var(--text-muted); font-size:14px; margin-top:6px;">Scan player QR codes for court entry verification</div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-5">

        <!-- Scanner Box -->
        <div class="stat-card text-center">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:var(--primary); margin-bottom:24px;">
                <i class="fas fa-qrcode me-2"></i>Camera Scanner
            </div>

            <!-- Video Feed -->
            <div id="scanner-container" style="position:relative; background:var(--dark-4); border-radius:var(--radius); overflow:hidden; aspect-ratio:1; max-width:320px; margin:0 auto 20px; border:2px solid var(--border);">
                <video id="qr-video" style="width:100%; height:100%; object-fit:cover; display:none;"></video>
                <canvas id="qr-canvas" style="display:none;"></canvas>

                <!-- Overlay when not scanning -->
                <div id="scan-placeholder" style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
                    <i class="fas fa-qrcode" style="font-size:64px; color:rgba(232,255,0,0.2);"></i>
                    <div style="font-size:14px; color:var(--text-muted);">Camera preview will appear here</div>
                </div>

                <!-- Scanning Animation Overlay -->
                <div id="scan-overlay" style="position:absolute; inset:0; display:none; pointer-events:none;">
                    <div style="position:absolute; inset:20px; border:2px solid rgba(232,255,0,0.5); border-radius:8px;">
                        <div id="scan-line" style="position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg, transparent, var(--primary), transparent); animation:scanLine 2s linear infinite;"></div>
                    </div>
                    <!-- Corner marks -->
                    <div style="position:absolute; top:14px; left:14px; width:24px; height:24px; border-top:3px solid var(--primary); border-left:3px solid var(--primary); border-radius:3px 0 0 0;"></div>
                    <div style="position:absolute; top:14px; right:14px; width:24px; height:24px; border-top:3px solid var(--primary); border-right:3px solid var(--primary); border-radius:0 3px 0 0;"></div>
                    <div style="position:absolute; bottom:14px; left:14px; width:24px; height:24px; border-bottom:3px solid var(--primary); border-left:3px solid var(--primary); border-radius:0 0 0 3px;"></div>
                    <div style="position:absolute; bottom:14px; right:14px; width:24px; height:24px; border-bottom:3px solid var(--primary); border-right:3px solid var(--primary); border-radius:0 0 3px 0;"></div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-center mb-4">
                <button id="start-scan" class="btn-primary-sm" style="padding:12px 24px;">
                    <i class="fas fa-camera"></i> Start Camera
                </button>
                <button id="stop-scan" class="btn-outline-sm" style="padding:12px 24px; display:none;">
                    <i class="fas fa-stop"></i> Stop
                </button>
            </div>

            <hr class="separator">

            <!-- Manual Token Entry -->
            <div>
                <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:14px;">Or enter token manually</div>
                <div style="display:flex; gap:8px;">
                    <input type="text" id="manual-token" class="form-control" placeholder="Booking token (e.g. AB12CD34EF)"
                        style="text-transform:uppercase; letter-spacing:2px; font-weight:700; text-align:center; font-size:16px;">
                    <button onclick="verifyToken(document.getElementById('manual-token').value)" class="btn-primary-sm" style="padding:12px 16px; flex-shrink:0;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Panel -->
    <div class="col-lg-5">
        <div class="stat-card" style="min-height:400px;">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:var(--primary); margin-bottom:24px;">
                <i class="fas fa-clipboard-check me-2"></i>Verification Result
            </div>

            <!-- Idle State -->
            <div id="result-idle" style="text-align:center; padding:60px 20px;">
                <div style="width:72px; height:72px; background:var(--dark-4); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px; color:var(--text-muted);">
                    <i class="fas fa-search"></i>
                </div>
                <div style="font-size:16px; font-weight:700; color:var(--text-muted);">Awaiting scan</div>
                <div style="font-size:13px; color:var(--text-muted); margin-top:8px;">Start the camera or enter a token to verify a booking</div>
            </div>

            <!-- Loading State -->
            <div id="result-loading" style="text-align:center; padding:60px 20px; display:none;">
                <div style="width:72px; height:72px; background:rgba(232,255,0,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px; color:var(--primary); animation:pulse 1s infinite;">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div style="font-size:16px; font-weight:700;">Verifying...</div>
            </div>

            <!-- Success State -->
            <div id="result-success" style="display:none;">
                <div style="text-align:center; padding:24px; background:rgba(0,214,143,0.08); border:1px solid rgba(0,214,143,0.2); border-radius:var(--radius); margin-bottom:20px;">
                    <div style="font-size:52px; color:var(--success); margin-bottom:8px;"><i class="fas fa-check-circle"></i></div>
                    <div style="font-family:var(--font-display); font-size:28px; color:var(--success);">ACCESS GRANTED</div>
                    <div id="success-msg" style="font-size:14px; color:var(--text-muted); margin-top:6px;"></div>
                </div>
                <div id="booking-details" class="d-flex flex-column gap-2"></div>
            </div>

            <!-- Error State -->
            <div id="result-error" style="display:none;">
                <div style="text-align:center; padding:24px; background:rgba(255,61,87,0.08); border:1px solid rgba(255,61,87,0.2); border-radius:var(--radius); margin-bottom:16px;">
                    <div style="font-size:52px; color:var(--danger); margin-bottom:8px;"><i class="fas fa-times-circle"></i></div>
                    <div style="font-family:var(--font-display); font-size:28px; color:var(--danger);">ACCESS DENIED</div>
                    <div id="error-msg" style="font-size:14px; color:var(--text-muted); margin-top:6px;"></div>
                </div>
                <div id="error-booking-details" class="d-flex flex-column gap-2"></div>
            </div>

            <!-- Reset Button -->
            <div id="result-reset" style="display:none; margin-top:20px;">
                <button onclick="resetScanner()" class="btn-outline-sm w-100 justify-content-center" style="padding:12px;">
                    <i class="fas fa-redo me-2"></i> Scan Another
                </button>
            </div>
        </div>

        <!-- Recent Scans Log -->
        <div class="stat-card mt-4">
            <div style="font-size:14px; font-weight:700; margin-bottom:16px;">
                <i class="fas fa-history me-2" style="color:var(--primary);"></i>Scan History
            </div>
            <div id="scan-log" style="max-height:240px; overflow-y:auto;">
                <div style="font-size:13px; color:var(--text-muted); text-align:center; padding:20px;">No scans yet this session</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
@keyframes scanLine {
    0%   { top: 0; }
    50%  { top: calc(100% - 2px); }
    100% { top: 0; }
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.5; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsQR/1.4.0/jsQR.min.js"></script>
<script>
let videoStream = null;
let scanInterval = null;
let scanning = false;
const scanLog = [];

// ── Camera scanning ─────────────────────────
document.getElementById('start-scan').addEventListener('click', startCamera);
document.getElementById('stop-scan').addEventListener('click', stopCamera);

async function startCamera() {
    try {
        videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        const video = document.getElementById('qr-video');
        video.srcObject = videoStream;
        video.style.display = 'block';
        await video.play();

        document.getElementById('scan-placeholder').style.display = 'none';
        document.getElementById('scan-overlay').style.display = 'block';
        document.getElementById('start-scan').style.display = 'none';
        document.getElementById('stop-scan').style.display = 'inline-flex';

        scanning = true;
        scanInterval = setInterval(scanFrame, 250);
    } catch (err) {
        alert('Camera access denied or not available. Please use manual token entry.');
    }
}

function stopCamera() {
    scanning = false;
    clearInterval(scanInterval);
    if (videoStream) videoStream.getTracks().forEach(t => t.stop());

    document.getElementById('qr-video').style.display = 'none';
    document.getElementById('scan-placeholder').style.display = 'flex';
    document.getElementById('scan-overlay').style.display = 'none';
    document.getElementById('start-scan').style.display = 'inline-flex';
    document.getElementById('stop-scan').style.display = 'none';
}

function scanFrame() {
    if (!scanning) return;
    const video  = document.getElementById('qr-video');
    const canvas = document.getElementById('qr-canvas');
    if (video.readyState !== video.HAVE_ENOUGH_DATA) return;

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });

    if (code) {
        scanning = false;
        clearInterval(scanInterval);
        // Extract token from URL if full URL, else use raw
        let token = code.data;
        const match = token.match(/\/qr-verify\/([A-Z0-9]+)/i);
        if (match) token = match[1];
        verifyToken(token.toUpperCase());
    }
}

// ── Verify token via API ─────────────────────
async function verifyToken(token) {
    if (!token || token.length < 5) return;
    token = token.trim().toUpperCase();

    showState('loading');

    try {
        const res  = await fetch(`{{ url('admin/qr-verify') }}/${token}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.valid) {
            showSuccess(data);
            addToLog(token, true, data.booking?.player ?? '—');
        } else {
            showError(data);
            addToLog(token, false, data.message);
        }
    } catch (e) {
        showError({ message: 'Network error. Please try again.' });
    }
}

function showState(state) {
    ['idle','loading','success','error'].forEach(s => {
        document.getElementById('result-' + s).style.display = 'none';
    });
    document.getElementById('result-' + state).style.display = 'block';
    document.getElementById('result-reset').style.display = state !== 'idle' && state !== 'loading' ? 'block' : 'none';
}

function showSuccess(data) {
    showState('success');
    document.getElementById('success-msg').textContent = data.message;
    const d = data.booking;
    if (d) renderDetails('booking-details', d, true);
}

function showError(data) {
    showState('error');
    document.getElementById('error-msg').textContent = data.message;
    const d = data.booking;
    if (d) renderDetails('error-booking-details', d, false);
}

function renderDetails(containerId, d, success) {
    const container = document.getElementById(containerId);
    const fields = [
        ['Player', d.player], ['Email', d.email], ['Court', d.court],
        ['Date', d.date], ['Time', d.time], ['Amount', d.amount],
        ['Scanned At', d.scanned_at ?? 'Just now'],
    ];
    container.innerHTML = fields.map(([label, value]) => `
        <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border); font-size:13px;">
            <span style="color:var(--text-muted);">${label}</span>
            <span style="font-weight:600;">${value || '—'}</span>
        </div>
    `).join('');
}

function addToLog(token, success, detail) {
    const now = new Date().toLocaleTimeString();
    scanLog.unshift({ token, success, detail, time: now });
    const logEl = document.getElementById('scan-log');
    logEl.innerHTML = scanLog.slice(0, 20).map(entry => `
        <div style="display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid var(--border); font-size:13px;">
            <i class="fas fa-${entry.success ? 'check-circle' : 'times-circle'}"
               style="color:${entry.success ? 'var(--success)' : 'var(--danger)'}; font-size:16px; flex-shrink:0;"></i>
            <div style="flex:1;">
                <div style="font-weight:700; font-family:monospace; font-size:12px; color:var(--primary);">${entry.token}</div>
                <div style="color:var(--text-muted); font-size:11px;">${entry.detail}</div>
            </div>
            <div style="color:var(--text-muted); font-size:11px; flex-shrink:0;">${entry.time}</div>
        </div>
    `).join('');
}

function resetScanner() {
    showState('idle');
    document.getElementById('manual-token').value = '';
    if (!scanning && videoStream) {
        scanning = true;
        scanInterval = setInterval(scanFrame, 250);
    }
}

// Enter key on manual input
document.getElementById('manual-token').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') verifyToken(this.value);
});
</script>
@endpush
