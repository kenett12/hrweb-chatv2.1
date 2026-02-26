/**
 * HRWeb Chat - Global Utilities
 * Logic shared across Admin, TSR, and Client roles.
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('HRWeb Global Logic Initialized');

    // 1. Auto-dismiss Flash Messages for a clean UI
    const alerts = document.querySelectorAll('.alert-auto-dismiss');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});

/**
 * Toggle Modal Visibility
 * Used by the 'Add TSR' and 'Add Client' buttons.
 * @param {string} modalId 
 */
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        // Switch between hidden and flex (centered)
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }
}

/**
 * Simple Logger for future Update Tracking
 */
function logAction(role, message) {
    console.log(`[${role.toUpperCase()}]: ${message}`);
}