/**
 * Matcher Module
 * Computes Euclidean distances, evaluates matching thresholds, and resolves student identity
 */
class FaceMatcherManager {
    constructor(threshold = 0.55, minConfidence = 0.45) {
        this.threshold = threshold;
        this.minConfidence = minConfidence;
        this.registeredStudents = [];
        this.isLoaded = false;
    }

    async loadDescriptors(apiUrl = '/face_attendance_system/public/api/face/descriptors') {
        try {
            const resp = await fetch(apiUrl);
            const data = await resp.json();
            if (data.success && Array.isArray(data.students)) {
                this.registeredStudents = data.students;
                this.isLoaded = true;
                return this.registeredStudents.length;
            }
            return 0;
        } catch (err) {
            console.error('Failed to load registered student descriptors:', err);
            return 0;
        }
    }

    setStudents(students) {
        this.registeredStudents = students || [];
        this.isLoaded = true;
    }

    findBestMatch(queryDescriptor) {
        if (!this.registeredStudents || this.registeredStudents.length === 0) {
            return { matched: false, reason: 'No registered face templates in the database.' };
        }

        let bestStudent = null;
        let minDistance = Infinity;

        // Iterate through all students and all of their samples
        for (const student of this.registeredStudents) {
            if (!student.descriptors || student.descriptors.length === 0) continue;

            for (const sampleDesc of student.descriptors) {
                const dist = this.euclideanDistance(queryDescriptor, sampleDesc);
                if (dist < minDistance) {
                    minDistance = dist;
                    bestStudent = student;
                }
            }
        }

        // Check if smallest distance satisfies the match threshold
        if (bestStudent && minDistance <= this.threshold) {
            // Confidence calculation: 1.0 when distance=0, linearly drops to 0 at threshold
            const confidence = Math.max(0, 1 - (minDistance / this.threshold));

            if (confidence < this.minConfidence) {
                return {
                    matched: false,
                    distance: minDistance,
                    confidence: confidence,
                    reason: 'Face match confidence is too low. Please reposition and face the camera directly.'
                };
            }

            return {
                matched: true,
                student: bestStudent,
                distance: minDistance,
                confidence: confidence,
                confidencePercentage: Math.round(confidence * 100),
                reason: `Identified as ${bestStudent.full_name} (${Math.round(confidence * 100)}%)`
            };
        }

        return {
            matched: false,
            distance: minDistance,
            reason: 'Unknown face. Student face has not been registered or does not match.'
        };
    }

    euclideanDistance(v1, v2) {
        if (!v1 || !v2 || v1.length !== v2.length) return Infinity;
        let sum = 0;
        for (let i = 0; i < v1.length; i++) {
            const diff = v1[i] - v2[i];
            sum += diff * diff;
        }
        return Math.sqrt(sum);
    }
}

window.FaceMatcherManager = FaceMatcherManager;
