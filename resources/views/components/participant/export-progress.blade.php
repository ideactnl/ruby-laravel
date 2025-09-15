@props([
    'type' => 'csv',
    'chartCanvasId' => 'exportChart',
])

<script>
    if (!window.RubyExportProgressComponent) {
    window.RubyExportProgressComponent = function (opts) {
        const endpoints = {
            csv: (params) => `/api/v1/participant/pbac/export?${params}`,
            pdf: '/api/v1/participant/pbac/chart/export/pdf',
            active: '/api/v1/participant/exports/active',
            status: (id) => `/api/v1/participant/exports/${id}`,
        };
        return {
            type: opts?.type || 'csv',
            chartCanvasId: opts?.chartCanvasId || 'exportChart',
            isBusy: false,
            job: null,
            progress: 0,
            status: null,
            error: '',
            timer: null,
            downloadUrl(){ return (this.job && this.job.download_url) ? this.job.download_url : null; },
            expiresLabel(){
                if (!this.job || !this.job.download_expires_at) return '';
                const dt = new Date(this.job.download_expires_at);
                if (isNaN(dt.getTime())) return '';
                const now = new Date();
                const mins = Math.max(0, Math.round((dt - now) / 60000));
                if (mins <= 0) return 'Link expired';
                if (mins <= 60) return `Link expires in ${mins} min`;

                return 'Link expires at ' + dt.toLocaleTimeString();
            },
            displayStatus(){
                const s = (this.status || '').toString();
                if (!s) return 'Processing';
                return s.charAt(0).toUpperCase() + s.slice(1);
            },
            statusClass(){
                const base = 'bg-[#5E0F0F]/10 text-[#5E0F0F] border-[#5E0F0F]/30';
                const strong = 'bg-[#5E0F0F]/15 text-[#5E0F0F] border-[#5E0F0F]/40';
                switch (this.status) {
                    case 'queued': return base;
                    case 'processing': return strong;
                    case 'completed': return base;
                    case 'failed': return 'bg-red-50 text-[#5E0F0F] border-red-200';
                    default: return base;
                }
            },

            csrfHeader() {
                const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
                const token = match ? decodeURIComponent(match[1]) : null;
                return token ? { 'X-XSRF-TOKEN': token } : {};
            },

            async start(preset, start, end) {
                try {
                    this.error = '';
                    if (this.isBusy) return;
                    this.isBusy = true;
                    this.$el.dispatchEvent(new CustomEvent('export:busy', { bubbles: true, detail: { type: this.type } }));

                    let res, data;
                    if (this.type === 'csv') {
                        const qp = new URLSearchParams();
                        if (preset) qp.set('preset', preset);
                        if (start) qp.set('start_date', start);
                        if (end) qp.set('end_date', end);
                        res = await fetch(endpoints.csv(qp.toString()), { credentials: 'include' });
                    } else {
                        const canvas = document.getElementById(this.chartCanvasId);
                        const chartImage = canvas ? canvas.toDataURL('image/png') : null;
                        res = await fetch(endpoints.pdf, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', ...this.csrfHeader() },
                            credentials: 'include',
                            body: JSON.stringify({ chart_image: chartImage, preset, start_date: start, end_date: end })
                        });
                    }
                    data = await res.json();
                    if (!res.ok) throw new Error(data?.error || 'Failed to queue export');

                    this.job = data.job || data;
                    this.progress = this.job.progress || 0;
                    this.status = this.job.status || 'queued';
                    // Emit alert for queued
                    window.dispatchEvent(new CustomEvent('alert:success', { detail: { message: (this.type === 'csv' ? 'CSV' : 'PDF') + ' export queued.' } }));
                    this.poll();
                } catch (e) {
                    this.error = e.message || 'Failed to queue export';
                    this.isBusy = false;
                    this.$el.dispatchEvent(new CustomEvent('export:idle', { bubbles: true, detail: { type: this.type } }));
                    window.dispatchEvent(new CustomEvent('alert:error', { detail: { message: this.error } }));
                }
            },

            async poll() {
                if (!this.job || !this.job.id) { this.isBusy = false; return; }
                try {
                    const res = await fetch(endpoints.status(this.job.id), { credentials: 'include' });
                    if (res.ok) {
                        const payload = await res.json();
                        const j = payload.job || payload;
                        this.job = j;
                        this.progress = j.progress || 0;
                        this.status = j.status;
                        if (j.status === 'completed' || j.status === 'failed') {
                            this.isBusy = false;
                            this.$el.dispatchEvent(new CustomEvent('export:idle', { bubbles: true, detail: { type: this.type, status: j.status } }));
                            if (j.status === 'completed') {
                                window.dispatchEvent(new CustomEvent('alert:success', { detail: { message: (this.type === 'csv' ? 'CSV' : 'PDF') + ' export is ready.' } }));
                            } else if (j.status === 'failed') {
                                window.dispatchEvent(new CustomEvent('alert:error', { detail: { message: (this.type === 'csv' ? 'CSV' : 'PDF') + ' export failed.' } }));
                            }
                            return;
                        }
                    }
                } catch(_) {}
                this.timer = setTimeout(() => this.poll(), 2000);
            },

            async resume() {
                try {
                    const res = await fetch(endpoints.active, { credentials: 'include' });
                    if (res.ok) {
                        const data = await res.json();
                        if (data.job) {
                            this.job = data.job;
                            this.progress = data.job.progress || 0;
                            this.status = data.job.status;
                            this.isBusy = true;
                            this.poll();
                        }
                    }
                } catch(_) {}
            },

            init() { this.resume(); },
            handleStart(evt){
                try {
                    const d = evt && evt.detail ? evt.detail : {};
                    if (!d || d.type !== this.type) return;
                    this.start(d.preset, d.start, d.end);
                } catch(_) {}
            },
        };
    };
    }
</script>

<div
    x-data="RubyExportProgressComponent({ type: '{{ $type }}', chartCanvasId: '{{ $chartCanvasId }}' })"
    @export:start.window="handleStart($event)"
    class="w-full"
>
    <template x-if="job">
        <div class="mt-2">
            <div class="w-full bg-gray-200/70 rounded-full overflow-hidden h-[6px]">
                <div class="bg-[#5E0F0F] h-[6px] transition-all duration-300"
                    :style="'width:' + progress + '%'"
                ></div>
            </div>
            <div class="flex items-center justify-between mt-1.5">
                <span class="inline-flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] sm:text-xs font-medium"
                          :class="statusClass()"
                          x-text="displayStatus()"></span>
                    <span class="text-[10px] sm:text-xs text-gray-500" x-text="progress + '%'" aria-label="Progress"></span>
                </span>
                <div class="flex items-center gap-3" x-show="job && status === 'completed' && downloadUrl()">
                    <a :href="downloadUrl()" class="text-[11px] sm:text-xs inline-flex items-center gap-1 text-[#5E0F0F] hover:underline">
                        <i class="fa-solid fa-download"></i>
                        Download
                    </a>
                    <span class="text-[10px] sm:text-xs text-gray-500" x-text="expiresLabel()"></span>
                </div>
            </div>
        </div>
    </template>

    <template x-if="error">
        <p class="text-xs text-red-600 mt-2" x-text="error"></p>
    </template>
</div>
