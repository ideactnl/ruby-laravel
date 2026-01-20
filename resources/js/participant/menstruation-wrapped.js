export const menstruationWrapped = (config) => {
    return {
        loading: true,
        data: null,
        translations: config.translations,
        selectedDomains: [],
        activeTooltip: null,
        init() {
            this.fetchData();
        },
        toggleTooltip(id) {
            if (this.activeTooltip === id) {
                this.activeTooltip = null;
            } else {
                this.activeTooltip = id;
            }
        },
        async fetchData() {
            try {
                const response = await fetch('/api/v1/participant/menstruation-wrapped');
                if (response.ok) {
                    this.data = await response.json();
                    window.dispatchEvent(new CustomEvent('wrapped-data-loaded', { detail: this.data }));
                }
            } catch (error) {
                console.error('Failed to fetch menstruation wrapped data:', error);
            } finally {
                this.loading = false;
            }
        },
        formatDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            return `${day}-${month}`;
        },
        getHeaderText() {
            if (!this.data || !this.data.can_calculate) return '';
            return this.translations.wrapped_header
                .replace(':start', `<span class="text-primary">${this.formatDate(this.data.start_date)}</span>`)
                .replace(':end', `<span class="text-primary">${this.formatDate(this.data.end_date)}</span>`);
        },
        getCycleLengthText() {
            if (!this.data || !this.data.can_calculate) return '';
            return this.translations.wrapped_cycle_length
                .replace(':days', `<span class="text-primary font-bold text-gray-900">${this.data.cycle_length}</span>`);
        },
        getBloodLossText() {
            if (!this.data) return '';
            return this.translations.wrapped_blood_loss_spotting
                .replace(':blood_days', `<span class="text-primary font-bold text-gray-900">${this.data.blood_loss_days}</span>`)
                .replace(':spotting_days', `<span class="text-primary font-bold text-gray-900">${this.data.spotting_days}</span>`);
        },
        getPainText() {
            if (!this.data) return '';
            return this.translations.wrapped_pain_extreme
                .replace(':pain_days', `<span class="text-primary font-bold text-gray-900">${this.data.pain_days}</span>`)
                .replace(':extreme_days', `<span class="text-primary font-bold text-gray-900">${this.data.extreme_pain_days}</span>`);
        },
        getImpactText() {
            if (!this.data) return '';
            return this.translations.wrapped_impact
                .replace(':days', `<span class="text-primary font-bold text-gray-900">${this.data.impact_days}</span>`);
        }
    }
}