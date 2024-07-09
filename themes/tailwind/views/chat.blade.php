<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{-- Showing the friend name --}}
            {{ $friend->name }} Chat
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Using the Vue.js ChatComponent in the chat view file, and passing the friend and the current-user instances to the component --}}
                    <chat-component :friend="{{ $friend }}" :current-user="{{ auth()->user() }}" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
