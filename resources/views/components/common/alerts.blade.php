<div class="max-w-4xl mx-auto mb-4 space-y-2"
     x-data="{
        kind: '', msg: '',
        show(k, m){ this.kind=k; this.msg=m || ''; },
        clear(){ this.kind=''; this.msg=''; }
     }"
     @alert:success.window="show('success', ($event.detail && $event.detail.message) ? $event.detail.message : 'Success')"
     @alert:error.window="show('error', ($event.detail && $event.detail.message) ? $event.detail.message : 'Something went wrong')"
>
    <template x-if="msg">
        <div x-show="true" :class="kind==='success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-4 py-3 rounded relative">
            <span x-text="msg"></span>
            <button class="absolute top-1 right-2 text-lg" @click="clear()">&times;</button>
        </div>
    </template>

    {{-- Session Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="bg-green-100 text-green-800 px-4 py-3 rounded relative" @click.away="show = false">
            {{ session('success') }}
            <button class="absolute top-1 right-2 text-lg" @click="show = false">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" class="bg-red-100 text-red-800 px-4 py-3 rounded relative" @click.away="show = false">
            {{ session('error') }}
            <button class="absolute top-1 right-2 text-lg" @click="show = false">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded relative" @click.away="show = false">
            <strong>Validation Errors:</strong>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="absolute top-1 right-2 text-lg" @click="show = false">&times;</button>
        </div>
    @endif
    {{-- End Session Messages --}}
</div>
