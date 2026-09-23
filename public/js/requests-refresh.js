(function () {
    const select = document.getElementById('requests-refresh-interval');
    const tbody = document.getElementById('requests-table-body');
    const statusEl = document.getElementById('requests-refresh-status');

    if (!select || !tbody) {
        return;
    }

    const storageKey = 'mock.requestsRefreshSeconds';
    const pollUrl = select.dataset.pollUrl;
    let timerId = null;

    function setStatus(text) {
        if (statusEl) {
            statusEl.textContent = text;
        }
    }

    async function refreshRows() {
        try {
            const response = await fetch(pollUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                setStatus('Refresh failed');

                return;
            }

            tbody.innerHTML = await response.text();
            setStatus('Updated ' + new Date().toLocaleTimeString());
        } catch {
            setStatus('Refresh failed');
        }
    }

    function applyInterval(seconds) {
        if (timerId !== null) {
            clearInterval(timerId);
            timerId = null;
        }

        if (!seconds) {
            setStatus('Auto-refresh off');
            localStorage.removeItem(storageKey);

            return;
        }

        localStorage.setItem(storageKey, String(seconds));
        setStatus('Every ' + seconds + 's');
        timerId = setInterval(refreshRows, seconds * 1000);
    }

    const saved = localStorage.getItem(storageKey);
    if (saved && select.querySelector('option[value="' + saved + '"]')) {
        select.value = saved;
    }

    select.addEventListener('change', function () {
        const seconds = parseInt(select.value, 10);
        applyInterval(Number.isFinite(seconds) && seconds > 0 ? seconds : 0);
    });

    select.dispatchEvent(new Event('change'));
})();
