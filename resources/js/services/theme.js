import axios from 'axios';

class ThemeService {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'LIGHT';
        this.applyTheme();
    }

    async loadTheme() {
        try {
            const token = localStorage.getItem('token');
            if (!token) return;

            const response = await axios.get('/api/preferences', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.data && response.data.theme) {
                this.setTheme(response.data.theme);
            }
        } catch (error) {
            console.error('Erro ao carregar tema:', error);
        }
    }

    setTheme(theme) {
        this.theme = theme;
        localStorage.setItem('theme', theme);
        this.applyTheme();
    }

    getTheme() {
        return this.theme;
    }

    applyTheme() {
        if (this.theme === 'DARK') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    toggleTheme() {
        const newTheme = this.theme === 'LIGHT' ? 'DARK' : 'LIGHT';
        this.setTheme(newTheme);
        return newTheme;
    }
}

export const themeService = new ThemeService(); 