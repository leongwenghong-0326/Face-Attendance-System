/**
 * Kiosk Flow Module
 * High-performance continuous scanning station for school lobbies, classrooms, and reception kiosks
 */
class KioskFlow {
    constructor(options = {}) {
        this.videoEl = options.videoElement;
        this.canvasEl = options.canvasElement;
        this.statusEl = options.statusElement;
        this.feedbackCardEl = options.feedbackCardElement;
        this.recentListEl = options.recentListElement;
        this.clockEl = options.clockElement;
        this.apiScanUrl = options.apiScanUrl || '/face_attendance_system/public/api/attendance/scan';

        this.camera = new CameraManager();
        this.engine = new FaceEngine(options.modelUrl);
        this.quality = new FaceQualityChecker(options.settings || {});
        this.matcher = new FaceMatcherManager(
            parseFloat(options.settings?.match_threshold || 0.55),
            parseFloat(options.settings?.min_confidence || 0.45)
        );

        this.isRunning = false;
        this.isLocked = false;
        this.cooldownMap = new Map(); // Local in-memory debounce map: studentId => timestamp
        this.cooldownSeconds = parseInt(options.settings?.duplicate_scan_interval || 300);
        this.recentScans = [];
    }

    async init() {
        this.startClock();
        this.updateStatus('Initializing Face Recognition Engine & Local Weights...', 'info');

        await this.engine.loadModels();
        const count = await this.matcher.loadDescriptors();
        this.updateStatus(`Loaded ${count} student biometric profiles. Starting camera...`, 'info');

        await this.camera.start(this.videoEl);
        this.isRunning = true;
        this.updateStatus('Attendance Kiosk Online. Please step in front of the camera.', 'success');

        this.loop();
    }

    startClock() {
        if (!this.clockEl) return;
        const update = () => {
            const now = new Date();
            this.clockEl.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        };
        update();
        setInterval(update, 1000);
    }

    async loop() {
        if (!this.isRunning) return;

        if (!this.isLocked && this.videoEl.readyState >= 2) {
            try {
                const detections = await this.engine.detectAllFaces(this.videoEl);
                const evalResult = this.quality.evaluate(detections, this.videoEl);

                if (!evalResult.passed) {
                    this.drawBox(evalResult.box, '#ffc107');
                    this.updateStatus(evalResult.reason, 'light');
                } else {
                    const singleFace = await this.engine.detectSingleFaceWithDescriptor(this.videoEl);
                    if (singleFace) {
                        const match = this.matcher.findBestMatch(singleFace.descriptor);

                        if (match.matched) {
                            this.drawBox(evalResult.box, '#198754');
                            await this.onStudentRecognized(match.student, match.confidence);
                        } else {
                            this.drawBox(evalResult.box, '#dc3545');
                            this.updateStatus(match.reason, 'warning');
                        }
                    }
                }
            } catch (err) {
                console.error('Kiosk detection loop error:', err);
            }
        }

        if (this.isRunning) {
            requestAnimationFrame(() => this.loop());
        }
    }

