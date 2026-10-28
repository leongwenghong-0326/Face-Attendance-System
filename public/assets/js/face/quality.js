/**
 * Quality Module
 * Evaluates face dimensions, lighting/brightness, image sharpness, and detection stability
 */
class FaceQualityChecker {
    constructor(settings = {}) {
        this.minFaceSize = parseInt(settings.min_face_size) || 70;
        this.maxFaceSize = parseInt(settings.max_face_size) || 450;
        this.minBrightness = parseInt(settings.min_brightness) || 35;
        this.maxBrightness = parseInt(settings.max_brightness) || 230;
        this.minSharpness = parseInt(settings.min_sharpness) || 5;
        this.requiredStableFrames = parseInt(settings.stable_frames) || 2;

        this.consecutiveStableCount = 0;
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d', { willReadFrequently: true });
    }

    updateSettings(settings) {
        if (settings.min_face_size) this.minFaceSize = parseInt(settings.min_face_size);
        if (settings.max_face_size) this.maxFaceSize = parseInt(settings.max_face_size);
        if (settings.min_brightness) this.minBrightness = parseInt(settings.min_brightness);
        if (settings.max_brightness) this.maxBrightness = parseInt(settings.max_brightness);
        if (settings.min_sharpness) this.minSharpness = parseInt(settings.min_sharpness);
        if (settings.stable_frames) this.requiredStableFrames = parseInt(settings.stable_frames);
    }

    evaluate(allDetections, videoElement) {
        // 1. Face Count Check
        if (!allDetections || allDetections.length === 0) {
            this.consecutiveStableCount = 0;
            return { passed: false, reason: 'No face detected. Please position yourself in front of the camera.' };
        }

        if (allDetections.length > 1) {
            this.consecutiveStableCount = 0;
            return { passed: false, reason: 'Multiple faces detected. Only one person should be in the frame.' };
        }

        const detection = allDetections[0];
        const box = detection.detection.box;
        const detectorScore = detection.detection.score || 0;

        // 2. Face Size Check
        const faceDim = Math.min(box.width, box.height);
        if (faceDim < this.minFaceSize) {
            this.consecutiveStableCount = 0;
            return { passed: false, reason: 'Face is too far away. Please step closer to the camera.' };
        }

        if (faceDim > this.maxFaceSize) {
            this.consecutiveStableCount = 0;
            return { passed: false, reason: 'Face is too close. Please step back slightly.' };
        }

        // 3. Brightness and Sharpness Inspection from Video Frame
        const qualityMetrics = this.analyzeRegion(videoElement, box);

        if (qualityMetrics.brightness < this.minBrightness) {
            this.consecutiveStableCount = 0;
            return { passed: false, reason: 'Lighting is too dark. Please increase ambient light.' };
        }

        if (qualityMetrics.brightness > this.maxBrightness) {
            this.consecutiveStableCount = 0;
            return { passed: false, reason: 'Lighting is too bright / overexposed. Avoid harsh direct backlights.' };
        }

        // Sharpness tolerance: If face detector confidence is high, allow slightly softer threshold
        const effectiveMinSharpness = detectorScore >= 0.70 ? Math.max(3, this.minSharpness - 2) : this.minSharpness;
        if (qualityMetrics.sharpness < effectiveMinSharpness) {
            this.consecutiveStableCount = 0;
            return { passed: false, reason: 'Image is too blurry. Hold still to focus.' };
        }

        // 4. Stability Accumulator
        this.consecutiveStableCount++;
        const isStable = this.consecutiveStableCount >= this.requiredStableFrames;

        return {
            passed: isStable,
            isStable: isStable,
            stableCount: this.consecutiveStableCount,
            requiredStable: this.requiredStableFrames,
            metrics: qualityMetrics,
            box: box,
            reason: isStable ? 'Face quality verified and stable.' : `Holding position (${this.consecutiveStableCount}/${this.requiredStableFrames})...`
        };
    }

    reset() {
        this.consecutiveStableCount = 0;
    }

    analyzeRegion(videoElement, box) {
        const x = Math.max(0, Math.floor(box.x));
        const y = Math.max(0, Math.floor(box.y));
        const w = Math.min(videoElement.videoWidth - x, Math.floor(box.width));
        const h = Math.min(videoElement.videoHeight - y, Math.floor(box.height));

        if (w <= 0 || h <= 0) {
            return { brightness: 128, sharpness: 30 };
        }

        this.canvas.width = w;
        this.canvas.height = h;
        this.ctx.drawImage(videoElement, x, y, w, h, 0, 0, w, h);

        const imgData = this.ctx.getImageData(0, 0, w, h);
        const data = imgData.data;

        let totalBrightness = 0;
        const totalPixels = w * h;

        // Grayscale conversion & brightness
        const gray = new Uint8ClampedArray(totalPixels);
        for (let i = 0, j = 0; i < data.length; i += 4, j++) {
            const b = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
            gray[j] = b;
            totalBrightness += b;
        }

        const avgBrightness = Math.round(totalBrightness / totalPixels);

        // Compute sharpness with focus on facial features (eyes, nose, mouth)
        // Focus on central 70% to avoid boundary background and uniform skin dilution
        const startX = Math.max(1, Math.floor(w * 0.15));
        const endX = Math.min(w - 2, Math.floor(w * 0.85));
        const startY = Math.max(1, Math.floor(h * 0.15));
        const endY = Math.min(h - 2, Math.floor(h * 0.85));

        let laplaceSum = 0;
        let count = 0;
        for (let py = startY; py <= endY; py += 2) {
            for (let px = startX; px <= endX; px += 2) {
                const idx = py * w + px;
                const lap = Math.abs(
                    gray[idx - 1] + gray[idx + 1] + gray[idx - w] + gray[idx + w] - 4 * gray[idx]
                );
                laplaceSum += lap;
                count++;
            }
        }

        // Calibrated sharpness score: focused edge variance amplified
        const rawScore = count > 0 ? (laplaceSum / count) * 2.5 : 20;
        const sharpnessScore = Math.round(rawScore);

        return {
            brightness: avgBrightness,
            sharpness: sharpnessScore
        };
    }
}

window.FaceQualityChecker = FaceQualityChecker;
