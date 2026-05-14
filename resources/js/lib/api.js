const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '';

export async function generateResume(profile, targetRole = null) {
    const response = await fetch(`${API_BASE_URL}/api/resume/generate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify({
            profile,
            target_role: targetRole,
        }),
    });

    if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText || 'Resume generation failed.');
    }

    return response.json();
}
