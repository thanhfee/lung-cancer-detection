<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Doctors List
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border">ID</th>
                            <th class="p-3 border">Name</th>
                            <th class="p-3 border">Email</th>
                            @if(auth()->user()->role === 'admin')
                                <th class="p-3 border">Delete</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="p-3 border">{{ $user->id }}</td>
                                <td class="p-3 border">{{ $user->name }}</td>
                                <td class="p-3 border">{{ $user->email }}</td>
                                @if(auth()->user()->role === 'admin')
                                    <td class="p-3 border">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" onclick="confirmDelete({{ $user->id }})" class="inline-flex items-center justify-center px-3 py-2 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-200 transition-all" title="Xóa thông tin bác sĩ">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                            <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>
    </div>
</x-app-layout>