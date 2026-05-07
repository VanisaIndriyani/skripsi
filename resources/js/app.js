import './bootstrap';

const FACE_API_CDN = 'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js';

function ensureScriptLoaded(src) {
    return new Promise((resolve, reject) => {
        const existing = Array.from(document.scripts || []).find((s) => s && s.src === src);
        if (existing) {
            if (existing.dataset.loaded === 'true') return resolve();
            existing.addEventListener('load', () => resolve(), { once: true });
            existing.addEventListener('error', (e) => reject(e), { once: true });
            return;
        }
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.addEventListener('load', () => {
            script.dataset.loaded = 'true';
            resolve();
        }, { once: true });
        script.addEventListener('error', (e) => reject(e), { once: true });
        document.head.appendChild(script);
    });
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
    const actionButtons = document.getElementById('actionButtons');
    const videoContainer = document.getElementById('videoContainer');
    const successOverlay = document.getElementById('successOverlay');
    const scanInterface = document.getElementById('scanInterface');
    const instructionToast = document.getElementById('instructionToast');
    const instructionText = document.getElementById('instructionText');
    const statusSpinner = document.getElementById('statusSpinner');
    const manualPickBtn = document.getElementById('manualPickBtn');
    const manualModal = document.getElementById('manualModal');
    const manualModalBackdrop = document.getElementById('manualModalBackdrop');
    const manualCloseBtn = document.getElementById('manualModalClose');
    const manualSearch = document.getElementById('manualSearch');
    const manualList = document.getElementById('manualList');

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
    const REQUIRED_BLINKS = 1;
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
    const YAW_TURN_THRESHOLD = 0.18;
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

    let statusHoldUntil = 0;
    let lastStatusText = "";
    let lastStatusType = "";

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
        statusBadge.style.borderColor =
            type === 'success' ? 'rgba(34, 197, 94, 0.55)'
            : type === 'danger' ? 'rgba(239, 68, 68, 0.45)'
            : 'rgba(255, 255, 255, 0.12)';

        const shouldSpin = type === 'info' && (text.includes('Memeriksa') || text.includes('Menyiapkan') || text.includes('Memuat'));
        if (statusSpinner) statusSpinner.classList.toggle('d-none', !shouldSpin);

        lastStatusText = text;
        lastStatusType = type;
        statusHoldUntil = holdMs > 0 ? now + holdMs : 0;
    }

    function showInstruction(text) {
        if (!instructionToast || !instructionText) return;
        const t = String(text || '').trim();
        if (!t) return hideInstruction();
        if (t === lastInstructionShown && instructionToast.classList.contains('is-visible')) return;
        instructionText.textContent = t;
        instructionToast.classList.add('is-visible');
        lastInstructionShown = t;
    }

    function hideInstruction() {
        if (!instructionToast) return;
        instructionToast.classList.remove('is-visible');
        lastInstructionShown = "";
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
        modelsPromise = ensureScriptLoaded(FACE_API_CDN).then(async () => {
            if (!window.faceapi) throw new Error('faceapi missing');
            try {
                if (window.faceapi.tf && typeof window.faceapi.tf.setBackend === 'function') {
                    await window.faceapi.tf.setBackend('webgl');
                    if (typeof window.faceapi.tf.ready === 'function') await window.faceapi.tf.ready();
                }
            } catch (e) {}
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
        if (!isLivenessVerified) {
            isAlreadyAttended = false;
            candidateEmployee = null;
            manualSelectedEmployee = null;
            if (detectedNameInput) detectedNameInput.value = "";
            if (userIdInput) userIdInput.value = "";
            if (submitBtn) submitBtn.disabled = true;
            initLivenessChallenge();
            hideInstruction();
        }
    }

    function initLivenessChallenge() {
        livenessSequence = ['blink'];
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
        if (REQUIRED_BLINKS > 0 && blinkCount < REQUIRED_BLINKS) {
            updateStatus("Kedip 1x untuk verifikasi", "warning", 900, true);
            return;
        }
        isLivenessVerified = true;
        if (detectedNameInput) detectedNameInput.value = recognizedEmployeeName || candidateEmployee.name;
        if (userIdInput) userIdInput.value = recognizedEmployeeId || candidateEmployee.id;
        if (submitBtn) submitBtn.disabled = true;
        setCaptureReady(true);
        updateStatus("Wajah terverifikasi", "success", 900, true);
        setVideoState('verified');
        if (successOverlay) successOverlay.classList.add('is-visible');
        playSuccessBeep();
        stopScanning();
        hideInstruction();
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
        const step = livenessSequence[livenessStepIndex] || 'blink';
        setVideoState('detecting');

        if (step === 'blink') {
            if (!isLivenessVerified && blinkCount < requiredBlinks) showInstruction('Kedip 1x untuk verifikasi');
            else hideInstruction();
            const ears = getEyeEARs(landmarks);
            const avgEAR = ears.avgEAR;
            const stable = isFaceStable(detections.detection?.box) || isFaceCentered(detections);
            const yaw = getYaw(landmarks);
            updateEarBaseline(avgEAR, stable, yaw);
            const thresholds = getBlinkThresholds();
            updateBlinkState(avgEAR, stable, yaw, now, thresholds.low, thresholds.high);
            if (blinkCount >= requiredBlinks) {
                livenessStepIndex += 1;
                headStableFrames = 0;
                mouthState = 'closed';
                mouthOpenFrames = 0;
                mouthClosedFrames = 0;
            }
        } else if (step === 'mouth') {
            hideInstruction();
            const stable = isFaceStable(detections.detection?.box) || isFaceCentered(detections);
            const yaw = getYaw(landmarks);
            const mar = getMAR(landmarks);
            const done = updateMouthState(mar, stable, yaw);
            if (done) {
                livenessStepIndex += 1;
                headStableFrames = 0;
                blinkCount = 0;
                blinkState = 'open';
                earClosedFrames = 0;
                earOpenFrames = 0;
            }
        } else {
            hideInstruction();
            const yaw = getYaw(landmarks);
            const ok =
                (step === 'left' && yaw <= -YAW_TURN_THRESHOLD) ||
                (step === 'right' && yaw >= YAW_TURN_THRESHOLD);
            const stable = isFaceStable(detections.detection?.box) || isFaceCentered(detections);
            if (ok && stable) headStableFrames += 1;
            else headStableFrames = 0;
            if (headStableFrames >= HEAD_STABLE_FRAMES_REQUIRED) {
                livenessStepIndex += 1;
                headStableFrames = 0;
                blinkCount = 0;
                blinkState = 'open';
                earClosedFrames = 0;
                earOpenFrames = 0;
                mouthState = 'closed';
                mouthOpenFrames = 0;
                mouthClosedFrames = 0;
            }
        }

        if (livenessStepIndex < livenessSequence.length) return;
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
            updateStatus("Memuat model wajah...", "info", 0, true);
            setVideoState('detecting');
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
                updateStatus("Arahkan wajah ke kamera", "info", 900);
                setVideoState('detecting');
            }
            if (manualPickBtn) manualPickBtn.classList.add('d-none');
            return;
        }

        if (!faceMatcher) {
            setCaptureReady(false);
            setVideoState('detecting');
            updateStatus("Menyiapkan data karyawan...", "info", 0, true);
            ensureMatcherReady();
            if (manualPickBtn) manualPickBtn.classList.remove('d-none');
            return;
        }

        if (detections.detection && detections.detection.score < MIN_DETECTION_SCORE) {
            setCaptureReady(false);
            hideInstruction();
            if (!isLivenessVerified && isFaceCentered(detections)) updateStatus("", "info");
            else updateStatus("Posisikan wajah di oval", "warning", INSTRUCTION_HOLD_MS);
            setVideoState('detecting');
            return;
        }

        if (manualSelectedEmployee) {
            if (!isFaceCentered(detections)) {
                updateStatus("Posisikan wajah di oval", "warning", 900);
                setVideoState('detecting');
                return;
            }
            candidateEmployee = manualSelectedEmployee;
            recognizedEmployeeId = String(manualSelectedEmployee.id);
            recognizedEmployeeName = manualSelectedEmployee.name;
            if (detectedNameInput) detectedNameInput.value = recognizedEmployeeName;
            updateLiveness(detections);
            return;
        }

        const result = faceMatcher.findBestMatch(detections.descriptor);
        if (result.label === 'unknown' || result.distance > MATCH_THRESHOLD) {
            setCaptureReady(false);
            hideInstruction();
            unknownFaceFrames += 1;
            if (!isLivenessVerified && candidateEmployee && unknownFaceFrames >= 3) initLivenessChallenge();
            if (unknownFaceFrames >= UNKNOWN_RESET_FRAMES) resetMatchState();
            if (!isLivenessVerified) {
                if (detectedNameInput) detectedNameInput.value = "";
                updateStatus("Wajah belum dikenali", "warning", INSTRUCTION_HOLD_MS);
                setVideoState('detecting');
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
        }

        if (isAlreadyAttended) {
            setCaptureReady(false);
            if (submitBtn) submitBtn.disabled = true;
            updateStatus("Sudah absen hari ini", "secondary", INSTRUCTION_HOLD_MS);
            if (detectedNameInput) detectedNameInput.value = "Sudah absen";
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
            hideInstruction();
            if (detectedNameInput) detectedNameInput.value = `${recognizedEmployeeName || matchedEmployee.name} • Posisikan di oval`;
            return;
        }

        updateStatus("", "info");
        setVideoState('detecting');
        updateLiveness(detections);
        if (!isLivenessVerified && detectedNameInput) detectedNameInput.value = recognizedEmployeeName || matchedEmployee.name;
    }

    function startVideo() {
        updateStatus("Menyiapkan kamera...", "info", 0, true);
        setVideoLoading(true);
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({
                audio: false,
                video: {
                    facingMode: { ideal: "user" },
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    frameRate: { ideal: 30, max: 30 }
                }
            }).then(s => {
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
                    initLivenessChallenge();
                    ensureMatcherReady();
                    startScanning();
                };
            }).catch(() => {
                setVideoLoading(false);
                updateStatus("Kamera error", "danger", 1600, true);
            });
        } else {
            setVideoLoading(false);
            updateStatus("Kamera tidak tersedia", "danger", 1600, true);
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
        updateStatus("Memeriksa lokasi...", "info", 0, true);
        startVideo();

        if (!navigator.geolocation) {
            updateStatus("GPS tidak tersedia", "danger", 1400, true);
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
                setTimeout(() => closeScanner(), 1200);
                return;
            }
            const landing = document.getElementById('landingPage');
            if (landing) landing.classList.add('d-none');
            updateStatus("Siap memindai", "info", 600, true);
        }, () => {
            updateStatus("Izin lokasi ditolak", "danger", 1600, true);
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
            if (detectedNameInput) detectedNameInput.value = "Sudah absen";
            return;
        }

        const targetId = recognizedEmployeeId || (userIdInput ? userIdInput.value : "");
        if (!targetId) {
            if (detectedNameInput) detectedNameInput.value = "Arahkan wajah ke kamera";
            return;
        }
        if (!isLivenessVerified) {
            updateStatus("Tunggu validasi wajah...", "info", 650);
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
        if (actionButtons) actionButtons.classList.remove('d-none');
        const photo = document.getElementById('photo');
        if (photo) photo.value = dataUrl;
        if (submitBtn) submitBtn.disabled = false;
        updateStatus("Foto siap dikonfirmasi", "success", 900, true);
    }

    function resetCamera() {
        isProcessing = false;
        video.style.display = 'block';
        capturedImage.style.display = 'none';
        if (captureBtn) captureBtn.classList.remove('d-none');
        if (actionButtons) actionButtons.classList.add('d-none');
        if (detectedNameInput) detectedNameInput.value = "";
        if (userIdInput) userIdInput.value = "";
        if (submitBtn) submitBtn.disabled = true;
        isLivenessVerified = false;
        isAlreadyAttended = false;
        candidateEmployee = null;
        manualSelectedEmployee = null;
        resetMatchState();
        initLivenessChallenge();
        updateStatus("Siap memindai", "info", 450, true);
        setVideoState('detecting');
        hideInstruction();
        if (scanInterface && scanInterface.style.display !== 'none') startScanning();
    }

    function submitAttendance() {
        if (!isLivenessVerified || !userIdInput || !userIdInput.value) {
            updateStatus("Verifikasi belum lengkap", "danger", 1400, true);
            return;
        }
        updateStatus("Menyimpan absensi...", "info", 0, true);
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
    updateStatus("Memulai...", "info", 0, true);
    ensureMatcherReady();
}

document.addEventListener('DOMContentLoaded', () => {
    initKiosk();
});
