/**
 * Attendance Flow Module
 * Handles individual student attendance verification via camera
 */
class AttendanceFlow {
    constructor(options = {}) {
        this.expectedStudentId = options.studentId || null;
        this.videoEl = options.videoElement;
        this.canvasEl = options.canvasElement;
        this.statusEl = options.statusElement;
        this.resultCardEl = options.resultCardElement;
        this.apiScanUrl = options.apiScanUrl || '/face_attendance_system/public/api/attendance/scan';

        this.camera = new CameraManager();
        this.engine = new FaceEngine(options.modelUrl);
        this.quality = new FaceQualityChecker(options.settings || {});
        this.matcher = new FaceMatcherManager(
            parseFloat(options.settings?.match_threshold || 0.55),
            parseFloat(options.settings?.min_confidence || 0.45)
        );

        this.isRunning = false;
        this.isProcessing = false;
    }

    async init() {
        this.updateStatus('Loading neural models and student templates...', 'info');
        await this.engine.loadModels();
        await this.matcher.loadDescriptors();

        this.updateStatus('Starting camera...', 'info');
        await this.camera.start(this.videoEl);

        this.isRunning = true;
        this.updateStatus('Looking for face. Please position your face in the camera frame.', 'primary');
        this.loop();
    }

    async loop() {
        if (!this.isRunning) return;

        if (!this.isProcessing && this.videoEl.readyState >= 2) {
            try {
                const detections = await this.engine.detectAllFaces(this.videoEl);
                const evalResult = this.quality.evaluate(detections, this.videoEl);

                if (!evalResult.passed) {
                    this.updateStatus(evalResult.reason, 'warning');
                    this.drawBox(evalResult.box, false);
                } else {
                    const singleFace = await this.engine.detectSingleFaceWithDescriptor(this.videoEl);
                    if (singleFace) {
                        const match = this.matcher.findBestMatch(singleFace.descriptor);

                        if (!match.matched) {
                            this.updateStatus(match.reason, 'danger');
                            this.drawBox(evalResult.box, false);
                        } else {
                            // If this scanner is restricted to a specific student (student portal)
                            if (this.expectedStudentId && match.student.student_id !== parseInt(this.expectedStudentId)) {
                                this.updateStatus(`Recognized ${match.student.full_name}, but you are logged in as a different student.`, 'danger');
                                this.drawBox(evalResult.box, false);
                            } else {
                                this.drawBox(evalResult.box, true);
                                await this.processAttendance(match.student, match.confidence);
                            }
                        }
                    }
                }
            } catch (err) {
                console.error('Frame loop error:', err);
            }
        }

        if (this.isRunning) {
            requestAnimationFrame(() => this.loop());
        }
    }

    async processAttendance(student, confidence) {
        this.isProcessing = true;
        this.updateStatus(`Verifying attendance for ${student.full_name}...`, 'info');

        const snapshot = this.camera.captureFrame(400, 400, 0.85);

        try {
            const resp = await fetch(this.apiScanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    student_id: student.student_id,
                    confidence: confidence,
                    photo: snapshot,
                })
            });

            const data = await resp.json();

            if (data.success) {
                this.displayResult(data, 'success');
            } else {
                this.displayResult(data, 'warning');
            }
        } catch (err) {
            console.error('Attendance submit error:', err);
            this.updateStatus('Network connection error while submitting attendance.', 'danger');
        }

        // Pause for 5 seconds before resuming detection
        setTimeout(() => {
            this.isProcessing = false;
            this.quality.reset();
            this.updateStatus('Ready for next scan. Look at the camera.', 'primary');
        }, 5000);
    }

    drawBox(box, isMatch) {
        const ctx = this.canvasEl.getContext('2d');
        ctx.clearRect(0, 0, this.canvasEl.width, this.canvasEl.height);
        if (!box) return;

        ctx.strokeStyle = isMatch ? '#198754' : '#dc3545';
        ctx.lineWidth = 3;
        ctx.strokeRect(box.x, box.y, box.width, box.height);
    }

    displayResult(data, alertType = 'success') {
        this.updateStatus(data.message, alertType);

        if (this.resultCardEl) {
            this.resultCardEl.style.display = 'block';
            this.resultCardEl.className = `card border-${alertType} shadow mb-4`;
            this.resultCardEl.innerHTML = `
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-1 text-${alertType} flex-shrink-0">
                            <i class="bi ${alertType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'}"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h5 class="card-title mb-1 text-truncate">${data.student?.name || 'Attendance Notice'}</h5>
                            <p class="card-text mb-1 text-muted text-break">${data.message}</p>
                            ${data.confidence ? `<span class="badge bg-secondary">Confidence: ${data.confidence}</span>` : ''}
                            ${data.status ? `<span class="badge bg-primary ms-1">${data.status.toUpperCase()}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
    }

    updateStatus(message, badgeClass = 'primary') {
        if (!this.statusEl) return;
        this.statusEl.className = `alert alert-${badgeClass} py-2 mb-3 text-center fw-semibold shadow-sm`;
        this.statusEl.textContent = message;
    }

    stop() {
        this.isRunning = false;
        this.camera.stop();
    }
}

window.AttendanceFlow = AttendanceFlow;
