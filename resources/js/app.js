// Progressive enhancement only:
// navigation, data and mutations work server-side.

const root = document.documentElement;

root.classList.add('js-enabled');


// =========================================================
// LIGHT MODE ONLY
// =========================================================

// Remove any old saved dark-theme preference.
try {
    localStorage.removeItem('talentflow.theme');
} catch {
    // Local storage is optional.
}

// Always keep the dashboard in light mode.
root.dataset.theme = 'light';
root.style.colorScheme = 'light';


// =========================================================
// LOCAL PREFERENCES
// =========================================================

const preferences = {
    read(key) {
        try {
            return localStorage.getItem(
                `talentflow.${key}`
            );
        } catch {
            return null;
        }
    },

    write(key, value) {
        try {
            localStorage.setItem(
                `talentflow.${key}`,
                value
            );
        } catch {
            // Storage is optional.
        }
    },
};


// =========================================================
// SIDEBAR
// =========================================================

const sidebar =
    document.querySelector('#sidebar');

const sidebarToggle =
    document.querySelector(
        '[data-sidebar-toggle]'
    );

const backdrop =
    document.querySelector(
        '[data-sidebar-close]'
    );

const mobileViewport =
    window.matchMedia(
        '(max-width: 760px)'
    );


function syncSidebar() {
    const mobileOpen =
        root.classList.contains(
            'sidebar-open'
        );

    const expanded =
        mobileViewport.matches
            ? mobileOpen
            : !root.classList.contains(
                'sidebar-collapsed'
            );

    sidebarToggle?.setAttribute(
        'aria-expanded',
        String(expanded)
    );

    if (sidebar) {
        sidebar.inert =
            mobileViewport.matches
            && !mobileOpen;
    }

    if (backdrop) {
        backdrop.hidden =
            !(
                mobileViewport.matches
                && mobileOpen
            );
    }
}


if (
    preferences.read('sidebar')
    === 'collapsed'
) {
    root.classList.add(
        'sidebar-collapsed'
    );
}


syncSidebar();


sidebarToggle?.addEventListener(
    'click',
    () => {
        if (mobileViewport.matches) {
            root.classList.toggle(
                'sidebar-open'
            );
        } else {
            root.classList.toggle(
                'sidebar-collapsed'
            );

            preferences.write(
                'sidebar',
                root.classList.contains(
                    'sidebar-collapsed'
                )
                    ? 'collapsed'
                    : 'expanded'
            );
        }

        syncSidebar();

        if (
            mobileViewport.matches
            && root.classList.contains(
                'sidebar-open'
            )
        ) {
            sidebar
                ?.querySelector('a')
                ?.focus();
        }
    }
);


function closeSidebar() {
    root.classList.remove(
        'sidebar-open'
    );

    syncSidebar();

    sidebarToggle?.focus();
}


backdrop?.addEventListener(
    'click',
    closeSidebar
);


mobileViewport.addEventListener(
    'change',
    syncSidebar
);


document.addEventListener(
    'keydown',
    (event) => {
        if (
            event.key === 'Escape'
            && root.classList.contains(
                'sidebar-open'
            )
        ) {
            closeSidebar();
        }

        if (
            event.key === 'Tab'
            && mobileViewport.matches
            && root.classList.contains(
                'sidebar-open'
            )
        ) {
            const controls = [
                sidebarToggle,
                ...sidebar.querySelectorAll(
                    'a[href], button:not(:disabled)'
                ),
            ].filter(Boolean);

            const first =
                controls[0];

            const last =
                controls[
                    controls.length - 1
                ];

            if (
                event.shiftKey
                && document.activeElement
                    === first
            ) {
                event.preventDefault();

                last.focus();
            } else if (
                !event.shiftKey
                && document.activeElement
                    === last
            ) {
                event.preventDefault();

                first.focus();
            }
        }
    }
);


// =========================================================
// LOADING
// =========================================================

const loadingIndicator =
    document.querySelector(
        '.loading-indicator'
    );


