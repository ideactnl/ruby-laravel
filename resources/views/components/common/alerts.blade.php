<div x-data="toastCenter()"
     x-init="init()"
     class="fixed top-4 right-4 sm:right-6 z-[1100] space-y-3 w-[calc(100%-2rem)] sm:w-96">

    <template x-for="t in toasts" :key="t.id">
        <div x-show="true" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             :class="wrapperClass(t.type)"
             class="rounded-lg shadow-lg border border-gray-200 overflow-hidden">
            
            <!-- Progress bar -->
            <div class="h-1 bg-gray-100">
                <div class="h-full transition-all duration-100 ease-linear" 
                     :class="barClass(t.type)" 
                     :style="`width: ${t.progress}%`"></div>
            </div>
            
            <div class="flex items-start gap-3 p-4">
                <!-- Icon -->
                <div :class="iconWrapClass(t.type)" 
                     class="h-6 w-6 rounded-full flex items-center justify-center flex-shrink-0">
                    <i :class="iconClass(t.type)" class="text-white text-xs"></i>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm text-gray-900" x-text="t.title"></p>
                    <p class="text-sm text-gray-600 mt-1" x-html="t.message"></p>
                </div>
                
                <!-- Close button -->
                <button @click="remove(t.id)" 
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
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
        wrapperClass(type){ 
            const classes = {
                success: 'border-l-4 border-success bg-success-50',
                error: 'border-l-4 border-danger bg-danger-50',
                info: 'border-l-4 border-accent bg-accent-50',
                warning: 'border-l-4 border-warning bg-warning-50'
            };
            return classes[type] || 'border-l-4 border-gray-300 bg-gray-50';
        },
        iconWrapClass(type){ 
            const classes = {
                success: 'bg-success',
                error: 'bg-danger',
                info: 'bg-accent', 
                warning: 'bg-warning'
            };
            return classes[type] || 'bg-gray-500';
        },
        barClass(type){ 
            const classes = {
                success: 'bg-success',
                error: 'bg-danger',
                info: 'bg-accent',
                warning: 'bg-warning'
            };
            return classes[type] || 'bg-gray-500';
        },
        iconClass(type){ 
            const icons = {
                success: 'fa-solid fa-check',
                error: 'fa-solid fa-triangle-exclamation', 
                info: 'fa-solid fa-circle-info',
                warning: 'fa-solid fa-exclamation'
            };
            return icons[type] || 'fa-solid fa-bell';
        }
    }
}
</script>
