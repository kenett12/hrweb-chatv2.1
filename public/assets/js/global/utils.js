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

    // 2. Global Preloader logic for navigation
    const loader = document.getElementById('globalPreloader');

    // Hide the preloader when the page fully loads
    window.addEventListener('load', () => {
        if (loader) {
            loader.classList.add('hidden');
            // Completely remove from DOM after CSS transition matches (0.5s)
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    });

    // Show the preloader on valid internal anchor clicks
    document.querySelectorAll('a').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const el = e.currentTarget;
            const targetAttr = el.getAttribute('target');
            const hrefAttr = el.getAttribute('href');
            const onclickAttr = el.getAttribute('onclick');

            if (
                hrefAttr &&
                hrefAttr !== '#' &&
                hrefAttr.trim() !== '' &&
                !hrefAttr.startsWith('javascript:') &&
                targetAttr !== '_blank' &&
                !e.ctrlKey &&
                !e.metaKey &&
                (!onclickAttr || !onclickAttr.includes('confirmAction'))
            ) {
                // Return preloader to block screen visually before navigation
                if (loader) {
                    loader.style.display = 'flex';
                    // Tick delay to ensure display:flex registers before unhiding opacity
                    setTimeout(() => {
                        loader.classList.remove('hidden');
                    }, 10);
                }
            }
        });
    });

    // 3. Form Submit Button Loading State & Global Confirmation
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const formEl = this;
            const submitBtn = formEl.querySelector('button[type="submit"], input[type="submit"]');

            // Skip forms with `.no-confirm` or specific IDs that shouldn't prompt
            const fId = (formEl.id || '').toLowerCase();
            const excludeWords = ['chat', 'reply', 'login', 'forgot', 'reset', 'search', 'filter'];
            const isExcluded = formEl.classList.contains('no-confirm') ||
                excludeWords.some(w => fId.includes(w));

            const method = (formEl.getAttribute('method') || '').toUpperCase();

            // Function to handle loading state visuals
            const setLoadingState = () => {
                if (submitBtn && !submitBtn.classList.contains('btn-loading')) {
                    submitBtn.style.width = submitBtn.offsetWidth + 'px';
                    submitBtn.style.height = submitBtn.offsetHeight + 'px';
                    submitBtn.classList.add('btn-loading');
                    submitBtn.setAttribute('data-original-text', submitBtn.innerHTML);
                }
            };

            // Only prompt for POST forms that aren't excluded
            if (method === 'POST' && !isExcluded) {
                // If the user already confirmed via SweetAlert
                if (formEl.dataset.confirmed === 'true') {
                    setLoadingState();
                    return; // Let native submission proceed
                }

                // Pause submission to ask for confirmation
                e.preventDefault();

                // 🚨 BUG FIX: Ensure HTML5 Validation runs first!
                if (!formEl.checkValidity()) {
                    formEl.reportValidity(); // Shows native browser tooltips
                    return; // Stop here, do not show SweetAlert
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Confirm Submission?',
                        text: 'Are you sure you want to proceed and save this entry?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3297ca',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: '<span class="font-bold tracking-wider uppercase text-xs">Yes, Proceed</span>',
                        cancelButtonText: '<span class="font-bold tracking-wider uppercase text-xs">Review</span>',
                        customClass: {
                            popup: 'rounded-3xl',
                            confirmButton: 'rounded-xl px-6 py-3',
                            cancelButton: 'rounded-xl px-6 py-3'
                        },
                        didOpen: () => {
                            document.querySelector('.swal2-container').style.zIndex = '9999';
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formEl.dataset.confirmed = 'true'; // Set flag
                            setLoadingState(); // UI feedback
                            formEl.submit(); // Manually submit
                        }
                    });
                } else {
                    // Fallback to native browser alert
                    if (confirm('Are you sure you want to proceed with this entry?')) {
                        formEl.dataset.confirmed = 'true';
                        setLoadingState();
                        formEl.submit();
                    }
                }
            } else {
                // For GET forms or excluded forms, just apply the loading state
                setLoadingState();
            }
        });
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

/**
 * Universal SweetAlert2 Confirmation Action
 * Used to intercept clicks and visually confirm before proceeding.
 * @param {Event} e - The click event to prevent default behavior
 * @param {string} url - The URL to redirect to upon confirmation
 * @param {string} title - The title of the modal (e.g., 'Are you sure?')
 * @param {string} text - The subtext warning
 * @param {string} confirmBtnText - The text on the confirm button
 * @param {string} confirmBtnColor - The hex color of the confirm button
 */
function confirmAction(e, url, title, text, confirmBtnText = 'Yes, continue', confirmBtnColor = '#1e72af') {
    e.preventDefault();

    // Check if Swal is loaded globally (it should be in main_layout)
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title || 'Are you sure?',
            text: text || "This action requires confirmation.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmBtnColor,
            cancelButtonColor: '#9ca3af',
            confirmButtonText: `<span class="font-bold tracking-wider uppercase text-xs">${confirmBtnText}</span>`,
            cancelButtonText: '<span class="font-bold tracking-wider uppercase text-xs">Cancel</span>',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl px-6 py-3',
                cancelButton: 'rounded-xl px-6 py-3'
            },
            didOpen: () => {
                document.querySelector('.swal2-container').style.zIndex = '9999';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Return preloader before redirecting
                const loader = document.getElementById('globalPreloader');
                if (loader) {
                    loader.style.display = 'flex';
                    setTimeout(() => {
                        loader.classList.remove('hidden');
                    }, 10);
                }

                // Proceed with the action
                window.location.href = url;
            }
        });
    } else {
        // Fallback to native browser confirm if SweetAlert fails to load
        if (confirm(`${title}\n\n${text}`)) {
            window.location.href = url;
        }
    }
}