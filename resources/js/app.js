import './bootstrap';

const FACE_API_SOURCES = [
    '/vendor/face-api.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/face-api.js/0.22.2/face-api.min.js',
    'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js',
];

const SCRIPT_LOAD_TIMEOUT_MS = 12000;

function ensureScriptLoaded(src, timeoutMs = SCRIPT_LOAD_TIMEOUT_MS) {
    return new Promise((resolve, reject) => {
        const targetHref = new URL(src, window.location.href).href;
        const existing = Array.from(document.scripts || []).find((s) => s && s.src === targetHref);
        if (existing) {
            if (existing.dataset.loaded === 'true') return resolve();
            existing.addEventListener('load', () => resolve(), { once: true });
            existing.addEventListener('error', (e) => reject(e), { once: true });
            return;
        }
        const script = document.createElement('script');
        script.src = targetHref;
        script.async = true;
        const timeoutId = window.setTimeout(() => {
            try { script.remove(); } catch (e) {}
            reject(new Error(`Script load timeout: ${src}`));
        }, timeoutMs);
        script.addEventListener('load', () => {
            window.clearTimeout(timeoutId);
            script.dataset.loaded = 'true';
            resolve();
        }, { once: true });
        script.addEventListener('error', (e) => {
            window.clearTimeout(timeoutId);
            reject(e);
        }, { once: true });
        document.head.appendChild(script);
    });
}

async function ensureScriptLoadedAny(sources) {
    const list = Array.isArray(sources) ? sources : [];
    let lastErr = null;
    for (const src of list) {
        try {
            await ensureScriptLoaded(src);
            return;
        } catch (e) {
            lastErr = e;
        }
    }
    throw lastErr || new Error('Failed to load script');
}

function initKioskClock() {
    const clockEl = document.getElementById('clock');
    const dateEl = document.getElementById('date');
    if (!clockEl || !dateEl) return;

    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        clockEl.textContent = `${hours}:${minutes}`;
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateEl.textContent = now.toLocaleDateString('id-ID', options).toUpperCase();
    }

    updateClock();
    window.setInterval(updateClock, 1000);
}

