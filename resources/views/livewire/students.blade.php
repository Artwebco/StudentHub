<div class="p-6 mx-auto max-w-7xl">
    <button wire:click="create()" class="mb-4 bg-blue-500 text-white px-4 py-2 rounded text-sm font-medium">Add Student</button>

    @if (session()->has('message'))
        <div class="text-green-500">{{ session('message') }}</div>
    @endif

    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr class="text-left text-sm font-semibold text-gray-900">
                <th class="px-6 py-3">Name</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3">Phone</th>
                <th class="px-6 py-3">Country</th>
                <th class="px-6 py-3 text-center">Active</th>
                <th class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @foreach($students as $student)
                @if($student)
                    <tr class="border-t">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td class="px-4 py-2">{{ $student->email }}</td>
                        <td class="px-4 py-2">{{ $student->phone }}</td>
                        <td class="px-4 py-2">{{ $student->country }}</td>
                        <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $student->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $student->active ? 'Yes' : 'No' }}
                        </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                        <button wire:click="edit({{ $student->id }})" class="rounded-md bg-amber-50 px-3 py-1.5 text-sm font-semibold text-amber-600 hover:bg-amber-100 transition">Edit</button>
                        <button wire:click="delete({{ $student->id }})" class="rounded-md bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-600 hover:bg-red-100 transition">Delete</button>
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
