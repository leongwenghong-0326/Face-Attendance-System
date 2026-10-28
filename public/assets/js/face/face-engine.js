/**
 * FaceEngine Module
 * Loads face-api.js local models and runs detection & descriptor extractions
 */
class FaceEngine {
    constructor(modelPath = '/face_attendance_system/public/assets/models') {
        this.modelPath = modelPath;
        this.isLoaded = false;
        this.detectorOptions = null;
    }

    async loadModels(inputSize = 320, scoreThreshold = 0.5) {
        if (this.isLoaded) return true;

        if (typeof faceapi === 'undefined') {
            throw new Error('face-api.js library not loaded in document.');
        }

        try {
            // Load required models from local assets
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(this.modelPath),
                faceapi.nets.faceLandmark68Net.loadFromUri(this.modelPath),
                faceapi.nets.faceRecognitionNet.loadFromUri(this.modelPath),
            ]);

            this.detectorOptions = new faceapi.TinyFaceDetectorOptions({
                inputSize: inputSize,
                scoreThreshold: scoreThreshold,
            });

            this.isLoaded = true;
            return true;
        } catch (err) {
            console.error('Failed to load local face recognition models:', err);
            throw err;
        }
    }

    async detectSingleFaceWithDescriptor(inputElement) {
        if (!this.isLoaded) throw new Error('Models not yet loaded');

        // Detect single face with landmarks and 128-d descriptor
        return await faceapi
            .detectSingleFace(inputElement, this.detectorOptions)
            .withFaceLandmarks()
            .withFaceDescriptor();
    }

    async detectAllFaces(inputElement) {
        if (!this.isLoaded) throw new Error('Models not yet loaded');

        return await faceapi
            .detectAllFaces(inputElement, this.detectorOptions)
            .withFaceLandmarks()
            .withFaceDescriptors();
    }

    drawDetection(canvas, detection, label = '') {
        if (!detection) return;
        const dims = faceapi.matchDimensions(canvas, canvas);
        const resized = faceapi.resizeResults(detection, dims);

        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const box = resized.detection.box;
        ctx.strokeStyle = '#0d6efd';
        ctx.lineWidth = 3;
        ctx.strokeRect(box.x, box.y, box.width, box.height);

        if (label) {
            ctx.fillStyle = '#0d6efd';
            ctx.fillRect(box.x, box.y - 25, Math.max(120, label.length * 9), 25);
            ctx.fillStyle = '#ffffff';
            ctx.font = '14px sans-serif';
            ctx.fillText(label, box.x + 6, box.y - 7);
        }
    }
}

window.FaceEngine = FaceEngine;
