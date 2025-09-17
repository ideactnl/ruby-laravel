<div x-data="confirmModal()" x-init="init()" @keydown.escape.window="close()">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/70 backdrop-blur-[1px] z-[999]" style="display:none"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-[1000] flex items-center justify-center p-4" style="display:none" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 max-w-md w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold" x-text="title"></h3>
                <p class="text-sm text-gray-600 mt-1" x-text="message"></p>
            </div>
            <div class="px-6 py-4 flex items-center justify-end gap-2">
                <button @click="close()" class="px-4 py-2 rounded-lg border text-sm cursor-pointer">Cancel</button>
                <button @click="confirm()" class="px-4 py-2 rounded-lg bg-[#5E0F0F] text-white text-sm cursor-pointer">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmModal(){
    return {
        open:false, title:'Are you sure?', message:'This action cannot be undone.', action:null, method:'POST',
        init(){
            window.addEventListener('confirm-delete', (e)=>{
                const d = e.detail||{};
                this.title = d.title || 'Confirm Deletion';
                this.message = d.message || 'This action cannot be undone.';
                this.action = d.action; this.method = d.method || 'DELETE';
                this.open = true;
            });
        },
        close(){ this.open=false; },
        confirm(){
            if(!this.action) { this.open=false; return; }
            const form = document.createElement('form');
            form.method = 'POST'; form.action = this.action;
            const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
            if(this.method && this.method.toUpperCase() !== 'POST'){
                const m = document.createElement('input'); m.type='hidden'; m.name='_method'; m.value=this.method.toUpperCase(); form.appendChild(m);
            }
            document.body.appendChild(form); form.submit();
        }
    }
}
</script>
