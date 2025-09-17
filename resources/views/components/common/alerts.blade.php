<div x-data="toastCenter()"
     x-init="init()"
     class="absolute top-4 right-4 sm:right-6 z-[1100] space-y-2 w-[calc(100%-2rem)] sm:w-80">

    <template x-for="t in toasts" :key="t.id">
        <div x-show="true" x-transition.opacity x-transition.scale.origin.top.right
             :class="wrapperClass(t.type)"
             class="relative rounded-lg shadow-md border overflow-hidden bg-white">
            <div class="flex items-start gap-2.5 p-3">
                <div :class="iconWrapClass(t.type)" class="h-6 w-6 rounded-md flex items-center justify-center">
                    <i :class="iconClass(t.type)" class="text-white text-[10px]"></i>
                </div>
                <div class="flex-1 text-xs text-gray-800 leading-snug">
                    <p class="font-medium" x-text="t.title"></p>
                    <p class="mt-0.5 text-gray-600" x-text="t.message"></p>
                </div>
                <button @click="remove(t.id)" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
function toastCenter(){
    return {
        toasts: [],
        nextId: 1,
        duration: 3000,
        init(){
            @if (session('success'))
                this.push('success', 'Success', @json(session('success')));
            @endif
            @if (session('error'))
                this.push('error', 'Error', @json(session('error')));
            @endif
            @if ($errors->any())
                this.push('warning', 'Validation Errors', @json(implode("\n", $errors->all())));
            @endif

            window.addEventListener('alert:success', e=> this.push('success', e.detail?.title || 'Success', e.detail?.message || 'Success', e.detail?.key));
            window.addEventListener('alert:error', e=> this.push('error', e.detail?.title || 'Error', e.detail?.message || 'Something went wrong', e.detail?.key));
            window.addEventListener('alert:info', e=> this.push('info', e.detail?.title || 'Info', e.detail?.message || 'Heads up', e.detail?.key));
            window.addEventListener('alert:warning', e=> this.push('warning', e.detail?.title || 'Warning', e.detail?.message || 'Please check', e.detail?.key));
        },
        push(type, title, message, logicalKey){
            const keyBase = logicalKey ? `${type}|k:${logicalKey}` : `${type}|${(message||'').trim().toLowerCase()}`;
            window.__toast_seen = window.__toast_seen || {};
            const now = Date.now();
            const seenAt = window.__toast_seen[keyBase] || 0;
            if (now - seenAt < 3000){
                const existing = this.toasts.find(x => (logicalKey ? `${x.type}|k:${x.key}` : `${x.type}|${(x.message||'').trim().toLowerCase()}`) === keyBase);
                if (existing){ existing.created = now; existing.progress = 0; existing.title = title; existing.message = message; return; }
            }
            const id = this.nextId++;
            const t = { id, type, title, message, key: logicalKey || null, created: Date.now(), progress: 0 };
            this.toasts.push(t);
            this.tick(id);
            window.__toast_seen[keyBase] = now;
        },
        tick(id){
            const timer = setInterval(()=>{
                const t = this.toasts.find(x=>x.id===id);
                if(!t){ clearInterval(timer); return; }
                const elapsed = Date.now() - t.created;
                t.progress = Math.min(100, (elapsed/this.duration)*100);
                if (elapsed >= this.duration){ this.remove(id); clearInterval(timer); }
            }, 100);
        },
        remove(id){ this.toasts = this.toasts.filter(t=>t.id!==id); },
        wrapperClass(type){ return 'border-l-4 ' + ({success:'border-green-500', error:'border-red-500', info:'border-blue-500', warning:'border-yellow-500'}[type] || 'border-gray-300'); },
        iconWrapClass(type){ return ({success:'bg-green-500', error:'bg-red-500', info:'bg-blue-500', warning:'bg-yellow-500'}[type] || 'bg-gray-400'); },
        barClass(type){ return ({success:'bg-green-500', error:'bg-red-500', info:'bg-blue-500', warning:'bg-yellow-500'}[type] || 'bg-gray-400'); },
        iconClass(type){ return ({success:'fa-solid fa-check', error:'fa-solid fa-triangle-exclamation', info:'fa-solid fa-circle-info', warning:'fa-solid fa-exclamation'}[type] || 'fa-solid fa-bell'); }
    }
}
</script>
