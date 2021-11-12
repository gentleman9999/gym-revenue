{{-- Bootstrap Notifications using Prologue Alerts & PNotify JS --}}
<link rel="stylesheet" href="https://gymrevenue.s3.us-east-2.amazonaws.com/noty.css"/>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/noty/3.1.4/noty.min.js"></script>
<script type="text/javascript">
    // This is intentionaly run after dom loads so this way we can avoid showing duplicate alerts
    // when the user is beeing redirected by persistent table, that happens before this event triggers.
    document.onreadystatechange = function () {
        if (document.readyState == "interactive") {
            // get alerts from the alert bag
            var $alerts_from_php = JSON.parse('@json(\Alert::getMessages())');

            // merge both php alerts and localstorage alerts
            /*
            Object.entries($alerts_from_php).forEach(function(type) {
                if(typeof $alerts_from_localstorage[type[0]] !== 'undefined') {
                    type[1].forEach(function(msg) {
                        $alerts_from_localstorage[type[0]].push(msg);
                    });
                } else {
                    $alerts_from_localstorage[type[0]] = type[1];
                }
            });
            */
            for (var type in $alerts_from_php) {
                let messages = new Set($alerts_from_php[type]);

                messages.forEach(function(text) {
                    let alert = {
                        type: 'warning',
                        theme: 'sunset',
                        text: 'Feature Coming Soon!',
                        timeout: 7500
                    };
                    alert['type'] = type;
                    alert['text'] = text;
                    new Noty(alert).show()
            });
            }

            // in the end, remove backpack alerts from localStorage
            //localStorage.removeItem('backpack_alerts');
        }
    };
</script>
