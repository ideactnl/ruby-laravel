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
                if (window.navigator && window.navigator.vibrate) {
                    window.navigator.vibrate(20);
                }
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
            const trackedText = this.translations.wrapped_tracked
                .replace(':days', `<span class="text-primary font-bold">${this.data.total_tracked_days}</span>`)
                .replace(':day_word', this.dayWord(this.data.total_tracked_days));

            return this.translations.wrapped_header
                .replace(':start', `<span class="text-primary font-bold">${this.formatDate(this.data.start_date)}</span>`)
                .replace(':end', `<span class="text-primary font-bold">${this.formatDate(this.data.end_date)}</span>`)
                .replace(':tracked_text', trackedText);
        },
        getCycleLengthText() {
            if (!this.data || !this.data.can_calculate) return '';

            return this.translations.wrapped_cycle_length
                .replace(':days', `<span class="text-primary font-bold text-gray-900">${this.data.cycle_length}</span>`)
                .replace(':day_word', this.dayWord(this.data.cycle_length));
        },
        getBloodLossText() {
            if (!this.data) return '';

            return this.translations.wrapped_blood_loss_spotting
                .replace(':blood_days', `<span class="text-primary font-bold text-gray-900">${this.data.blood_loss_days}</span>`)
                .replace(':blood_day_word', this.dayWord(this.data.blood_loss_days))
                .replace(':spotting_days', `<span class="text-primary font-bold text-gray-900">${this.data.spotting_days}</span>`)
                .replace(':spotting_day_word', this.dayWord(this.data.spotting_days));
        },
        getPainText() {
            if (!this.data) return '';

            return this.translations.wrapped_pain_extreme
                .replace(':pain_days', `<span class="text-primary font-bold text-gray-900">${this.data.pain_days}</span>`)
                .replace(':pain_day_word', this.dayWord(this.data.pain_days))
                .replace(':extreme_days', `<span class="text-primary font-bold text-gray-900">${this.data.extreme_pain_days}</span>`)
                .replace(':extreme_day_word', this.dayWord(this.data.extreme_pain_days));
        },
        getImpactText() {
            if (!this.data) return '';

            return this.translations.wrapped_impact
                .replace(':days', `<span class="text-primary font-bold text-gray-900">${this.data.impact_days}</span>`)
                .replace(':day_word', this.dayWord(this.data.impact_days));
        },
        getTrackedDaysText() {
            if (!this.data) return '';

            return this.translations.wrapped_tracked
                .replace(':days', `<span class="text-primary font-bold text-gray-900">${this.data.total_tracked_days}</span>`)
                .replace(':day_word', this.dayWord(this.data.total_tracked_days));
        },
        dayWord(count) {
            const n = Number(count) || 0;
            return n === 1 ? this.translations.wrapped_day_singular : this.translations.wrapped_day_plural;
        }
    }
}