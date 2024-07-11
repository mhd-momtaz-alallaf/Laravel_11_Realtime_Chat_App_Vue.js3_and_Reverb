<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl pb-2 text-center font-semibold text-gray-800">
            Contacts
        </h2>
    </x-slot>

    {{-- Showing the list of users using tailwind css --}}
    <div class="container mx-auto p-4">
        <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($users as $user)
                <a href="{{ route('chat', $user) }}">
                    <div class="p-4 border rounded-lg shadow-sm bg-white grid grid-cols-3 gap-4 items-center">
                        <!-- User Avatar -->
                        <div class="col-span-1 flex justify-center">
                            <img src="{{ url('storage\avatar.jpg') }}" alt="User Avatar" class="w-16 h-16 rounded-full object-cover">
                        </div>
                        <!-- User Info -->
                        <div class="col-span-2 flex flex-col">
                            <div class="font-bold">{{ $user->name }}</div>
                            <div class="text-gray-600">{{ $user->email }}</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
