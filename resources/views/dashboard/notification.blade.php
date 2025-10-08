@extends('layouts.sidebar')

@section('title', 'Notification')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold ml-8 mb-6 pl-1">Notifications</h1>

        {{-- Container untuk notifikasi --}}
        <div id="notification-list" class="space-y-4 px-7">
            <p class="text-gray-500">Memuat notifikasi...</p>
        </div>
    </div>
    @vite([
        'resources/js/dashboard/notification.js',
    ])
@endsection