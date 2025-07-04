@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Earnings Card -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-500">
                <i class="fas fa-money-bill-wave text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Earnings</p>
                <p class="text-2xl font-semibold text-gray-800">₦{{ number_format($totalEarnings, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Pending Withdrawals Card -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                <i class="fas fa-wallet text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Pending Withdrawals</p>
                <p class="text-2xl font-semibold text-gray-800">₦{{ number_format($pendingWithdrawals, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Subscribers Card -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Subscribers</p>
                <p class="text-2xl font-semibold text-gray-800">{{ number_format($totalSubscribers) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Episodes Card -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                <i class="fas fa-podcast text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Episodes</p>
                <p class="text-2xl font-semibold text-gray-800">{{ number_format($totalEpisodes) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Earnings -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Earnings</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentEarnings as $earning)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $earning->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₦{{ number_format($earning->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $earning->source }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Notifications</h2>
        <div class="space-y-4">
            @foreach($recentNotifications as $notification)
            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                    <i class="fas fa-bell text-indigo-500"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                    <p class="text-sm text-gray-500">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection 