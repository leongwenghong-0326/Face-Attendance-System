/**
 * Camera Module
 * Handles WebRTC camera streams, device switching, and video frame capture
 */
class CameraManager {
    constructor() {
        this.stream = null;
        this.videoElement = null;
        this.isStreaming = false;
    }

    async start(videoElement, facingMode = 'user') {
        this.videoElement = videoElement;
        this.stop(); // Stop previous stream if active

        const constraints = {
            audio: false,
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: facingMode
            }
        };

        try {
            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
            this.videoElement.srcObject = this.stream;

            return new Promise((resolve) => {
                this.videoElement.onloadedmetadata = () => {
                    this.videoElement.play();
                    this.isStreaming = true;
                    resolve(true);
                };
            });
        } catch (error) {
            console.error('Camera access error:', error);
            throw error;
        }
    }

    stop() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        if (this.videoElement) {
            this.videoElement.srcObject = null;
        }
        this.isStreaming = false;
    }

    captureFrame(targetWidth = 640, targetHeight = 480, quality = 0.85) {
        if (!this.videoElement || !this.isStreaming) {
            return null;
        }

        const canvas = document.createElement('canvas');
        canvas.width = targetWidth;
        canvas.height = targetHeight;
        const ctx = canvas.getContext('2d');

        // Draw current video frame onto canvas
        ctx.drawImage(this.videoElement, 0, 0, targetWidth, targetHeight);
        return canvas.toDataURL('image/jpeg', quality);
    }
}

window.CameraManager = CameraManager;
