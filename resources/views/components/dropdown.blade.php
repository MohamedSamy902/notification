<div x-data="notificationDropdown()" class="relative">
    <button @click="open = !open" class="relative z-10 block rounded-md bg-white p-2 focus:outline-none">
        <svg class="h-6 w-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span x-show="unreadCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full" x-text="unreadCount"></span>
    </button>

    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-20" style="display: none;">
        <div class="py-2">
            <template x-for="notification in notifications" :key="notification.id">
                <a :href="notification.data.action_url || '#'" class="flex items-center px-4 py-3 border-b hover:bg-gray-100 -mx-2" @click="markAsRead(notification.id)">
                    <img class="h-8 w-8 rounded-full object-cover mx-1" :src="notification.data.icon || 'https://via.placeholder.com/40'" alt="avatar">
                    <p class="text-gray-600 text-sm mx-2">
                        <span class="font-bold" x-text="notification.data.title"></span>
                        <span class="block text-xs" x-text="notification.data.body"></span>
                    </p>
                </a>
            </template>
            <div x-show="notifications.length === 0" class="px-4 py-3 text-gray-500 text-sm text-center">
                {{ __('No new notifications') }}
            </div>
            <a href="#" class="block bg-gray-800 text-white text-center font-bold py-2">See all notifications</a>
        </div>
    </div>
</div>

<script>
function notificationDropdown() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,
        init() {
            this.fetchNotifications();
            // Listen for real-time events if configured
            // Echo.private('App.Models.User.' + userId).notification((notification) => { ... });
        },
        fetchNotifications() {
            fetch('/api/notifications/unread')
                .then(response => response.json())
                .then(data => {
                    this.notifications = data;
                    this.unreadCount = data.length;
                });
        },
        markAsRead(id) {
            fetch(`/api/notifications/${id}/read`, { method: 'POST' })
                .then(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                    this.unreadCount--;
                });
        }
    }
}
</script>
