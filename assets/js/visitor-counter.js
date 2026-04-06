// Visitor Counter — all counting is done server-side
// The client only reads data and sends a "record visit" POST.
class VisitorCounter {
    constructor() {
        this.apiEndpoint = './assets/api/visitor-api.php';
        this.updateInterval = 30000; // Poll every 30 seconds for display updates
        this.init();
    }

    async init() {
        await this.recordVisit();
        await this.loadAndDisplay();
        this.startPolling();
    }

    /**
     * Record a visit via POST — the server handles incrementing.
     * Uses sessionStorage so we only count once per browser session.
     */
    async recordVisit() {
        if (sessionStorage.getItem('visitRecorded')) {
            return; // Already recorded this session
        }

        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'record_visit' })
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data) {
                    this.data = result.data;
                    this.updateDisplay();
                }
                sessionStorage.setItem('visitRecorded', 'true');
            }
        } catch (error) {
            console.error('Error recording visit:', error);
        }
    }

    /**
     * Fetch current stats from server (GET) and update the display.
     */
    async loadAndDisplay() {
        try {
            const response = await fetch(this.apiEndpoint);
            if (response.ok) {
                this.data = await response.json();
                this.updateDisplay();
            }
        } catch (error) {
            console.error('Error loading visitor data:', error);
        }
    }

    /**
     * Poll the server periodically to keep values up-to-date
     * (e.g. if another visitor arrives while you're on the page)
     */
    startPolling() {
        setInterval(async () => {
            try {
                const response = await fetch(this.apiEndpoint);
                if (response.ok) {
                    const newData = await response.json();
                    if (JSON.stringify(newData) !== JSON.stringify(this.data)) {
                        this.data = newData;
                        this.updateDisplay();
                    }
                }
            } catch (error) {
                console.error('Error polling visitor data:', error);
            }
        }, this.updateInterval);
    }

    updateDisplay() {
        if (!this.data) return;

        this.animateCounter('total-visits', this.data.totalVisits || 0);
        this.animateCounter('daily-visits', this.data.dailyVisits || 0);

        if (this.data.lastVisit) {
            const el = document.getElementById('last-visit');
            if (el) {
                // lastVisit is already in Sri Lankan time (e.g. "2026-04-06 14:30:00")
                el.textContent = this.formatSLTime(this.data.lastVisit);
            }
        }
    }

    animateCounter(elementId, targetValue) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const currentValue = parseInt(element.textContent.replace(/,/g, '')) || 0;

        if (currentValue === targetValue) {
            element.textContent = targetValue.toLocaleString();
            return;
        }

        const duration = 1500;
        const steps = 60;
        const increment = (targetValue - currentValue) / steps;
        let current = currentValue;
        let step = 0;

        const timer = setInterval(() => {
            step++;
            current += increment;

            if (step >= steps || Math.abs(current - targetValue) < 1) {
                element.textContent = targetValue.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, duration / steps);
    }

    /**
     * Format a Sri Lankan time string "YYYY-MM-DD HH:MM:SS" for display.
     */
    formatSLTime(dateStr) {
        // Parse as local components (the server already gives Sri Lankan time)
        const parts = dateStr.match(/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/);
        if (!parts) return dateStr;

        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const year  = parts[1];
        const month = months[parseInt(parts[2], 10) - 1];
        const day   = parseInt(parts[3], 10);
        const hour  = parseInt(parts[4], 10);
        const min   = parts[5];

        const ampm  = hour >= 12 ? 'PM' : 'AM';
        const h12   = hour % 12 || 12;

        return `${month} ${day}, ${year}, ${h12}:${min} ${ampm}`;
    }

    getData() {
        return this.data;
    }

    async refresh() {
        await this.loadAndDisplay();
    }
}

// Initialize counter when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.visitorCounter = new VisitorCounter();
});
