@extends('advanced-notifications::dashboard.layout')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Analytics Overview</h2>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Sent -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Sent</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ $totalSent }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivered -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Delivered</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ $totalDelivered }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Read -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Read</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ $totalRead }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clicked -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Clicked</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ $totalClicked }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Channel Breakdown -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sent by Channel</h3>
            <div class="space-y-4">
                @foreach($channelStats as $channel => $count)
                    <div>
                        <div class="flex justify-between text-sm font-medium text-gray-900 mb-1">
                            <span class="capitalize">{{ $channel }}</span>
                            <span>{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $totalSent > 0 ? ($count / $totalSent) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
                @if(empty($channelStats))
                    <p class="text-gray-500 text-sm">No data available.</p>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($recentActivity as $activity)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">
                                    {{ ucfirst($activity->event_type) }} via {{ ucfirst($activity->channel) }}
                                </p>
                                <p class="text-gray-500 text-xs">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $activity->event_type === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $activity->event_type === 'delivered' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $activity->event_type === 'read' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $activity->event_type === 'clicked' ? 'bg-purple-100 text-purple-800' : '' }}
                            ">
                                {{ ucfirst($activity->event_type) }}
                            </span>
                        </div>
                    </li>
                @endforeach
                @if($recentActivity->isEmpty())
                    <li class="px-6 py-4 text-sm text-gray-500">No recent activity.</li>
                @endif
            </ul>
        </div>
    </div>
@endsection
