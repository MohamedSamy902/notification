@extends('advanced-notifications::dashboard.layout')

@section('content')
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Send Notification</h2>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                {{ $errors->first() }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('advanced-notifications.send.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Target Selection -->
                    <div class="sm:col-span-3">
                        <label for="target_type" class="block text-sm font-medium text-gray-700">Target Audience</label>
                        <select id="target_type" name="target_type" onchange="toggleRecipientField()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md border">
                            <option value="user">Specific User (ID)</option>
                            <option value="topic">FCM Topic</option>
                            <option value="all">All Users</option>
                        </select>
                    </div>

                    <div class="sm:col-span-3" id="recipient_field">
                        <label for="recipient_id" class="block text-sm font-medium text-gray-700">Recipient ID(s) / Topic Name</label>
                        
                        <!-- User Search Input (Only visible when target_type is user) -->
                        <div id="user_search_container" class="relative mb-2">
                            <input type="text" id="user_search" placeholder="Search user by name..." class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2" autocomplete="off">
                            <ul id="search_results" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm hidden">
                                <!-- Results will be populated here -->
                            </ul>
                        </div>

                        <input type="text" name="recipient_id" id="recipient_id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2" placeholder="e.g. 1, 2, 5">
                        <p class="mt-1 text-xs text-gray-500" id="recipient_help">Enter User IDs separated by comma (e.g., 1, 2, 5)</p>
                    </div>

                    <!-- Channels -->
                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Channels</label>
                        <div class="flex space-x-4">
                            <div class="flex items-center">
                                <input id="channel_database" name="channels[]" value="database" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="channel_database" class="ml-2 block text-sm text-gray-900">Database</label>
                            </div>
                            <div class="flex items-center">
                                <input id="channel_realtime" name="channels[]" value="realtime" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="channel_realtime" class="ml-2 block text-sm text-gray-900">Real-time (Reverb)</label>
                            </div>
                            <div class="flex items-center">
                                <input id="channel_fcm" name="channels[]" value="fcm" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="channel_fcm" class="ml-2 block text-sm text-gray-900">FCM (Mobile)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="sm:col-span-6 border-t border-gray-200 pt-6 mt-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Content</h3>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" id="title" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2">
                    </div>

                    <div class="sm:col-span-6">
                        <label for="body" class="block text-sm font-medium text-gray-700">Body</label>
                        <textarea name="body" id="body" rows="3" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2"></textarea>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="icon" class="block text-sm font-medium text-gray-700">Icon (Optional)</label>
                        <input type="text" name="icon" id="icon" placeholder="e.g., info, warning" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2">
                    </div>

                    <div class="sm:col-span-3">
                        <label for="action_url" class="block text-sm font-medium text-gray-700">Action URL (Optional)</label>
                        <input type="text" name="action_url" id="action_url" placeholder="https://..." class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2">
                    </div>
                </div>

                <div class="pt-5">
                    <div class="flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Send Notification
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleRecipientField() {
            const type = document.getElementById('target_type').value;
            const field = document.getElementById('recipient_field');
            const help = document.getElementById('recipient_help');
            const searchContainer = document.getElementById('user_search_container');
            
            if (type === 'all') {
                field.style.display = 'none';
            } else {
                field.style.display = 'block';
                if (type === 'user') {
                    help.innerText = 'Enter User IDs separated by comma (e.g., 1, 2, 5)';
                    searchContainer.style.display = 'block';
                } else {
                    help.innerText = 'Enter Topic Name (e.g., news)';
                    searchContainer.style.display = 'none';
                }
            }
        }

        // Initialize state
        document.addEventListener('DOMContentLoaded', function() {
            toggleRecipientField();

            const searchInput = document.getElementById('user_search');
            const resultsList = document.getElementById('search_results');
            const recipientInput = document.getElementById('recipient_id');

            searchInput.addEventListener('input', function() {
                const query = this.value;
                if (query.length < 2) {
                    resultsList.classList.add('hidden');
                    return;
                }

                fetch(`{{ route('advanced-notifications.users.search') }}?q=${query}`)
                    .then(response => response.json())
                    .then(users => {
                        resultsList.innerHTML = '';
                        if (users.length > 0) {
                            resultsList.classList.remove('hidden');
                            users.forEach(user => {
                                const li = document.createElement('li');
                                li.className = 'cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white text-gray-900 border-b border-gray-100 last:border-0';
                                li.textContent = `${user.name} (#${user.id})`;
                                li.onclick = () => {
                                    const currentIds = recipientInput.value.split(',').map(id => id.trim()).filter(id => id);
                                    if (!currentIds.includes(String(user.id))) {
                                        currentIds.push(user.id);
                                        recipientInput.value = currentIds.join(', ');
                                    }
                                    searchInput.value = '';
                                    resultsList.classList.add('hidden');
                                };
                                resultsList.appendChild(li);
                            });
                        } else {
                            resultsList.classList.add('hidden');
                        }
                    });
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                    resultsList.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