function initKiosk() {
    const cfg = window.__KIOSK__;
    if (!cfg) return;

    initKioskClock();

    if (!cfg.activeSession) return;

    const video = document.getElementById('video');
    const capturedImage = document.getElementById('captured_image');
    const statusText = document.getElementById('status-text');
    const statusBadge = document.getElementById('status-badge');
    const detectedNameInput = document.getElementById('detected_name');
    const userIdInput = document.getElementById('user_id');
    const submitBtn = document.getElementById('submitBtn');
    const captureBtn = document.getElementById('captureBtn');
    const videoContainer = document.getElementById('videoContainer');
    const successOverlay = document.getElementById('successOverlay');
    const scanInterface = document.getElementById('scanInterface');
    const progressPanel = scanInterface.querySelector('.progress-panel');
    const statusSpinner = document.getElementById('statusSpinner');
    const manualPickBtn = document.getElementById('manualPickBtn');
    const manualModal = document.getElementById('manualModal');
    const manualModalBackdrop = document.getElementById('manualModalBackdrop');
    const manualCloseBtn = document.getElementById('manualModalClose');
    const manualSearch = document.getElementById('manualSearch');
    const manualList = document.getElementById('manualList');
    const instructionPanel = document.getElementById('instructionPanel');
    const instructionStepLabel = document.getElementById('instructionStepLabel');
    const instructionText = document.getElementById('instructionText');
    const instructionHint = document.getElementById('instructionHint');
    const instructionIcon = document.getElementById('instructionIcon');
    const instructionIconShell = document.getElementById('instructionIconShell');
    const instructionStatusChip = document.getElementById('instructionStatusChip');
    const instructionStatusLabel = document.getElementById('instructionStatusLabel');
    const progressFill = document.getElementById('progressFill');
    const progressPercent = document.getElementById('progressPercent');
    const progressStepText = document.getElementById('progressStepText');
    const progressStageText = document.getElementById('progressStageText');
    const progressStatusText = document.getElementById('progressStatusText');
    const faceStatus = document.getElementById('faceStatus');
    const faceStatusText = document.getElementById('faceStatusText');
    const checklistRoot = document.getElementById('verificationChecklist');

    if (!video || !capturedImage || !scanInterface) return;

    const attendedUserIds = Array.isArray(cfg.attendedUserIds) ? cfg.attendedUserIds.map(String) : [];
    const employees = Array.isArray(cfg.employees) ? cfg.employees : [];
    const MODEL_URL = String(cfg.modelUrl || '');

    const OFFICE_LAT = Number(cfg.officeLat || 0);
    const OFFICE_LNG = Number(cfg.officeLng || 0);
    const MAX_RADIUS = Number(cfg.officeRadius || 100);

    const DETECTION_INTERVAL_MS = 90;
    const VIDEO_INPUT_SIZE = (window.innerWidth && window.innerWidth >= 520) ? 128 : 96;
    const VIDEO_SCORE_THRESHOLD = 0.42;
    const PHOTO_INPUT_SIZE = 128;

    const MATCH_THRESHOLD = 0.55;
    const MAX_ACCEPT_DISTANCE = 0.65;
    const STABLE_FRAMES_REQUIRED = 1;

    const MIN_DETECTION_SCORE = 0.35;
    const LIVENESS_TIMEOUT_MS = 6500;
    const MIN_LIVENESS_DURATION_MS = 140;
    const REQUIRED_BLINKS = 0;
    const BLINK_LOW_THRESHOLD = 0.20;
    const BLINK_HIGH_THRESHOLD = 0.24;
    const BLINK_MIN_CLOSED_FRAMES = 1;
    const BLINK_MIN_OPEN_FRAMES = 1;
    const BLINK_MIN_INTERVAL_MS = 150;
    const BLINK_MAX_CLOSED_FRAMES = 28;
    const EAR_BASELINE_MIN_SAMPLES = 2;
    const EAR_BASELINE_MAX_SAMPLES = 8;
    const EAR_OPEN_MIN = 0.14;
    const EAR_OPEN_MAX = 0.40;
    const EYE_MOVE_ASYM_THRESHOLD = 0.03;
    const EYE_MOVE_HIGH_FRAMES = 2;
    const EYE_MOVE_NEUTRAL_FRAMES = 2;
    const EYE_MOVE_REQUIRED_EVENTS = 999;
    const GAZE_YAW_MAX = 0.20;
    const GAZE_REQUIRED_FRAMES = 0;
    const YAW_TURN_THRESHOLD = 0.12;
    const HEAD_STABLE_FRAMES_REQUIRED = 2;
    const REQUIRE_MOUTH_STEP = false;
    const MOUTH_STEP_PROBABILITY = 0;
    const MOUTH_OPEN_THRESHOLD = 0.38;
    const MOUTH_CLOSE_THRESHOLD = 0.30;
    const MOUTH_MIN_OPEN_FRAMES = 2;
    const MOUTH_MIN_CLOSED_FRAMES = 2;
    const FACE_STABLE_CENTER_NORM_DELTA = 0.14;
    const FACE_STABLE_SIZE_DELTA = 0.18;
    const FACE_MISSING_RESET_FRAMES = 8;
    const UNKNOWN_RESET_FRAMES = 8;
    const INSTRUCTION_HOLD_MS = 900;
    const FACE_DETECTED_HOLD_MS = 450;
    const PITCH_BASELINE_MIN_SAMPLES = 4;
    const PITCH_BASELINE_MAX_SAMPLES = 12;
    const PITCH_MOVE_THRESHOLD = 0.05;
    const VERIFICATION_STEPS_TOTAL = 3;
    const LIVENESS_SEQUENCE_TEMPLATE = ['move'];

    const checklistItems = {
        face: checklistRoot ? checklistRoot.querySelector('[data-check="face"]') : null,
        lighting: checklistRoot ? checklistRoot.querySelector('[data-check="lighting"]') : null,
        left: checklistRoot ? checklistRoot.querySelector('[data-check="left"]') : null,
        right: checklistRoot ? checklistRoot.querySelector('[data-check="right"]') : null,
        up: checklistRoot ? checklistRoot.querySelector('[data-check="up"]') : null,
        down: checklistRoot ? checklistRoot.querySelector('[data-check="down"]') : null,
        verification: checklistRoot ? checklistRoot.querySelector('[data-check="verification"]') : null,
    };

    let stream = null;

    let isFaceSystemReady = false;
    let isProcessing = false;
    let isDetecting = false;

    let labeledFaceDescriptors = [];
    let faceMatcher = null;
    let modelsPromise = null;
    let matcherPromise = null;

    let rafHandle = null;
    let lastDetectionAt = 0;

    let isLivenessVerified = false;
    let isAlreadyAttended = false;
    let candidateEmployee = null;
    let currentMatchLabel = null;
    let stableMatchFrames = 0;
    let livenessStartedAt = null;
    let livenessChallengeStartedAt = null;
    let blinkCount = 0;
    let blinkState = 'open';
    let earClosedFrames = 0;
    let earOpenFrames = 0;
    let lastBlinkAt = 0;
    let requiredBlinks = REQUIRED_BLINKS;
    let eyeMoveEventCount = 0;
    let eyeMoveState = 'neutral';
    let eyeMoveHighFrames = 0;
    let eyeMoveNeutralFrames = 0;
    let gazeFrames = 0;
    let earBaseline = null;
    let earBaselineSum = 0;
    let earBaselineSamples = 0;
    let livenessSequence = [];
    let livenessStepIndex = 0;
    let headStableFrames = 0;
    let mouthState = 'closed';
    let mouthOpenFrames = 0;
    let mouthClosedFrames = 0;
    let missingFaceFrames = 0;
    let unknownFaceFrames = 0;
    let lastShownName = "";
    let recognizedEmployeeId = "";
    let recognizedEmployeeName = "";
    let lastFaceBox = null;
    let lastInstructionShown = "";
    let manualSelectedEmployee = null;
    let lastPrepUiAt = 0;
    let faceDetectedAt = 0;
    let pitchBaseline = null;
    let pitchBaselineSum = 0;
    let pitchBaselineSamples = 0;

    const checklistState = {
        face: 'pending',
        lighting: 'pending',
        left: 'pending',
        right: 'pending',
        up: 'pending',
        down: 'pending',
        verification: 'pending',
    };

    let statusHoldUntil = 0;
    let lastStatusText = "";
    let lastStatusType = "";

    function setFaceStatus(state, text) {
        if (faceStatus) faceStatus.dataset.state = state;
        if (faceStatusText) faceStatusText.textContent = text;
    }

    function setAlreadyAttendedUI(active) {
        if (!scanInterface) return;
        scanInterface.classList.toggle('is-attendance-locked', !!active);
        if (!active) {
            if (progressPanel) progressPanel.removeAttribute('aria-hidden');
            return;
        }
        if (progressFill) progressFill.style.width = '100%';
        if (progressPercent) progressPercent.textContent = '100%';
        if (progressStepText) progressStepText.textContent = 'Sudah absen';
        if (progressStatusText) progressStatusText.textContent = '';
        if (progressPanel) progressPanel.setAttribute('aria-hidden', 'true');
    }

    function getStatusPresentation(type) {
        if (type === 'success') return { label: 'OK', state: 'success' };
        if (type === 'danger') return { label: 'Gagal', state: 'failed' };
        if (type === 'warning') return { label: 'Arah', state: 'instruction' };
        if (type === 'secondary') return { label: 'Scan', state: 'detecting' };
        return { label: 'Scan', state: 'detecting' };
    }

    function setChecklistItemState(key, state) {
        const item = checklistItems[key];
        if (!item) return;
        const icon = item.querySelector('.checklist-icon');
        item.classList.remove('is-pending', 'is-current', 'is-complete', 'is-failed');
        item.classList.add(`is-${state}`);
        if (icon) {
            if (state === 'complete') icon.innerHTML = '<i class="fas fa-check-circle"></i>';
            else if (state === 'current') icon.innerHTML = '<i class="fas fa-circle-dot"></i>';
            else if (state === 'failed') icon.innerHTML = '<i class="fas fa-circle-exclamation"></i>';
            else icon.innerHTML = '<i class="fas fa-circle"></i>';
        }
        checklistState[key] = state;
    }

    function resetChecklist(activeKey = null) {
        Object.keys(checklistState).forEach((key) => {
            setChecklistItemState(key, key === activeKey ? 'current' : 'pending');
        });
    }

    function markChecklistComplete(key) {
        setChecklistItemState(key, 'complete');
    }

    function focusChecklistStep(key) {
        Object.keys(checklistState).forEach((entryKey) => {
            if (checklistState[entryKey] === 'complete') {
                setChecklistItemState(entryKey, 'complete');
                return;
            }
            setChecklistItemState(entryKey, entryKey === key ? 'current' : 'pending');
        });
    }

    function failChecklistStep(key) {
        setChecklistItemState(key, 'failed');
    }

    function getProgressPercent(stepNumber) {
        const clamped = Math.min(VERIFICATION_STEPS_TOTAL, Math.max(1, stepNumber));
        return Math.round((clamped / VERIFICATION_STEPS_TOTAL) * 100);
    }

    function updateProgress(stepNumber, stageText, statusTextValue) {
        const clamped = Math.min(VERIFICATION_STEPS_TOTAL, Math.max(1, stepNumber));
        const percent = getProgressPercent(clamped);
        if (progressFill) progressFill.style.width = `${percent}%`;
        if (progressPercent) progressPercent.textContent = `${percent}%`;
        if (progressStepText) progressStepText.textContent = `Langkah ${clamped} dari ${VERIFICATION_STEPS_TOTAL}`;
        if (progressStageText) progressStageText.textContent = stageText;
        if (progressStatusText) progressStatusText.textContent = statusTextValue;
    }

    function setInstructionPanelState({
        state = 'detecting',
        stepLabel = 'Verifikasi',
        text = 'Posisikan wajah di dalam bingkai.',
        hint = '',
        icon = 'fa-camera',
        progressStep = 1,
        progressLabel = text,
        statusLabel = 'Scan',
    }) {
        const signature = JSON.stringify({ state, stepLabel, text, hint, icon, progressStep, progressLabel, statusLabel });
        if (signature === lastInstructionShown) return;
        lastInstructionShown = signature;

        if (instructionPanel) {
            instructionPanel.classList.remove('is-updating');
            void instructionPanel.offsetWidth;
            instructionPanel.classList.add('is-updating');
            instructionPanel.dataset.state = state;
        }
        if (instructionStatusChip) instructionStatusChip.dataset.state = state;
        if (instructionIconShell) instructionIconShell.dataset.state = state;
        if (instructionStepLabel) instructionStepLabel.textContent = stepLabel;
        if (instructionText) instructionText.textContent = text;
        if (instructionHint) instructionHint.textContent = hint;
        if (instructionStatusLabel) instructionStatusLabel.textContent = statusLabel;
        if (instructionIcon) instructionIcon.className = `fas ${icon}`;
        updateProgress(progressStep, progressLabel, statusLabel);
    }

    function getLivenessPrompt(step) {
        return {
            stepLabel: 'Verifikasi',
            text: 'Gerakkan kepala sedikit.',
            hint: '',
            icon: 'fa-arrows-left-right',
            progressStep: 2,
            progressLabel: 'Gerakkan kepala',
            statusLabel: 'Arah',
            checklistKey: 'left',
        };
    }

    function showDefaultInstruction() {
        setInstructionPanelState({
            state: 'detecting',
            stepLabel: 'Verifikasi',
            text: 'Posisikan wajah di dalam bingkai.',
            hint: '',
            icon: 'fa-camera',
            progressStep: 1,
            progressLabel: 'Deteksi wajah',
            statusLabel: 'Scan',
        });
        focusChecklistStep('face');
    }

    function updateStatus(text, type, holdMs = 0, force = false) {
        const now = Date.now();
        if (!force && now < statusHoldUntil) return;
        if (text === lastStatusText && type === lastStatusType) return;

        if (!statusBadge || !statusText) return;

        if (!text) {
            if (statusSpinner) statusSpinner.classList.add('d-none');
            lastStatusText = text;
            lastStatusType = type;
            statusHoldUntil = holdMs > 0 ? now + holdMs : 0;
            return;
        }

        statusBadge.style.display = "inline-flex";
        statusText.textContent = text;
        const presentation = getStatusPresentation(type);
        statusBadge.dataset.state = presentation.state;

        const shouldSpin = type === 'info' && (text.includes('Memeriksa') || text.includes('Menyiapkan') || text.includes('Memuat'));
        if (statusSpinner) statusSpinner.classList.toggle('d-none', !shouldSpin);

        lastStatusText = text;
        lastStatusType = type;
        statusHoldUntil = holdMs > 0 ? now + holdMs : 0;
    }

    function showInstruction(text) {
        const t = String(text || '').trim();
        if (!t) return hideInstruction();
        setInstructionPanelState({
            state: 'instruction',
            stepLabel: 'Verifikasi',
            text: t,
            hint: '',
            icon: 'fa-user-check',
            progressStep: 2,
            progressLabel: t,
            statusLabel: 'Arah',
        });
    }

    function hideInstruction() {
        showDefaultInstruction();
    }

    function setCaptureReady(ready) {
        if (!captureBtn) return;
        captureBtn.disabled = !ready;
        captureBtn.classList.toggle('is-not-ready', !ready);
        captureBtn.setAttribute('aria-disabled', ready ? 'false' : 'true');
    }

    function setVideoState(state) {
        if (!videoContainer) return;
        videoContainer.classList.toggle('is-detecting', state === 'detecting');
        videoContainer.classList.toggle('is-recognized', state === 'recognized' || state === 'locked');
        videoContainer.classList.toggle('is-verified', state === 'verified');
    }

    function setVideoLoading(loading) {
        if (!videoContainer) return;
        videoContainer.classList.toggle('is-loading', !!loading);
    }

    function playSuccessBeep() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const o = ctx.createOscillator();
            const g = ctx.createGain();
            o.type = 'sine';
            o.frequency.value = 880;
            g.gain.value = 0.0001;
            o.connect(g);
            g.connect(ctx.destination);
            o.start();
            g.gain.exponentialRampToValueAtTime(0.12, ctx.currentTime + 0.02);
            g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.18);
            o.stop(ctx.currentTime + 0.2);
            setTimeout(() => ctx.close(), 350);
        } catch (e) {}
    }

    function deg2rad(deg) { return deg * (Math.PI / 180); }

    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function hashString(input) {
        let hash = 5381;
        for (let i = 0; i < input.length; i++) {
            hash = ((hash << 5) + hash) ^ input.charCodeAt(i);
        }
        return (hash >>> 0).toString(16);
    }

    function getDescriptorsCacheKey() {
        return 'pms_face_descriptors_v3_' + hashString(JSON.stringify(employees.map(e => ({ id: e.id, name: e.name, photo: e.photo }))));
    }

    function ensureModelsLoaded() {
        if (modelsPromise) return modelsPromise;
        modelsPromise = ensureScriptLoadedAny(FACE_API_SOURCES).then(async () => {
            if (!window.faceapi) throw new Error('faceapi missing');
            try {
                if (window.faceapi.tf && typeof window.faceapi.tf.setBackend === 'function') {
                    await window.faceapi.tf.setBackend('webgl');
                    if (typeof window.faceapi.tf.ready === 'function') await window.faceapi.tf.ready();
                }
            } catch (e) {}
            const base = String(MODEL_URL || '').replace(/\/+$/, '');
            const manifests = [
                `${base}/tiny_face_detector_model-weights_manifest.json`,
                `${base}/face_landmark_68_model-weights_manifest.json`,
                `${base}/face_recognition_model-weights_manifest.json`,
            ];
            for (const url of manifests) {
                try {
                    const res = await fetch(url, { cache: 'no-store' });
                    if (!res.ok) throw new Error(`Model not found: ${url} (${res.status})`);
                } catch (e) {
                    throw new Error(`Model load failed. ${String(e && e.message ? e.message : e)}`);
                }
            }
            return Promise.all([
                window.faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                window.faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                window.faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            ]);
        }).then(() => {
            isFaceSystemReady = true;
        }).catch((err) => {
            console.error(err);
            updateStatus("Gagal memuat model", "danger", 1400, true);
            try {
                setFaceStatus('failed', 'Model tidak termuat');
                setInstructionPanelState({
                    state: 'failed',
                    stepLabel: 'Instruksi',
                    text: 'Gagal memuat sistem. Refresh halaman.',
                    hint: '',
                    icon: 'fa-triangle-exclamation',
                    progressStep: 1,
                    progressLabel: 'Deteksi wajah',
                    statusLabel: '',
                });
            } catch (e) {}
        });
        return modelsPromise;
    }

    async function loadLabeledDescriptorsFromCacheOrCompute() {
        const cacheKey = getDescriptorsCacheKey();
        try {
            const cached = localStorage.getItem(cacheKey);
            if (cached) {
                const parsed = JSON.parse(cached);
                return parsed.map(item => new window.faceapi.LabeledFaceDescriptors(
                    item.label,
                    item.descriptors.map(d => new Float32Array(d))
                ));
            }
        } catch (e) {}

        async function computeEmployeeDescriptor(employee, photoDetectorOptions) {
            try {
                const img = await window.faceapi.fetchImage(employee.photo);
                const canvas = document.createElement('canvas');
                const maxSide = 160;
                const ratio = img.width && img.height ? Math.min(1, maxSide / Math.max(img.width, img.height)) : 1;
                canvas.width = Math.max(1, Math.round((img.width || maxSide) * ratio));
                canvas.height = Math.max(1, Math.round((img.height || maxSide) * ratio));
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                const detections = await window.faceapi
                    .detectSingleFace(canvas, photoDetectorOptions)
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (detections && detections.descriptor) {
                    return new window.faceapi.LabeledFaceDescriptors(employee.name, [detections.descriptor]);
                }
            } catch (e) {}
            return null;
        }

        const photoDetectorOptions = new window.faceapi.TinyFaceDetectorOptions({
            inputSize: PHOTO_INPUT_SIZE,
            scoreThreshold: VIDEO_SCORE_THRESHOLD
        });

        const results = [];
        const total = employees.length;
        let nextIndex = 0;
        let doneCount = 0;
        let lastMatcherUpdateAt = 0;
        let lastMatcherCount = 0;
        const cpu = Number(navigator.hardwareConcurrency || 4);
        const concurrency = Math.min(Math.max(2, cpu - 1), Math.min(6, Math.max(1, total)));

        const workers = Array.from({ length: concurrency }, async () => {
            while (nextIndex < total) {
                const i = nextIndex;
                nextIndex += 1;

                const now = Date.now();
                if (now - lastPrepUiAt > 220) {
                    updateStatus(`Menyiapkan data (${doneCount + 1}/${total})`, "info", 0, true);
                    lastPrepUiAt = now;
                }

                const lfd = await computeEmployeeDescriptor(employees[i], photoDetectorOptions);
                doneCount += 1;
                if (lfd) {
                    results.push(lfd);
                    const now2 = Date.now();
                    const shouldUpdate =
                        !faceMatcher ||
                        (results.length - lastMatcherCount >= 8) ||
                        (now2 - lastMatcherUpdateAt >= 650);
                    if (shouldUpdate) {
                        try {
                            faceMatcher = new window.faceapi.FaceMatcher(results.slice(), MATCH_THRESHOLD);
                            lastMatcherUpdateAt = now2;
                            lastMatcherCount = results.length;
                        } catch (e) {}
                    }
                }
            }
        });

        await Promise.all(workers);

        try {
            const toCache = results.map(ld => ({
                label: ld.label,
                descriptors: ld.descriptors.map(d => Array.from(d))
            }));
            localStorage.setItem(cacheKey, JSON.stringify(toCache));
        } catch (e) {}

        if (results.length > 0) {
            try {
                faceMatcher = new window.faceapi.FaceMatcher(results.slice(), MATCH_THRESHOLD);
            } catch (e) {}
            updateStatus("Siap memindai", "info", 500, true);
        } else {
            updateStatus("Data wajah karyawan belum siap", "warning", 1400, true);
        }
        return results;
    }

    function ensureMatcherReady() {
        if (matcherPromise) return matcherPromise;
        matcherPromise = ensureModelsLoaded().then(async () => {
            labeledFaceDescriptors = await loadLabeledDescriptorsFromCacheOrCompute();
            if (labeledFaceDescriptors.length > 0) {
                faceMatcher = new window.faceapi.FaceMatcher(labeledFaceDescriptors, MATCH_THRESHOLD);
            }
        });
        return matcherPromise;
    }

    function openManualModal() {
        if (!manualModal) return;
        manualModal.classList.add('is-open');
        if (manualSearch) manualSearch.focus();
    }

    function closeManualModal() {
        if (!manualModal) return;
        manualModal.classList.remove('is-open');
    }

    function renderManualList(filterText = "") {
        if (!manualList) return;
        const q = String(filterText || "").trim().toLowerCase();
        const filtered = q
            ? employees.filter(e => String(e.name || "").toLowerCase().includes(q))
            : employees.slice();
        manualList.innerHTML = filtered.map(e => (
            `<button type="button" data-emp-id="${String(e.id)}">${String(e.name)}</button>`
        )).join('');
    }

    function resetMatchState() {
        currentMatchLabel = null;
        stableMatchFrames = 0;
        missingFaceFrames = 0;
        unknownFaceFrames = 0;
        lastShownName = "";
        recognizedEmployeeId = "";
        recognizedEmployeeName = "";
        lastFaceBox = null;
        faceDetectedAt = 0;
        pitchBaseline = null;
        pitchBaselineSum = 0;
        pitchBaselineSamples = 0;
        if (!isLivenessVerified) {
            isAlreadyAttended = false;
            candidateEmployee = null;
            manualSelectedEmployee = null;
            if (detectedNameInput) detectedNameInput.value = "";
            if (userIdInput) userIdInput.value = "";
            if (submitBtn) submitBtn.disabled = true;
            initLivenessChallenge();
        }
    }

    function initLivenessChallenge() {
        livenessSequence = LIVENESS_SEQUENCE_TEMPLATE.slice();
        livenessStepIndex = 0;
        headStableFrames = 0;
        requiredBlinks = REQUIRED_BLINKS;
        blinkCount = 0;
        blinkState = 'open';
        earClosedFrames = 0;
        earOpenFrames = 0;
        lastBlinkAt = 0;
        eyeMoveEventCount = 0;
        eyeMoveState = 'neutral';
        eyeMoveHighFrames = 0;
        eyeMoveNeutralFrames = 0;
        gazeFrames = 0;
        earBaseline = null;
        earBaselineSum = 0;
        earBaselineSamples = 0;
        mouthState = 'closed';
        mouthOpenFrames = 0;
        mouthClosedFrames = 0;
        livenessStartedAt = null;
        livenessChallengeStartedAt = Date.now();
        if (successOverlay) successOverlay.classList.remove('is-visible');
        focusChecklistStep('face');
        showDefaultInstruction();
    }

    function averagePoints(points) {
        let x = 0;
        let y = 0;
        for (let i = 0; i < points.length; i++) {
            x += points[i].x;
            y += points[i].y;
        }
        const len = points.length || 1;
        return { x: x / len, y: y / len };
    }

    function distance(a, b) {
        const dx = a.x - b.x;
        const dy = a.y - b.y;
        return Math.sqrt(dx * dx + dy * dy);
    }

    function getEAR(eye) {
        const vertical1 = distance(eye[1], eye[5]);
        const vertical2 = distance(eye[2], eye[4]);
        const horizontal = distance(eye[0], eye[3]);
        if (!horizontal) return 0;
        return (vertical1 + vertical2) / (2 * horizontal);
    }

    function getEyeEARs(landmarks) {
        const leftEye = landmarks.getLeftEye();
        const rightEye = landmarks.getRightEye();
        const leftEAR = getEAR(leftEye);
        const rightEAR = getEAR(rightEye);
        return { leftEAR, rightEAR, avgEAR: (leftEAR + rightEAR) / 2 };
    }

    function getYaw(landmarks) {
        const leftEye = landmarks.getLeftEye();
        const rightEye = landmarks.getRightEye();
        if (!leftEye?.length || !rightEye?.length) return 0;
        const leftCenter = averagePoints(leftEye);
        const rightCenter = averagePoints(rightEye);
        const midEye = { x: (leftCenter.x + rightCenter.x) / 2, y: (leftCenter.y + rightCenter.y) / 2 };
        const interEye = distance(leftCenter, rightCenter);
        const nose = landmarks.getNose();
        const noseTip = nose && nose.length ? nose[Math.min(3, nose.length - 1)] : midEye;
        if (!interEye) return 0;
        return (noseTip.x - midEye.x) / interEye;
    }

    function getPitch(landmarks) {
        const leftEye = landmarks.getLeftEye();
        const rightEye = landmarks.getRightEye();
        const mouth = landmarks.getMouth();
        const nose = landmarks.getNose();
        if (!leftEye?.length || !rightEye?.length || !mouth?.length || !nose?.length) return 0;
        const leftCenter = averagePoints(leftEye);
        const rightCenter = averagePoints(rightEye);
        const eyeMid = { x: (leftCenter.x + rightCenter.x) / 2, y: (leftCenter.y + rightCenter.y) / 2 };
        const mouthCenter = averagePoints(mouth.slice(0, 12));
        const noseTip = nose[Math.min(6, nose.length - 1)] || nose[nose.length - 1];
        const faceHeight = Math.max(1, mouthCenter.y - eyeMid.y);
        return (noseTip.y - eyeMid.y) / faceHeight;
    }

    function getMAR(landmarks) {
        const mouth = landmarks.getMouth();
        if (!mouth?.length || mouth.length < 10) return 0;
        const left = mouth[0];
        const right = mouth[6] || mouth[mouth.length - 1];
        const top = mouth[3];
        const bottom = mouth[9] || mouth[Math.min(9, mouth.length - 1)];
        const horizontal = distance(left, right);
        const vertical = distance(top, bottom);
        if (!horizontal) return 0;
        return vertical / horizontal;
    }

    function updateMouthState(mar, isStableFrame, yaw) {
        if (!isStableFrame) {
            mouthState = 'closed';
            mouthOpenFrames = 0;
            mouthClosedFrames = 0;
            return false;
        }
        if (Math.abs(yaw) > 0.12) {
            mouthState = 'closed';
            mouthOpenFrames = 0;
            mouthClosedFrames = 0;
            return false;
        }
        if (mar >= MOUTH_OPEN_THRESHOLD) {
            mouthOpenFrames += 1;
            mouthClosedFrames = 0;
            if (mouthOpenFrames >= MOUTH_MIN_OPEN_FRAMES) mouthState = 'open';
            return false;
        }
        if (mar <= MOUTH_CLOSE_THRESHOLD) {
            mouthClosedFrames += 1;
            if (mouthState === 'open' && mouthClosedFrames >= MOUTH_MIN_CLOSED_FRAMES) {
                mouthState = 'closed';
                mouthOpenFrames = 0;
                mouthClosedFrames = 0;
                return true;
            }
            if (mouthState !== 'open') mouthOpenFrames = 0;
            return false;
        }
        mouthOpenFrames = 0;
        mouthClosedFrames = 0;
        return false;
    }

    function updateEarBaseline(avgEAR, isStableFrame, yaw) {
        if (!isStableFrame) return;
        if (Math.abs(yaw) > 0.18) return;
        if (avgEAR < EAR_OPEN_MIN || avgEAR > EAR_OPEN_MAX) return;
        if (earBaselineSamples >= EAR_BASELINE_MAX_SAMPLES) return;
        earBaselineSum += avgEAR;
        earBaselineSamples += 1;
        if (earBaselineSamples >= EAR_BASELINE_MIN_SAMPLES) {
            earBaseline = earBaselineSum / earBaselineSamples;
        }
    }

    function updatePitchBaseline(pitch, isStableFrame, yaw) {
        if (!isStableFrame) return;
        if (Math.abs(yaw) > 0.14) return;
        if (pitchBaselineSamples >= PITCH_BASELINE_MAX_SAMPLES) return;
        pitchBaselineSum += pitch;
        pitchBaselineSamples += 1;
        if (pitchBaselineSamples >= PITCH_BASELINE_MIN_SAMPLES) {
            pitchBaseline = pitchBaselineSum / pitchBaselineSamples;
        }
    }

    function measureBrightness() {
        if (!video.videoWidth || !video.videoHeight) return null;
        const canvas = document.createElement('canvas');
        canvas.width = 64;
        canvas.height = 48;
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        if (!ctx) return null;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const image = ctx.getImageData(12, 8, 40, 32).data;
        let total = 0;
        const pixels = image.length / 4;
        if (!pixels) return null;
        for (let i = 0; i < image.length; i += 4) {
            total += (image[i] * 0.299) + (image[i + 1] * 0.587) + (image[i + 2] * 0.114);
        }
        return total / pixels;
    }

    function getBlinkThresholds() {
        if (earBaselineSamples >= EAR_BASELINE_MIN_SAMPLES && earBaseline) {
            const low = Math.max(0.13, Math.min(0.30, earBaseline * 0.78));
            const high = Math.max(low + 0.02, Math.min(0.36, earBaseline * 0.92));
            return { low, high };
        }
        return { low: BLINK_LOW_THRESHOLD, high: BLINK_HIGH_THRESHOLD };
    }

    function updateBlinkState(avgEAR, isStableFrame, yaw, now, lowThreshold, highThreshold) {
        if (!isStableFrame) {
            earClosedFrames = 0;
            earOpenFrames = 0;
            return;
        }
        if (Math.abs(yaw) > 0.22) {
            earClosedFrames = 0;
            earOpenFrames = 0;
            blinkState = 'open';
            return;
        }
        if (avgEAR < lowThreshold) {
            earClosedFrames += 1;
            earOpenFrames = 0;
            blinkState = 'closed';
            if (earClosedFrames > BLINK_MAX_CLOSED_FRAMES) {
                earClosedFrames = 0;
                earOpenFrames = 0;
                blinkState = 'open';
            }
            return;
        }
        if (avgEAR > highThreshold) {
            earOpenFrames += 1;
            if (blinkState === 'closed' && earClosedFrames >= BLINK_MIN_CLOSED_FRAMES && earOpenFrames >= BLINK_MIN_OPEN_FRAMES) {
                if (!lastBlinkAt || now - lastBlinkAt >= BLINK_MIN_INTERVAL_MS) {
                    blinkCount += 1;
                    lastBlinkAt = now;
                }
                earClosedFrames = 0;
                earOpenFrames = 0;
                blinkState = 'open';
            } else if (blinkState === 'open') {
                earClosedFrames = 0;
            }
        }
    }

    function updateEyeMoveState(leftEAR, rightEAR, isStableFrame, yaw) {
        if (!isStableFrame) {
            eyeMoveState = 'neutral';
            eyeMoveHighFrames = 0;
            eyeMoveNeutralFrames = 0;
            return;
        }
        if (Math.abs(yaw) > 0.18) {
            eyeMoveState = 'neutral';
            eyeMoveHighFrames = 0;
            eyeMoveNeutralFrames = 0;
            return;
        }
        const asym = Math.abs(leftEAR - rightEAR);
        const high = asym >= EYE_MOVE_ASYM_THRESHOLD;
        const neutral = asym <= (EYE_MOVE_ASYM_THRESHOLD * 0.55);
        if (eyeMoveState === 'neutral') {
            if (high) eyeMoveHighFrames += 1;
            else eyeMoveHighFrames = 0;
            if (eyeMoveHighFrames >= EYE_MOVE_HIGH_FRAMES) {
                eyeMoveState = 'moved';
                eyeMoveNeutralFrames = 0;
            }
            return;
        }
        if (eyeMoveState === 'moved') {
            if (neutral) eyeMoveNeutralFrames += 1;
            else eyeMoveNeutralFrames = 0;
            if (eyeMoveNeutralFrames >= EYE_MOVE_NEUTRAL_FRAMES) {
                eyeMoveEventCount += 1;
                eyeMoveState = 'neutral';
                eyeMoveHighFrames = 0;
                eyeMoveNeutralFrames = 0;
            }
        }
    }

    function updateGazeState(avgEAR, isStableFrame, yaw, openThreshold) {
        if (!isStableFrame) {
            gazeFrames = 0;
            return;
        }
        if (Math.abs(yaw) > GAZE_YAW_MAX) {
            gazeFrames = 0;
            return;
        }
        const minOpen = Math.max(EAR_OPEN_MIN, openThreshold * 0.85);
        if (avgEAR <= minOpen) {
            gazeFrames = 0;
            return;
        }
        gazeFrames += 1;
    }

    function isFaceStable(box) {
        if (!box) return false;
        if (!lastFaceBox) {
            lastFaceBox = { x: box.x, y: box.y, width: box.width, height: box.height };
            return true;
        }
        const prev = lastFaceBox;
        const prevCx = prev.x + prev.width / 2;
        const prevCy = prev.y + prev.height / 2;
        const cx = box.x + box.width / 2;
        const cy = box.y + box.height / 2;
        const dx = cx - prevCx;
        const dy = cy - prevCy;
        const norm = Math.max(1, Math.max(prev.width, prev.height));
        const centerNormDelta = Math.sqrt(dx * dx + dy * dy) / norm;
        const sizeDelta = Math.abs(box.width - prev.width) / Math.max(1, prev.width);
        lastFaceBox = { x: box.x, y: box.y, width: box.width, height: box.height };
        return centerNormDelta <= FACE_STABLE_CENTER_NORM_DELTA && sizeDelta <= FACE_STABLE_SIZE_DELTA;
    }

    function isFaceCentered(detections) {
        try {
            const box = detections.detection.box;
            const videoWidth = video.videoWidth;
            const videoHeight = video.videoHeight;
            if (!videoWidth || !videoHeight) return false;
            const centerX = videoWidth / 2;
            const centerY = videoHeight / 2;
            const faceCenterX = box.x + box.width / 2;
            const faceCenterY = box.y + box.height / 2;
            const toleranceX = videoWidth * 0.42;
            const toleranceY = videoHeight * 0.42;
            return Math.abs(faceCenterX - centerX) < toleranceX && Math.abs(faceCenterY - centerY) < toleranceY;
        } catch (e) {
            return false;
        }
    }

    function verifyLiveness() {
        if (!candidateEmployee) return;
        isLivenessVerified = true;
        markChecklistComplete('verification');
        if (detectedNameInput) detectedNameInput.value = recognizedEmployeeName || candidateEmployee.name;
        if (userIdInput) userIdInput.value = recognizedEmployeeId || candidateEmployee.id;
        if (submitBtn) submitBtn.disabled = true;
        setCaptureReady(true);
        setFaceStatus('success', 'Wajah terdeteksi');
        updateStatus("Wajah terverifikasi", "success", 900, true);
        setInstructionPanelState({
            state: 'success',
            stepLabel: 'Instruksi',
            text: 'Verifikasi berhasil.',
            hint: '',
            icon: 'fa-circle-check',
            progressStep: 3,
            progressLabel: 'Selesai',
            statusLabel: 'OK',
        });
        setVideoState('verified');
        if (successOverlay) successOverlay.classList.add('is-visible');
        playSuccessBeep();
        stopScanning();
    }

    function updateLiveness(detections) {
        const landmarks = detections.landmarks;
        const now = Date.now();
        if (!livenessStartedAt) livenessStartedAt = now;
        if (livenessStartedAt && now - livenessStartedAt > LIVENESS_TIMEOUT_MS) {
            initLivenessChallenge();
            livenessStartedAt = now;
        }
        if (!livenessSequence.length) initLivenessChallenge();
        const yaw = getYaw(landmarks);
        const stable = isFaceStable(detections.detection?.box) || isFaceCentered(detections);

        markChecklistComplete('face');
        markChecklistComplete('lighting');

        if (!faceDetectedAt) faceDetectedAt = now;
        if (now - faceDetectedAt < FACE_DETECTED_HOLD_MS) {
            setVideoState('recognized');
            setFaceStatus('success', 'Wajah terdeteksi');
            updateStatus("Wajah terdeteksi", "success", 450, true);
            setInstructionPanelState({
                state: 'instruction',
                stepLabel: 'Instruksi',
                text: 'Wajah terdeteksi.',
                hint: '',
                icon: 'fa-face-smile',
                progressStep: 2,
                progressLabel: 'Wajah',
                statusLabel: 'OK',
            });
            return;
        }

        const step = livenessSequence[livenessStepIndex] || 'move';
        const prompt = getLivenessPrompt(step);
        const pitch = getPitch(landmarks);
        updatePitchBaseline(pitch, stable, yaw);
        const pitchDelta = pitchBaseline !== null ? pitch - pitchBaseline : 0;
        const ok =
            Math.abs(yaw) >= YAW_TURN_THRESHOLD ||
            (pitchBaseline !== null && Math.abs(pitchDelta) >= PITCH_MOVE_THRESHOLD);

        setVideoState('detecting');
        focusChecklistStep(prompt.checklistKey);
        setInstructionPanelState({
            state: 'instruction',
            stepLabel: prompt.stepLabel,
            text: prompt.text,
            hint: prompt.hint,
            icon: prompt.icon,
            progressStep: prompt.progressStep,
            progressLabel: prompt.progressLabel,
            statusLabel: prompt.statusLabel,
        });

        if (ok && stable) headStableFrames += 1;
        else headStableFrames = 0;

        if (headStableFrames >= HEAD_STABLE_FRAMES_REQUIRED) {
            markChecklistComplete(prompt.checklistKey);
            livenessStepIndex += 1;
            headStableFrames = 0;
            livenessChallengeStartedAt = now;
        }

        if (livenessStepIndex < livenessSequence.length) return;

        focusChecklistStep('verification');
        setFaceStatus('instruction', 'Memverifikasi identitas');
        setInstructionPanelState({
            state: 'instruction',
            stepLabel: 'Instruksi',
            text: 'Memverifikasi identitas...',
            hint: '',
            icon: 'fa-spinner fa-spin',
            progressStep: 3,
            progressLabel: 'Verifikasi',
            statusLabel: 'Arah',
        });
        if (!livenessChallengeStartedAt) livenessChallengeStartedAt = now;
        if (now - livenessChallengeStartedAt < MIN_LIVENESS_DURATION_MS) {
            setVideoState('detecting');
            return;
        }
        verifyLiveness();
    }

    function startScanning() {
        stopScanning();
        lastDetectionAt = 0;
        rafHandle = requestAnimationFrame(scanLoop);
    }

    function stopScanning() {
        if (rafHandle) cancelAnimationFrame(rafHandle);
        rafHandle = null;
    }

    async function scanLoop(ts) {
        if (!stream) {
            rafHandle = null;
            return;
        }
        if (!lastDetectionAt || ts - lastDetectionAt >= DETECTION_INTERVAL_MS) {
            lastDetectionAt = ts;
            await runDetectionFrame();
        }
        rafHandle = requestAnimationFrame(scanLoop);
    }

    async function detectFaceOnly(videoDetectorOptions) {
        return await window.faceapi.detectSingleFace(video, videoDetectorOptions);
    }

    async function detectFaceWithLandmarks(videoDetectorOptions) {
        return await window.faceapi
            .detectSingleFace(video, videoDetectorOptions)
            .withFaceLandmarks();
    }

    async function detectFaceWithDescriptor(videoDetectorOptions) {
        return await window.faceapi
            .detectSingleFace(video, videoDetectorOptions)
            .withFaceLandmarks()
            .withFaceDescriptor();
    }

    async function runDetectionFrame() {
        if (isProcessing) return;
        if (isDetecting) return;
        if (isLivenessVerified) {
            setCaptureReady(true);
            return;
        }
        if (!isFaceSystemReady) {
            setCaptureReady(false);
            setFaceStatus('detecting', 'Menyiapkan kamera');
            updateStatus("Memuat model wajah...", "info", 0, true);
            setVideoState('detecting');
            setInstructionPanelState({
                state: 'detecting',
                stepLabel: 'Instruksi',
                text: 'Posisikan wajah di dalam bingkai.',
                hint: '',
                icon: 'fa-brain',
                progressStep: 1,
                progressLabel: 'Deteksi wajah',
                statusLabel: 'Scan',
            });
            ensureModelsLoaded();
            return;
        }

        const videoDetectorOptions = new window.faceapi.TinyFaceDetectorOptions({
            inputSize: VIDEO_INPUT_SIZE,
            scoreThreshold: VIDEO_SCORE_THRESHOLD
        });

        isDetecting = true;
        let detections = null;
        try {
            if (manualSelectedEmployee) {
                detections = await detectFaceWithLandmarks(videoDetectorOptions);
            } else if (!faceMatcher) {
                detections = await detectFaceOnly(videoDetectorOptions);
            } else {
                detections = await detectFaceWithDescriptor(videoDetectorOptions);
            }
        } finally {
            isDetecting = false;
        }

        if (!detections) {
            setCaptureReady(false);
            hideInstruction();
            missingFaceFrames += 1;
            if (!isLivenessVerified && candidateEmployee && missingFaceFrames >= 3) initLivenessChallenge();
            if (missingFaceFrames >= FACE_MISSING_RESET_FRAMES) resetMatchState();
            if (!isLivenessVerified) {
                if (detectedNameInput) detectedNameInput.value = "";
                setFaceStatus('detecting', 'Wajah belum terdeteksi');
                updateStatus("Arahkan wajah ke kamera", "info", 900);
                setVideoState('detecting');
                showDefaultInstruction();
            }
            if (manualPickBtn) manualPickBtn.classList.add('d-none');
            return;
        }

        if (!faceMatcher) {
            setCaptureReady(false);
            setVideoState('detecting');
            setFaceStatus('detecting', 'Menyiapkan data wajah');
            updateStatus("Menyiapkan data karyawan...", "info", 0, true);
            setInstructionPanelState({
                state: 'detecting',
                stepLabel: 'Instruksi',
                text: 'Posisikan wajah di dalam bingkai.',
                hint: '',
                icon: 'fa-database',
                progressStep: 1,
                progressLabel: 'Deteksi wajah',
                statusLabel: 'Scan',
            });
            ensureMatcherReady();
            if (manualPickBtn) manualPickBtn.classList.remove('d-none');
            return;
        }

        if (detections.detection && detections.detection.score < MIN_DETECTION_SCORE) {
            setCaptureReady(false);
            setFaceStatus('detecting', 'Posisikan wajah');
            if (!isLivenessVerified && isFaceCentered(detections)) updateStatus("", "info");
            else updateStatus("Posisikan wajah di oval", "warning", INSTRUCTION_HOLD_MS);
            setVideoState('detecting');
            setInstructionPanelState({
                state: 'instruction',
                stepLabel: 'Instruksi',
                text: 'Posisikan wajah di dalam bingkai.',
                hint: '',
                icon: 'fa-camera',
                progressStep: 1,
                progressLabel: 'Deteksi wajah',
                statusLabel: 'Arah',
            });
            return;
        }

        if (manualSelectedEmployee) {
            if (!isFaceCentered(detections)) {
                updateStatus("Posisikan wajah di oval", "warning", 900);
                setVideoState('detecting');
                setFaceStatus('detecting', 'Posisikan wajah');
                setInstructionPanelState({
                    state: 'instruction',
                    stepLabel: 'Instruksi',
                    text: 'Posisikan wajah di dalam bingkai.',
                    hint: '',
                    icon: 'fa-expand',
                    progressStep: 1,
                    progressLabel: 'Deteksi wajah',
                    statusLabel: 'Arah',
                });
                return;
            }
            candidateEmployee = manualSelectedEmployee;
            recognizedEmployeeId = String(manualSelectedEmployee.id);
            recognizedEmployeeName = manualSelectedEmployee.name;
            if (detectedNameInput) detectedNameInput.value = recognizedEmployeeName;
            setFaceStatus('success', 'Wajah terdeteksi');
            if (!faceDetectedAt) faceDetectedAt = Date.now();
            updateLiveness(detections);
            return;
        }

        const result = faceMatcher.findBestMatch(detections.descriptor);
        if (result.label === 'unknown' || result.distance > MATCH_THRESHOLD) {
            setCaptureReady(false);
            unknownFaceFrames += 1;
            if (!isLivenessVerified && candidateEmployee && unknownFaceFrames >= 3) initLivenessChallenge();
            if (unknownFaceFrames >= UNKNOWN_RESET_FRAMES) resetMatchState();
            if (!isLivenessVerified) {
                if (detectedNameInput) detectedNameInput.value = "";
                setFaceStatus('failed', 'Wajah belum dikenali');
                updateStatus("Wajah belum dikenali", "warning", INSTRUCTION_HOLD_MS);
                setVideoState('detecting');
                focusChecklistStep('face');
                setInstructionPanelState({
                    state: 'failed',
                    stepLabel: 'Instruksi',
                    text: 'Wajah belum dikenali.',
                    hint: '',
                    icon: 'fa-user-xmark',
                    progressStep: 1,
                    progressLabel: 'Deteksi wajah',
                    statusLabel: 'Gagal',
                });
            }
            if (manualPickBtn) manualPickBtn.classList.toggle('d-none', unknownFaceFrames < 6);
            return;
        }

        missingFaceFrames = 0;
        unknownFaceFrames = 0;
        if (manualPickBtn) manualPickBtn.classList.add('d-none');

        if (currentMatchLabel === result.label) stableMatchFrames += 1;
        else {
            currentMatchLabel = result.label;
            stableMatchFrames = 1;
        }

        if (stableMatchFrames < STABLE_FRAMES_REQUIRED) {
            if (!isLivenessVerified && isFaceCentered(detections)) updateStatus("", "info");
            else updateStatus("Mendeteksi wajah...", "info", 500);
            setVideoState('detecting');
            return;
        }

        const matchedEmployee = employees.find(e => e.name === result.label);
        if (!matchedEmployee) {
            resetMatchState();
            updateStatus("Wajah belum dikenali", "warning", INSTRUCTION_HOLD_MS);
            return;
        }

        if (!isLivenessVerified && matchedEmployee.name !== lastShownName) {
            if (detectedNameInput) detectedNameInput.value = matchedEmployee.name;
            lastShownName = matchedEmployee.name;
        }

        if (!candidateEmployee || String(candidateEmployee.id) !== String(matchedEmployee.id)) {
            candidateEmployee = matchedEmployee;
            manualSelectedEmployee = null;
            isAlreadyAttended = attendedUserIds.includes(String(matchedEmployee.id));
            initLivenessChallenge();
            recognizedEmployeeId = String(candidateEmployee.id);
            recognizedEmployeeName = candidateEmployee.name;
            faceDetectedAt = Date.now();
        }

        if (isAlreadyAttended) {
            setCaptureReady(false);
            if (submitBtn) submitBtn.disabled = true;
            setAlreadyAttendedUI(true);
            setFaceStatus('failed', 'Sudah absen');
            updateStatus("Sudah absen hari ini", "secondary", INSTRUCTION_HOLD_MS);
            if (detectedNameInput) detectedNameInput.value = recognizedEmployeeName || matchedEmployee.name;
            failChecklistStep('verification');
            setInstructionPanelState({
                state: 'failed',
                stepLabel: 'Instruksi',
                text: 'Anda sudah absen hari ini.',
                hint: '',
                icon: 'fa-ban',
                progressStep: 1,
                progressLabel: 'Sudah absen',
                statusLabel: '',
            });
            stopScanning();
            return;
        }

        setCaptureReady(false);

        if (result.distance > MAX_ACCEPT_DISTANCE) {
            updateStatus("Posisikan wajah di oval", "warning", INSTRUCTION_HOLD_MS);
            setVideoState('detecting');
        }

        if (!isFaceCentered(detections)) {
            initLivenessChallenge();
            updateStatus("Posisikan wajah di oval", "warning", INSTRUCTION_HOLD_MS);
            setVideoState('detecting');
            setFaceStatus('detecting', 'Posisikan wajah');
            setInstructionPanelState({
                state: 'instruction',
                stepLabel: 'Instruksi',
                text: 'Posisikan wajah di dalam bingkai.',
                hint: '',
                icon: 'fa-expand',
                progressStep: 1,
                progressLabel: 'Deteksi wajah',
                statusLabel: 'Arah',
            });
            if (detectedNameInput) detectedNameInput.value = `${recognizedEmployeeName || matchedEmployee.name} • Posisikan di oval`;
            return;
        }

        updateStatus("", "info");
        setVideoState('recognized');
        setFaceStatus('success', 'Wajah terdeteksi');
        updateLiveness(detections);
        if (!isLivenessVerified && detectedNameInput) detectedNameInput.value = recognizedEmployeeName || matchedEmployee.name;
    }

    function startVideo() {
        setFaceStatus('detecting', 'Menyiapkan kamera');
        updateStatus("Menyiapkan kamera...", "info", 0, true);
        setVideoLoading(true);
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            const attempts = [
                {
                    audio: false,
                    video: {
                        facingMode: { ideal: "user" },
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        frameRate: { ideal: 30, max: 30 }
                    }
                },
                { audio: false, video: { facingMode: { ideal: "user" } } },
                { audio: false, video: true }
            ];

            (async () => {
                let lastError = null;
                for (const constraints of attempts) {
                    try {
                        return await navigator.mediaDevices.getUserMedia(constraints);
                    } catch (e) {
                        lastError = e;
                    }
                }
                throw lastError || new Error('getUserMedia failed');
            })().then(s => {
                stream = s;
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    const playPromise = video.play();
                    if (playPromise && typeof playPromise.catch === 'function') playPromise.catch(() => {});
                };
                video.onplaying = () => {
                    setVideoLoading(false);
                    updateStatus("Siap memindai", "info", 0, true);
                    setVideoState('detecting');
                    setFaceStatus('detecting', 'Arahkan wajah ke kamera');
                    showDefaultInstruction();
                    initLivenessChallenge();
                    ensureMatcherReady();
                    startScanning();
                };
            }).catch((err) => {
                setVideoLoading(false);
                setFaceStatus('failed', 'Kamera tidak tersedia');
                updateStatus("Kamera error", "danger", 1600, true);
                const name = String(err && err.name ? err.name : '');
                const msg =
                    name === 'NotAllowedError' ? 'Izin kamera ditolak.' :
                    name === 'NotFoundError' ? 'Kamera tidak ditemukan.' :
                    name === 'NotReadableError' ? 'Kamera sedang dipakai aplikasi lain.' :
                    name === 'OverconstrainedError' ? 'Kamera tidak kompatibel.' :
                    'Kamera tidak dapat diakses.';
                setInstructionPanelState({
                    state: 'failed',
                    stepLabel: 'Instruksi',
                    text: msg,
                    hint: '',
                    icon: 'fa-camera-slash',
                    progressStep: 1,
                    progressLabel: 'Deteksi wajah',
                    statusLabel: '',
                });
            });
        } else {
            setVideoLoading(false);
            setFaceStatus('failed', 'Kamera tidak tersedia');
            updateStatus("Kamera tidak tersedia", "danger", 1600, true);
            setInstructionPanelState({
                state: 'failed',
                stepLabel: 'Instruksi',
                text: 'Verifikasi gagal. Kamera tidak tersedia.',
                hint: '',
                icon: 'fa-camera-slash',
                progressStep: 1,
                progressLabel: 'Deteksi wajah',
                statusLabel: '',
            });
        }
    }

    function stopVideo() {
        if (stream) stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        stream = null;
        stopScanning();
    }

    function openScanner() {
        scanInterface.style.display = 'flex';
        requestAnimationFrame(() => scanInterface.classList.add('is-open'));
        setAlreadyAttendedUI(false);
        resetChecklist('face');
        setFaceStatus('detecting', 'Memeriksa lokasi');
        showDefaultInstruction();
        updateStatus("Memeriksa lokasi...", "info", 0, true);
        startVideo();

        if (!navigator.geolocation) {
            updateStatus("GPS tidak tersedia", "danger", 1400, true);
            setFaceStatus('failed', 'GPS tidak tersedia');
            setInstructionPanelState({
                state: 'failed',
                stepLabel: 'Instruksi',
                text: 'Verifikasi gagal. GPS tidak tersedia.',
                hint: '',
                icon: 'fa-location-crosshairs',
                progressStep: 1,
                progressLabel: 'Deteksi wajah',
                statusLabel: 'Gagal',
            });
            setTimeout(() => closeScanner(), 1000);
            return;
        }

        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const loc = document.getElementById('location');
            if (loc) loc.value = lat + "," + lng;
            const distanceM = getDistanceFromLatLonInKm(lat, lng, OFFICE_LAT, OFFICE_LNG) * 1000;
            if (distanceM > MAX_RADIUS) {
                updateStatus(`Di luar radius (${Math.round(distanceM)}m)`, "danger", 1600, true);
                setFaceStatus('failed', 'Di luar radius');
                setInstructionPanelState({
                    state: 'failed',
                    stepLabel: 'Instruksi',
                    text: 'Verifikasi gagal. Anda berada di luar radius absensi.',
                    hint: '',
                    icon: 'fa-location-dot',
                    progressStep: 1,
                    progressLabel: 'Deteksi wajah',
                    statusLabel: 'Gagal',
                });
                setTimeout(() => closeScanner(), 1200);
                return;
            }
            const landing = document.getElementById('landingPage');
            if (landing) landing.classList.add('d-none');
            setFaceStatus('detecting', 'Arahkan wajah ke kamera');
            updateStatus("Siap memindai", "info", 600, true);
        }, () => {
            updateStatus("Izin lokasi ditolak", "danger", 1600, true);
            setFaceStatus('failed', 'Izin lokasi ditolak');
            setInstructionPanelState({
                state: 'failed',
                stepLabel: 'Instruksi',
                text: 'Verifikasi gagal. Izin lokasi ditolak.',
                hint: '',
                icon: 'fa-location-crosshairs',
                progressStep: 1,
                progressLabel: 'Deteksi wajah',
                statusLabel: 'Gagal',
            });
            setTimeout(() => closeScanner(), 1200);
        }, { enableHighAccuracy: false, timeout: 3000, maximumAge: 60000 });
    }

    function closeScanner() {
        stopVideo();
        stopScanning();
        scanInterface.classList.remove('is-open');
        setTimeout(() => { scanInterface.style.display = 'none'; }, 230);
        const landing = document.getElementById('landingPage');
        if (landing) landing.classList.remove('d-none');
        resetCamera();
        closeManualModal();
    }

    async function captureAndDetect() {
        if (isAlreadyAttended) {
            if (detectedNameInput) detectedNameInput.value = recognizedEmployeeName || candidateEmployee?.name || "";
            return;
        }

        const targetId = recognizedEmployeeId || (userIdInput ? userIdInput.value : "");
        if (!targetId) {
            if (detectedNameInput) detectedNameInput.value = "Arahkan wajah ke kamera";
            return;
        }
        if (!isLivenessVerified) {
            updateStatus("Tunggu validasi wajah...", "info", 650);
            setFaceStatus('instruction', 'Selesaikan verifikasi');
            setInstructionPanelState({
                state: 'instruction',
                stepLabel: 'Instruksi',
                text: 'Memverifikasi identitas...',
                hint: '',
                icon: 'fa-spinner fa-spin',
                progressStep: 3,
                progressLabel: 'Verifikasi',
                statusLabel: 'Arah',
            });
            return;
        }

        if (userIdInput) userIdInput.value = targetId;
        isProcessing = true;
        stopScanning();

        const canvas = document.createElement('canvas');
        const maxWidth = 640;
        const srcWidth = video.videoWidth;
        const srcHeight = video.videoHeight;
        const ratio = srcWidth ? Math.min(1, maxWidth / srcWidth) : 1;
        canvas.width = Math.round(srcWidth * ratio);
        canvas.height = Math.round(srcHeight * ratio);
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.72);

        video.style.display = 'none';
        capturedImage.src = dataUrl;
        capturedImage.style.display = 'block';
        if (captureBtn) captureBtn.classList.add('d-none');
        if (submitBtn) submitBtn.classList.remove('d-none');
        const photo = document.getElementById('photo');
        if (photo) photo.value = dataUrl;
        if (submitBtn) submitBtn.disabled = false;
        setFaceStatus('success', 'Foto siap dikonfirmasi');
        updateStatus("Foto siap dikonfirmasi", "success", 900, true);
        setInstructionPanelState({
            state: 'success',
            stepLabel: 'Instruksi',
            text: 'Verifikasi berhasil.',
            hint: '',
            icon: 'fa-image',
            progressStep: 3,
            progressLabel: 'Selesai',
            statusLabel: 'OK',
        });
    }

    function resetCamera() {
        isProcessing = false;
        setAlreadyAttendedUI(false);
        video.style.display = 'block';
        capturedImage.style.display = 'none';
        if (captureBtn) captureBtn.classList.remove('d-none');
        if (submitBtn) submitBtn.classList.add('d-none');
        if (detectedNameInput) detectedNameInput.value = "";
        if (userIdInput) userIdInput.value = "";
        if (submitBtn) submitBtn.disabled = true;
        isLivenessVerified = false;
        isAlreadyAttended = false;
        candidateEmployee = null;
        manualSelectedEmployee = null;
        resetChecklist('face');
        resetMatchState();
        initLivenessChallenge();
        setFaceStatus('detecting', 'Arahkan wajah ke kamera');
        updateStatus("Siap memindai", "info", 450, true);
        setVideoState('detecting');
        showDefaultInstruction();
        if (scanInterface && scanInterface.style.display !== 'none') startScanning();
    }

    function submitAttendance() {
        if (!isLivenessVerified || !userIdInput || !userIdInput.value) {
            updateStatus("Verifikasi belum lengkap", "danger", 1400, true);
            failChecklistStep('verification');
            return;
        }
        setFaceStatus('instruction', 'Menyimpan absensi');
        updateStatus("Menyimpan absensi...", "info", 0, true);
        focusChecklistStep('verification');
        setInstructionPanelState({
            state: 'instruction',
            stepLabel: 'Instruksi',
            text: 'Memverifikasi identitas...',
            hint: '',
            icon: 'fa-spinner fa-spin',
            progressStep: 3,
            progressLabel: 'Verifikasi',
            statusLabel: 'Arah',
        });
        const form = document.getElementById('attendanceForm');
        if (form) form.submit();
    }

    if (manualPickBtn) {
        manualPickBtn.addEventListener('click', () => openManualModal());
    }

    if (manualCloseBtn) manualCloseBtn.addEventListener('click', () => closeManualModal());
    if (manualModalBackdrop) manualModalBackdrop.addEventListener('click', () => closeManualModal());

    if (manualSearch) {
        manualSearch.addEventListener('input', (e) => renderManualList(e.target.value));
    }

    if (manualList) {
        manualList.addEventListener('click', (e) => {
            const btn = e.target && e.target.closest ? e.target.closest('[data-emp-id]') : null;
            if (!btn) return;
            const id = String(btn.getAttribute('data-emp-id') || "");
            const employee = employees.find(emp => String(emp.id) === id);
            if (!employee) return;
            manualSelectedEmployee = employee;
            candidateEmployee = employee;
            recognizedEmployeeId = String(employee.id);
            recognizedEmployeeName = employee.name;
            if (detectedNameInput) detectedNameInput.value = employee.name;
            updateStatus("Nama dipilih • arahkan wajah", "info", 900, true);
            setFaceStatus('detecting', 'Arahkan wajah ke kamera');
            faceDetectedAt = Date.now();
            resetChecklist('face');
            showDefaultInstruction();
            closeManualModal();
        });
    }

    renderManualList("");

    window.openScanner = openScanner;
    window.closeScanner = closeScanner;
    window.captureAndDetect = captureAndDetect;
    window.resetCamera = resetCamera;
    window.submitAttendance = submitAttendance;

    setCaptureReady(false);
    if (submitBtn) submitBtn.classList.add('d-none');
    setFaceStatus('detecting', 'Wajah belum terdeteksi');
    updateStatus("Memulai...", "info", 0, true);
    resetChecklist('face');
    showDefaultInstruction();
    ensureMatcherReady();
}

document.addEventListener('DOMContentLoaded', () => {
    initKiosk();
});
