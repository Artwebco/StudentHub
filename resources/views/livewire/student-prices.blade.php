<div>
    {{-- Секција за пораки (Успех и Грешка) --}}
    @if (session()->has('message'))
        <x-flash-message :message="session('message')" />
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4 gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-700">Ценовник</h1>
            <p class="text-md text-gray-600">Управување со типови на часови и цени</p>
        </div>
        <button wire:click="create"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:scale-105 active:scale-95 flex items-center gap-2 ">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Тип на час
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($templates as $template)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-emerald-50 text-emerald-500 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-800">{{ $template->name }}</h3>
                    </div>
                    <p class="text-xs text-gray-400 mb-4">{{ $template->description }}</p>

                    <div class="flex justify-between items-end mt-4">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold">Времетраење</span>
                            <p class="font-bold text-gray-700">{{ $template->duration }} мин.</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-gray-400 uppercase font-bold block">Цена</span>
                            <p class="text-xl font-bold text-[#10b981]">
                                {{ number_format($template->default_price, 0, ',', '.') }} ден.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 mt-6 pt-4 border-t border-gray-100">
                    {{-- Вратени оригинални копчиња Измени/Избриши --}}
                    <button wire:click="edit({{ $template->id }})"
                        class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-blue-600 md:bg-blue-50 md:hover:bg-blue-100 md:hover:text-blue-800 transition focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        <span class="hidden md:inline">Измени</span>
                    </button>
                    <button type="button"
                        onclick="confirmDeleteTemplate({{ $template->id }}, '{{ addslashes($template->name) }}')"
                        class="inline-flex items-center gap-2 px-3 py-1 text-sm font-medium rounded-md text-red-600 md:bg-red-50 md:hover:bg-red-100 md:hover:text-red-800 transition focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        <span class="hidden md:inline">Избриши</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- МОДАЛ СО ALPINE АНИМАЦИЈА --}}
    @if($showModal)
        <div x-data="{ open: false }" x-init="setTimeout(() => open = true, 10)"
            x-on:close-modal.window="open = false; setTimeout(() => @this.closeModal(), 200)"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">

            {{-- BACKDROP (Позадина со Blur) --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="$dispatch('close-modal')">
            </div>

            {{-- MODAL CONTENT (Содржина со Zoom ефект) --}}
            <div x-show="open" x-transition:enter="animate-swal-show" x-transition:leave="animate-swal-hide"
                class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden z-50 border border-gray-100">

                <div class="px-5 sm:px-8 pt-6 sm:pt-8 pb-4 flex justify-between items-start gap-3">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $editingId ? 'Измени тип' : 'Внес на тип на час' }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Додадете нов или ажурирајте постоечки тип</p>
                    </div>
                    <button @click="$dispatch('close-modal')" class="text-gray-400 hover:text-gray-600 transition p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="px-5 sm:px-8 py-5 sm:py-6 space-y-4">
                    <input type="text" wire:model="name" placeholder="Име на часот"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">

                    <input type="text" wire:model="description" placeholder="Краток опис"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">

                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-2">
                        <input type="number" wire:model="duration" placeholder="Минути"
                            class="w-full sm:flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none min-w-0">
                        <input type="number" wire:model="default_price" placeholder="Цена"
                            class="w-full sm:flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none min-w-0">
                    </div>
                </div>

                <div class="px-5 sm:px-8 py-5 sm:py-6 bg-gray-50/50 flex flex-row-reverse gap-3 items-center">
                    <button wire:click="save"
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-200 transition-all transform active:scale-95">
                        <span wire:loading.remove wire:target="save">Зачувај</span>
                        <span wire:loading wire:target="save">Се зачувува...</span>
                    </button>
                    <button @click="$dispatch('close-modal')"
                        class="px-6 py-3 text-gray-500 font-semibold hover:text-gray-700">Откажи</button>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    function confirmDeleteTemplate(id, name) {
        Swal.fire({
            title: 'Бришење на тип?',
            html: "Дали сте сигурни дека сакате да го избришете '<b>" + name + "</b>' од ценовникот?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#2563eb',
            confirmButtonText: 'Да, избриши!',
            cancelButtonText: 'Откажи',
            reverseButtons: true,
            backdrop: `rgba(15, 23, 42, 0.5)` // Суптилен blur ефект
        }).then((result) => {
            if (result.isConfirmed) {
                // Го повикуваме Livewire методот за бришење
                @this.delete(id);

                // Мала потврда за успех
                Swal.fire({
                    title: 'Успешно избришано!',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        })
    }
</script>