    async onStudentRecognized(student, confidence) {
        const studentId = student.student_id;
        const now = Date.now();

        // Check local in-memory cooldown to avoid hammering API
        if (this.cooldownMap.has(studentId)) {
            const lastTime = this.cooldownMap.get(studentId);
            const elapsed = Math.floor((now - lastTime) / 1000);
            if (elapsed < 30) { // 30s local UI silence
                this.updateStatus(`Attendance already noted for ${student.full_name}. Next person please.`, 'info');
                return;
            }
        }

        this.isLocked = true;
        this.cooldownMap.set(studentId, now);

        this.playBeep(880, 150); // Audio chime
        const snapshot = this.camera.captureFrame(400, 400, 0.85);

        try {
            const resp = await fetch(this.apiScanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    student_id: studentId,
                    confidence: confidence,
                    photo: snapshot,
                })
            });

            const result = await resp.json();
            this.showFeedbackModal(result, student, confidence, snapshot);

            if (result.success) {
                this.addRecentScan(result, student, snapshot);
            }
        } catch (err) {
            console.error('Kiosk sync error:', err);
            this.updateStatus('Network communication error.', 'danger');
        }

        // Display feedback card for 2.8 seconds, then automatically continue scanning
        setTimeout(() => {
            this.isLocked = false;
            this.quality.reset();
            this.hideFeedbackModal();
            this.updateStatus('Kiosk Ready. Step in front of the camera.', 'success');
        }, 2800);
    }

    showFeedbackModal(result, student, confidence, snapshot) {
        if (!this.feedbackCardEl) return;

        const isSuccess = result.success;
        const statusType = result.status || 'checked_in';
        const badgeColor = isSuccess ? (statusType === 'late' ? 'warning' : 'success') : 'warning';
        const headerTitle = isSuccess ? 'Attendance Recorded' : 'Attendance Notice';
        const headerIcon = isSuccess ? 'bi-person-check-fill' : 'bi-exclamation-triangle-fill';

        this.feedbackCardEl.innerHTML = `
            <div class="card border-${badgeColor} shadow-lg animate__animated animate__fadeInDown overflow-hidden">
                <div class="card-header bg-${badgeColor} text-white d-flex justify-content-between align-items-center py-2 px-3">
                    <span class="fw-bold fs-6"><i class="bi ${headerIcon} me-2"></i> ${headerTitle}</span>
                    <span class="badge bg-light text-dark">${result.action ? result.action.toUpperCase() : (isSuccess ? 'VERIFIED' : 'NOTICE')}</span>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-sm-row align-items-center gap-3">
                        <div class="flex-shrink-0 text-center">
                            <img src="${snapshot || student.preview_image || '/face_attendance_system/public/assets/img/avatar.png'}" 
                                 class="rounded-circle border border-3 border-${badgeColor} shadow-sm" 
                                 style="width: 96px; height: 96px; object-fit: cover; display: block;"
                                 alt="${student.full_name}">
                        </div>
                        <div class="flex-grow-1 text-center text-sm-start min-w-0" style="overflow: hidden;">
                            <h4 class="fw-bold mb-1 text-dark text-truncate">${student.full_name}</h4>
                            <div class="text-muted small mb-2 text-truncate">Code: <span class="fw-semibold text-dark">${student.student_code}</span> | Class: <span class="fw-semibold text-dark">${student.class_name || 'General'}</span></div>
                            <div class="alert alert-${badgeColor} py-2 px-3 mb-2 fw-semibold text-break shadow-sm" style="font-size: 0.9rem; line-height: 1.4;">
                                ${result.message}
                            </div>
                            <div>
                                <small class="text-muted">Confidence: <strong>${Math.round(confidence * 100)}%</strong></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        this.feedbackCardEl.style.display = 'block';
    }

    hideFeedbackModal() {
        if (this.feedbackCardEl) {
            this.feedbackCardEl.style.display = 'none';
        }
    }

    addRecentScan(result, student, snapshot) {
        this.recentScans.unshift({
            name: student.full_name,
            code: student.student_code,
            time: new Date().toLocaleTimeString(),
            status: result.status,
            photo: snapshot
        });

        if (this.recentScans.length > 6) {
            this.recentScans.pop();
        }

        if (this.recentListEl) {
            this.recentListEl.innerHTML = this.recentScans.map(item => `
                <li class="list-group-item d-flex align-items-center justify-content-between py-2">
                    <div class="d-flex align-items-center">
                        <img src="${item.photo}" class="rounded-circle me-2 border" style="width: 38px; height: 38px; object-fit: cover;">
                        <div>
                            <div class="fw-bold">${item.name}</div>
                            <small class="text-muted">${item.code}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge ${item.status === 'late' ? 'bg-warning text-dark' : 'bg-success'}">${item.status}</span>
                        <div class="small text-muted">${item.time}</div>
                    </div>
                </li>
            `).join('');
        }
    }

    drawBox(box, color = '#0d6efd') {
        const ctx = this.canvasEl.getContext('2d');
        ctx.clearRect(0, 0, this.canvasEl.width, this.canvasEl.height);
        if (!box) return;

        ctx.strokeStyle = color;
        ctx.lineWidth = 3;
        ctx.strokeRect(box.x, box.y, box.width, box.height);
    }

    updateStatus(message, badgeClass = 'info') {
        if (!this.statusEl) return;
        this.statusEl.className = `alert alert-${badgeClass} py-2 mb-2 text-center fw-semibold shadow-sm`;
        this.statusEl.textContent = message;
    }

    playBeep(frequency = 880, duration = 120) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = frequency;
            osc.type = 'sine';
            osc.start();
            setTimeout(() => {
                osc.stop();
                ctx.close();
            }, duration);
        } catch (e) {
            // Audio context not allowed before user interaction
        }
    }

    stop() {
        this.isRunning = false;
        this.camera.stop();
    }
}

window.KioskFlow = KioskFlow;
