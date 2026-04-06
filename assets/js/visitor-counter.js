// Visitor Counter with Real-Time JSON File Updates
class VisitorCounter {
    constructor() {
        this.apiEndpoint = './assets/api/visitor-api.php';
        this.updateInterval = 5000; // Update every 5 seconds
        this.init();
    }

    async init() {
        await this.loadData();
        await this.updateVisit();
        this.updateDisplay();
        this.startRealTimeUpdates();
    }

    async loadData() {
        try {
            const response = await fetch(this.apiEndpoint);
            if (response.ok) {
                this.data = await response.json();
            } else {
                this.data = this.getDefaultData();
            }
        } catch (error) {
            console.error('Error loading visitor data:', error);
            this.data = this.getDefaultData();
        }
    }

    getDefaultData() {
        return {
            totalVisits: 716,
            dailyVisits: 0,
            lastVisit: null,
            lastVisitDate: null,
            firstVisit: new Date().toISOString()
        };
    }

    async updateVisit() {
        // Check if this is a new visit (using sessionStorage)
        if (!sessionStorage.getItem('currentVisit')) {
            this.data.totalVisits++;
            
            const today = new Date().toDateString();
            if (this.data.lastVisitDate !== today) {
                this.data.dailyVisits = 1;
                this.data.lastVisitDate = today;
            } else {
                this.data.dailyVisits++;
            }
            
            this.data.lastVisit = new Date().toISOString();
            
            await this.saveData();
            sessionStorage.setItem('currentVisit', 'true');
        }
    }

    async saveData() {
        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.data)
            });
            
            if (response.ok) {
                const result = await response.json();
                console.log('Data saved to JSON file:', result);
            } else {
                console.error('Failed to save data:', response.statusText);
            }
        } catch (error) {
            console.error('Error saving visitor data:', error);
        }
    }

    async startRealTimeUpdates() {
        // Poll for updates from the JSON file
        setInterval(async () => {
            try {
                const response = await fetch(this.apiEndpoint);
                if (response.ok) {
                    const newData = await response.json();
                    
                    // Only update if data has changed
                    if (JSON.stringify(newData) !== JSON.stringify(this.data)) {
                        this.data = newData;
                        this.updateDisplay();
                        console.log('Data updated from server:', newData);
                    }
                }
            } catch (error) {
                console.error('Error fetching updates:', error);
            }
        }, this.updateInterval);
    }

    updateDisplay() {
        this.animateCounter('total-visits', this.data.totalVisits);
        this.animateCounter('daily-visits', this.data.dailyVisits);
        
        if (this.data.lastVisit) {
            const lastVisitElement = document.getElementById('last-visit');
            if (lastVisitElement) {
                const lastVisitDate = new Date(this.data.lastVisit);
                lastVisitElement.textContent = this.formatDate(lastVisitDate);
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

    formatDate(date) {
        const options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleDateString('en-US', options);
    }

    // Export current data
    getData() {
        return this.data;
    }

    // Force refresh from server
    async refresh() {
        await this.loadData();
        this.updateDisplay();
    }

    // Reset counter (for testing)
    async reset() {
        this.data = this.getDefaultData();
        await this.saveData();
        sessionStorage.removeItem('currentVisit');
        location.reload();
    }
}

// Initialize counter when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.visitorCounter = new VisitorCounter();
});
