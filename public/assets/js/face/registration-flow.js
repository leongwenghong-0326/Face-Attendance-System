/**
 * Registration Flow Module
 * Coordinates interactive multi-sample face capture, quality checking, and server persistence
 */
class FaceRegistrationFlow {
    constructor(options = {}) {
        this.studentId = options.studentId;
        this.videoEl = options.videoElement;
        this.canvasEl = options.canvasElement;
        this.statusEl = options.statusElement;
        this.progressEl = options.progressBar;
        this.sampleCountEl = options.sampleCountElement;
        this.targetSamples = options.samples || 3;
        this.apiRegisterUrl = options.apiRegisterUrl || '/face_attendance_system/public/api/face/register';
        this.redirectUrl = options.redirectUrl || '/admin/students';

        this.camera = new CameraManager();
        this.engine = new FaceEngine(options.modelUrl);
        this.quality = new FaceQualityChecker(options.settings || {});

        this.samples = [];
        this.previewBase64 = null;
        this.isRunning = false;
        this.isSubmitting = false;
        this.lastSampleTime = 0;
    }

    async init() {
        this.updateStatus('Loading neural detection models...', 'info');
        await this.engine.loadModels();

        this.updateStatus('Starting camera stream...', 'info');
        await this.camera.start(this.videoEl);

        this.isRunning = true;
        this.updateStatus('Position your face inside the guide frame to begin capture.', 'primary');
        this.loop();
    }

    async loop() {
        if (!this.isRunning) return;

        try {
            if (this.videoEl.readyState >= 2) {
                const detections = await this.engine.detectAllFaces(this.videoEl);

                // Quality evaluation
                const evalResult = this.quality.evaluate(detections, this.videoEl);

                if (!evalResult.passed) {
                    this.updateStatus(evalResult.reason, 'warning');
                    this.drawGuide(evalResult.box, false);
                } else {
                    // Check head pose
                    const singleFace = await this.engine.detectSingleFaceWithDescriptor(this.videoEl);
                    if (singleFace) {
                        const pose = PoseChecker.checkFrontal(singleFace.landmarks);
                        if (!pose.isFrontal) {
                            this.updateStatus(pose.reason, 'warning');
                            this.drawGuide(evalResult.box, false);
                        } else {
                            this.drawGuide(evalResult.box, true);

                            // Face is good and stable. Check throttle for sample capture (at least 600ms between samples)
                            const now = Date.now();
                            if (now - this.lastSampleTime > 600 && this.samples.length < this.targetSamples) {
                                this.captureSample(singleFace.descriptor);
                                this.lastSampleTime = now;
                            }
                        }
                    }
                }
            }
        } catch (err) {
            console.error('Detection frame error:', err);
        }

        if (this.isRunning && this.samples.length < this.targetSamples) {
            requestAnimationFrame(() => this.loop());
        }
    }

    captureSample(descriptor) {
        // Convert Float32Array to regular array
        this.samples.push(Array.from(descriptor));

        // Save preview snapshot on the first high quality sample
        if (!this.previewBase64) {
            this.previewBase64 = this.camera.captureFrame(400, 400, 0.9);
        }

        const count = this.samples.length;
        if (this.sampleCountEl) {
            this.sampleCountEl.textContent = `${count} / ${this.targetSamples}`;
        }
        if (this.progressEl) {
            const pct = Math.round((count / this.targetSamples) * 100);
            this.progressEl.style.width = `${pct}%`;
            this.progressEl.textContent = `${pct}%`;
        }

        this.updateStatus(`Sample ${count} of ${this.targetSamples} captured! Keep looking at the camera...`, 'success');

        if (this.samples.length >= this.targetSamples) {
            this.finishRegistration();
        }
    }

    async finishRegistration() {
        this.isRunning = false;
        this.isSubmitting = true;
        this.updateStatus('All samples captured! Saving biometric profile to server...', 'info');

        try {
            const resp = await fetch(this.apiRegisterUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    student_id: this.studentId,
                    descriptors: this.samples,
                    preview_image: this.previewBase64,
                })
            });

            const result = await resp.json();

            if (result.success) {
                this.updateStatus('Face registration successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = this.redirectUrl;
                }, 1500);
            } else {
                this.updateStatus(`Registration failed: ${result.message}`, 'danger');
                this.showRetry();
            }
        } catch (err) {
            console.error('Registration upload error:', err);
            this.updateStatus('Network error while saving registration.', 'danger');
            this.showRetry();
        }
    }

    drawGuide(box, isGood = false) {
        const ctx = this.canvasEl.getContext('2d');
        ctx.clearRect(0, 0, this.canvasEl.width, this.canvasEl.height);

        if (!box) return;

        ctx.strokeStyle = isGood ? '#198754' : '#ffc107';
        ctx.lineWidth = 3;
        ctx.strokeRect(box.x, box.y, box.width, box.height);
    }

    updateStatus(message, badgeClass = 'primary') {
        if (!this.statusEl) return;
        this.statusEl.className = `alert alert-${badgeClass} py-2 mb-3 text-center fw-semibold shadow-sm`;
        this.statusEl.textContent = message;
    }

    showRetry() {
        this.samples = [];
        this.quality.reset();
        const retryBtn = document.getElementById('retry-btn');
        if (retryBtn) retryBtn.style.display = 'inline-block';
    }

    stop() {
        this.isRunning = false;
        this.camera.stop();
    }
}

window.FaceRegistrationFlow = FaceRegistrationFlow;
