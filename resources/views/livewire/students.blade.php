<div class="p-6 mx-auto max-w-7xl">
    <button wire:click="create()" class="mb-4 bg-blue-500 text-white px-4 py-2 rounded text-sm font-medium">Додади
        ученик</button>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-center justify-between bg-green-100 border-l-4 border-green-500 p-3 mb-6 text-green-800 rounded shadow-sm">

            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                        clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium">{{ session('message') }}</span>
            </div>

            <button @click="show = false" class="text-green-500 hover:text-green-800 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr class="text-left text-sm font-semibold text-gray-900">
                <th class="px-6 py-3">Име и презиме</th>
                <th class="px-6 py-3">Е-пошта</th>
                <th class="px-6 py-3">Телефон</th>
                <th class="px-6 py-3">Држава</th>
                <th class="px-6 py-3 text-center">Активен</th>
                <th class="px-6 py-3 text-right">Акции</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @foreach($students as $student)
                @if($student)
                    <tr class="border-t">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}
                        </td>
                        <td class="px-4 py-2">{{ $student->email }}</td>
                        <td class="px-4 py-2">{{ $student->phone }}</td>
                        <td class="px-4 py-2">{{ $student->country }}</td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium {{ $student->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $student->active ? 'Да' : 'Не' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <button wire:click="edit({{ $student->id }})"
                                class="rounded-md bg-amber-50 px-3 py-1.5 text-sm font-semibold text-amber-600 hover:bg-amber-100 transition">Измени</button>
                            <button wire:click="delete({{ $student->id }})"
                                class="rounded-md bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-600 hover:bg-red-100 transition">Избриши</button>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">
        {{ $students->links() }}
    </div>
    <!-- Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded w-1/3">
                <h2 class="text-lg font-bold mb-4">Student Form</h2>
                <input type="text" wire:model="first_name" placeholder="First Name" class="border p-2 w-full mb-2">
                <input type="text" wire:model="last_name" placeholder="Last Name" class="border p-2 w-full mb-2">
                <input type="email" wire:model="email" placeholder="Email" class="border p-2 w-full mb-2">
                <input type="text" wire:model="phone" placeholder="Phone" class="border p-2 w-full mb-2">
                <input type="text" wire:model="country" placeholder="Country" class="border p-2 w-full mb-2">
                <label><input type="checkbox" wire:model="active"> Active</label>
                <div class="mt-4 flex justify-end">
                    <button wire:click="store()" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
                    <button wire:click="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded ml-2">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
