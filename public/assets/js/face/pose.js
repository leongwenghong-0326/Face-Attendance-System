/**
 * Pose Module
 * Analyzes facial landmarks to ensure front-facing pose (checks head tilt and yaw)
 */
class PoseChecker {
    static checkFrontal(landmarks) {
        if (!landmarks || !landmarks.positions || landmarks.positions.length < 68) {
            return { isFrontal: true, roll: 0, yaw: 0 };
        }

        const pts = landmarks.positions;

        // Left eye outer corner (pt 36), Right eye outer corner (pt 45)
        const leftEye = pts[36];
        const rightEye = pts[45];
        const noseTip = pts[30];

        // 1. Roll (Head tilt angle in degrees)
        const dy = rightEye.y - leftEye.y;
        const dx = rightEye.x - leftEye.x;
        const rollRad = Math.atan2(dy, dx);
        const rollDeg = (rollRad * 180) / Math.PI;

        // 2. Yaw (Horizontal turn ratio)
        // Ratio of distance from nose to left eye vs nose to right eye
        const distToLeft = Math.abs(noseTip.x - leftEye.x);
        const distToRight = Math.abs(rightEye.x - noseTip.x);
        const ratio = distToLeft / (distToRight || 1);

        const isTiltOk = Math.abs(rollDeg) <= 18;
        const isTurnOk = ratio >= 0.55 && ratio <= 1.85;

        const isFrontal = isTiltOk && isTurnOk;

        let reason = 'Front-facing pose verified.';
        if (!isTiltOk) {
            reason = 'Please keep your head upright and level.';
        } else if (!isTurnOk) {
            reason = 'Please look directly at the camera.';
        }

        return {
            isFrontal: isFrontal,
            roll: Math.round(rollDeg),
            ratio: parseFloat(ratio.toFixed(2)),
            reason: reason
        };
    }
}

window.PoseChecker = PoseChecker;
