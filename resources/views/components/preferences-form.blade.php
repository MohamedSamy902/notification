<div x-data="notificationPreferences()" class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Notification Preferences</h3>
        <div class="mt-2 max-w-xl text-sm text-gray-500">
            <p>Choose how you receive notifications.</p>
        </div>
        <form class="mt-5 space-y-4">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="email_notifications" name="email_notifications" type="checkbox" x-model="preferences.email" @change="updatePreference('email')" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="email_notifications" class="font-medium text-gray-700">Email Notifications</label>
                    <p class="text-gray-500">Get notified via email.</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="push_notifications" name="push_notifications" type="checkbox" x-model="preferences.fcm" @change="updatePreference('fcm')" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="push_notifications" class="font-medium text-gray-700">Push Notifications</label>
                    <p class="text-gray-500">Get notified on your device.</p>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function notificationPreferences() {
    return {
        preferences: {
            email: true,
            fcm: true
        },
        init() {
            this.fetchPreferences();
        },
        fetchPreferences() {
            fetch('/api/notifications/preferences')
                .then(response => response.json())
                .then(data => {
                    data.forEach(pref => {
                        if (this.preferences.hasOwnProperty(pref.channel)) {
                            this.preferences[pref.channel] = pref.is_enabled;
                        }
                    });
                });
        },
        updatePreference(channel) {
            fetch('/api/notifications/preferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    channel: channel,
                    is_enabled: this.preferences[channel]
                })
            });
        }
    }
}
</script>
