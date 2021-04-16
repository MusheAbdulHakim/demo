{{-- Bootstrap Notifications using Prologue Alerts & PNotify JS --}}
<script type="text/javascript">
    // This is intentionaly run after dom loads so this way we can avoid showing duplicate alerts
    // when the user is beeing redirected by persistent table, that happens before this event triggers.
document.onreadystatechange = function () {
    if (document.readyState == "interactive") {
        Noty.overrideDefaults({
            layout: 'topRight',
            theme: 'backstrap',
            timeout: 2500,
            closeWith: ['click', 'button'],
        });

        // get alerts from the alert bag
        var $alerts_from_php = JSON.parse('@json(\Alert::getMessages())');

        // get the alerts from the localstorage
        var $alerts_from_localstorage = JSON.parse(localStorage.getItem('backpack_alerts'))
                ? JSON.parse(localStorage.getItem('backpack_alerts')) : {};

        // merge both php alerts and localstorage alerts
        Object.entries($alerts_from_php).forEach(([type, messages]) => {
            if(typeof $alerts_from_localstorage[type] !== 'undefined') {
                $alerts_from_localstorage[type].push(...messages);
            } else {
                $alerts_from_localstorage[type] = messages;
            }
        });

        for (var type in $alerts_from_localstorage) {
            let messages = new Set($alerts_from_localstorage[type]);
            messages.forEach(text => new Noty({type, text}).show());
        }

        // in the end, remove backpack alerts from localStorage
        localStorage.removeItem('backpack_alerts');

        @if (app('env') !== 'local')
        @php
            $now = \Carbon\Carbon::now();
            $refreshTime = \Carbon\Carbon::now()->endOfHour();

            if ($now->diffInMinutes($refreshTime) < 3) {
            @endphp
                new Noty({
                    type: "info",
                    text: "<strong>Demo Refresh in {{ $now->diffInMinutes($refreshTime) }} Minutes</strong><br>You'll lose all changes.",
                    timeout : 5000, 
                }).show();
            @php
            }
            if ($now->diffInMinutes($refreshTime) > 57) {
            @endphp
                new Noty({
                    type: "info",
                    text: "<strong>Demo Refreshed {{ 60-$now->diffInMinutes($refreshTime) }} Minutes Ago</strong><br>All custom entries & files have been deleted.",
                    timeout : 5000, 
                }).show();
            @php
            }
        @endphp
        @endif
    }
}
</script>
