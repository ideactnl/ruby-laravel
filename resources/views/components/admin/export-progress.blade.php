@props([
    'type' => 'csv',
])

<script>
if (!window.AdminExportProgressComponent) {
window.AdminExportProgressComponent = function () {
  const endpoints = {
    queue: '/pbac/export/queue',
    active: '/pbac/exports/active',
    status: (id) => `/pbac/exports/${id}`,
  };
  return {
    isBusy: false,
    job: null,
    progress: 0,
    status: null,
    error: '',
    timer: null,

    csrfHeader() {
      const meta = document.querySelector('meta[name="csrf-token"]');
      if (meta && meta.content) return { 'X-CSRF-TOKEN': meta.content };
      const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
      const token = match ? decodeURIComponent(match[1]) : null;
      return token ? { 'X-XSRF-TOKEN': token } : {};
    },

    async requestJson(url, init = {}){
      const headers = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(init.headers || {}),
      };
      const res = await fetch(url, { ...init, headers, credentials: 'include' });
      const ct = res.headers.get('content-type') || '';
      let payload = null;
      try {
        payload = ct.includes('application/json') ? await res.json() : { error: await res.text() };
      } catch(e){ payload = { error: e.message || 'Unknown error' }; }
      return { res, payload };
    },

    downloadUrl(){ return (this.job && this.job.download_url) ? this.job.download_url : null; },
    expiresLabel(){
      if (!this.job || !this.job.download_expires_at) return '';
      const dt = new Date(this.job.download_expires_at);
      if (isNaN(dt.getTime())) return '';
      const mins = Math.max(0, Math.round((dt - new Date())/60000));
      if (mins <= 0) return 'Link expired';
      if (mins <= 60) return `Link expires in ${mins} min`;
      return 'Link expires at ' + dt.toLocaleTimeString();
    },
    statusClass(){
      const base = 'bg-primary/10 text-primary border-primary/30';
      const strong = 'bg-primary/15 text-primary border-primary/40';
      switch (this.status) {
        case 'queued': return base;
        case 'processing': return strong;
        case 'completed': return base;
        case 'failed': return 'bg-red-50 text-primary border-red-200';
        default: return base;
      }
    },

    async start(preset, start, end){
      try {
        this.error='';
        if (this.isBusy) return;
        this.isBusy=true;
        this.$el.dispatchEvent(new CustomEvent('export:busy', { bubbles:true }));
        const payload = { preset, start_date:start, end_date:end, format: (document.getElementById('format')?.value || 'csv') };
        const { res, payload: body } = await this.requestJson(endpoints.queue, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', ...this.csrfHeader() },
          body: JSON.stringify(payload),
        });
        if (!res.ok) throw new Error(body?.error || `Request failed (${res.status})`);
        const data = body || {};
        this.job = data.job || data;
        this.progress = this.job.progress || 0;
        this.status = this.job.status || 'queued';
        window.dispatchEvent(new CustomEvent('alert:success', { detail: { key: this.job?.id || 'export', title: 'Export', message: 'Queued.' } }));
        this.poll();
      } catch(e){
        this.error = e.message || 'Failed to queue export';
        this.isBusy=false;
        this.$el.dispatchEvent(new CustomEvent('export:idle', { bubbles:true }));
        window.dispatchEvent(new CustomEvent('alert:error', { detail: { message: this.error } }));
      }
    },

    async poll(){
      if (!this.job || !this.job.id) { this.isBusy=false; return; }
      try {
        const { res, payload } = await this.requestJson(endpoints.status(this.job.id));
        if (res.ok && payload){
          const j = payload.job || payload;
          this.job=j;
          this.progress=j.progress||0;
          this.status=j.status;
          if (j.status==='completed' || j.status==='failed'){
            this.isBusy=false;
            this.$el.dispatchEvent(new CustomEvent('export:idle', { bubbles:true, detail: { status:j.status } }));
            if (j.status==='completed') {
              window.dispatchEvent(new CustomEvent('alert:success', { detail:{ key: this.job?.id || 'export', title: 'Export', message:'Ready.' } }));
              window.dispatchEvent(new CustomEvent('exports:completed', { detail: { job: j } }));
            }
            else window.dispatchEvent(new CustomEvent('alert:error', { detail:{ key: this.job?.id || 'export', title: 'Export', message:'Failed.' } }));
            return;
          }
        }
      } catch(_){}
      this.timer = setTimeout(()=>this.poll(), 2000);
    },

    async resume(){
      try {
        const { res, payload } = await this.requestJson(endpoints.active);
        if (res.ok && payload && payload.job){
          this.job = payload.job;
          this.progress = payload.job.progress || 0;
          this.status = payload.job.status;
          this.isBusy=true;
          this.poll();
        }
      } catch(_){}
    },

    init(){ this.resume(); },
    handleStart(evt){
      const d = evt?.detail || {};
      if (!d) return;
      this.start(d.preset, d.start, d.end);
    }
  }
};
}
</script>

<div x-data="AdminExportProgressComponent()" @export:start.window="handleStart($event)" class="w-full">
  <template x-if="job">
    <div class="mt-2">
      <div class="w-full bg-gray-200/70 rounded-full overflow-hidden h-[6px]">
        <div class="bg-primary h-[6px] transition-all duration-300" :style="'width:' + progress + '%'"
        ></div>
      </div>
      <div class="flex items-center justify-between mt-2">
        <span class="inline-flex items-center gap-2">
          <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] sm:text-xs font-medium" :class="statusClass()" x-text="(status||'processing').replace(/^./, c=>c.toUpperCase())"></span>
          <span class="text-[10px] sm:text-xs text-gray-500" x-text="progress + '%'" aria-label="Progress"></span>
        </span>
        <div class="flex items-center gap-3" x-show="job && status === 'completed' && downloadUrl()">
          <a :href="downloadUrl()" class="text-[11px] sm:text-xs inline-flex items-center gap-1 text-primary hover:underline hover:text-primary-800 transition-colors">
            <i class="fa-solid fa-download"></i>
            Download
          </a>
          <span class="text-[10px] sm:text-xs text-gray-500" x-text="expiresLabel()"></span>
        </div>
      </div>
    </div>
  </template>

  <template x-if="!job && !error">
    <div class="text-center text-gray-500 py-6">
      <div class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-100 mb-2">
        <i class="fa-solid fa-cloud-arrow-up text-gray-500"></i>
      </div>
      <div class="text-sm">No active export. Queue one to see progress here.</div>
    </div>
  </template>

  <template x-if="error">
    <p class="text-xs text-red-600 mt-2" x-text="error"></p>
  </template>
</div>