function showLoading() {
    if (loadingIndicator) {
        loadingIndicator.hidden = false;
    }

    document
        .querySelector('main')
        ?.setAttribute(
            'aria-busy',
            'true'
        );
}


document
    .querySelectorAll('[data-refresh]')
    .forEach((button) => {
        button.addEventListener(
            'click',
            () => {
                showLoading();

                window.location.reload();
            }
        );
    });


// =========================================================
// CONFIRMATION DIALOG
// =========================================================

const confirmation =
    document.querySelector(
        '#confirmation-dialog'
    );

let pendingConfirmation = null;


document
    .querySelectorAll(
        'form[data-loading-form]'
    )
    .forEach((form) => {
        form.addEventListener(
            'submit',
            (event) => {
                if (
                    form.dataset.submitting
                    === 'true'
                ) {
                    event.preventDefault();

                    return;
                }

                if (
                    form.hasAttribute(
                        'data-confirm-form'
                    )
                    && !form.hasAttribute(
                        'data-confirmed'
                    )
                    && confirmation?.showModal
                ) {
                    event.preventDefault();

                    pendingConfirmation = {
                        form,
                        submitter:
                            event.submitter,
                    };

                    confirmation.showModal();

                    confirmation
                        .querySelector(
                            '[data-confirm-cancel]'
                        )
                        ?.focus();

                    return;
                }

                if (
                    event.defaultPrevented
                ) {
                    return;
                }

                form.dataset.submitting =
                    'true';

                showLoading();

                form
                    .querySelectorAll(
                        'button[type="submit"]'
                    )
                    .forEach(
                        (button) => {
                            button.disabled =
                                true;

                            button.dataset.loading =
                                'true';
                        }
                    );
            }
        );
    });


document
    .querySelector(
        '[data-confirm-cancel]'
    )
    ?.addEventListener(
        'click',
        () => {
            confirmation.close();

            pendingConfirmation
                ?.submitter
                ?.focus();

            pendingConfirmation = null;
        }
    );


confirmation?.addEventListener(
    'cancel',
    () => {
        pendingConfirmation = null;
    }
);


document
    .querySelector(
        '[data-confirm-accept]'
    )
    ?.addEventListener(
        'click',
        () => {
            if (!pendingConfirmation) {
                return;
            }

            const {
                form,
                submitter,
            } = pendingConfirmation;

            pendingConfirmation = null;

            confirmation.close();

            form.setAttribute(
                'data-confirmed',
                'true'
            );

            form.requestSubmit(
                submitter || undefined
            );

            form.removeAttribute(
                'data-confirmed'
            );
        }
    );


// =========================================================
// PASSWORD VISIBILITY
// =========================================================

document
    .querySelectorAll(
        '[data-password-toggle]'
    )
    .forEach((button) => {
        button.addEventListener(
            'click',
            () => {
                const input =
                    document.getElementById(
                        button.getAttribute(
                            'aria-controls'
                        )
                    );

                if (!input) {
                    return;
                }

                const visible =
                    input.type
                    === 'password';

                input.type =
                    visible
                        ? 'text'
                        : 'password';

                button.textContent =
                    visible
                        ? 'Hide'
                        : 'Show';

                button.setAttribute(
                    'aria-label',
                    visible
                        ? 'Hide password'
                        : 'Show password'
                );
            }
        );
    });


// =========================================================
// PAGE CACHE RESTORE
// =========================================================

// Restore controls when a browser brings a completed
// form back from its page cache.
window.addEventListener(
    'pageshow',
    () => {
        if (loadingIndicator) {
            loadingIndicator.hidden =
                true;
        }

        document
            .querySelector('main')
            ?.removeAttribute(
                'aria-busy'
            );

        document
            .querySelectorAll(
                'form[data-submitting]'
            )
            .forEach((form) => {
                delete form.dataset
                    .submitting;
            });

        document
            .querySelectorAll(
                'button[data-loading]'
            )
            .forEach((button) => {
                button.disabled = false;

                delete button.dataset
                    .loading;
            });
    }
);