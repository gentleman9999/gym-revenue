<template>
    <div class="ml-3 relative">
        <button
            @click="showingNotificationDropdown = !showingNotificationDropdown"
            class="flex mx-4 focus:outline-none"
        >
            <svg
                class="h-6 w-6"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M15 17H20L18.5951 15.5951C18.2141 15.2141 18 14.6973 18 14.1585V11C18 8.38757 16.3304 6.16509 14 5.34142V5C14 3.89543 13.1046 3 12 3C10.8954 3 10 3.89543 10 5V5.34142C7.66962 6.16509 6 8.38757 6 11V14.1585C6 14.6973 5.78595 15.2141 5.40493 15.5951L4 17H9M15 17V18C15 19.6569 13.6569 21 12 21C10.3431 21 9 19.6569 9 18V17M15 17H9"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                ></path>
            </svg>
            <div class="badge badge-error">{{ unreadCount }}</div>
        </button>

        <div
            v-show="showingNotificationDropdown"
            @click="showingNotificationDropdown = false"
            class="fixed inset-0 h-full w-full z-10"
            style="display: none"
        ></div>

        <!-- Notifications links -->
        <div
            v-show="showingNotificationDropdown"
            class="absolute right-0 mt-2 w-80 bg-base-300 rounded-lg shadow-xl border border-base-100 overflow-hidden z-10 max-h-[600px] overflow-y-auto"
            style="width: 20rem; display: none"
        >
            <template v-for="notification in notifications">
                <calendar-event-notification
                    v-if="
                        notification.entity_type ===
                        'App\\Models\\Calendar\\CalendarEvent'
                    "
                    :notification="notification"
                    @dismiss="() => dismissNotification(notification.id)"
                />
                <base-notification
                    v-else
                    @dismiss="() => dismissNotification(notification.id)"
                    :notification="notification"
                >
                        {{ notification.text }}
                </base-notification>
            </template>

            <!--            <a href="#" class="flex items-center px-4 py-3  hover:bg-base-100 -mx-2">-->
            <!--                <figure class="w-1/6">-->
            <!--                    <img class="h-8 w-8 rounded-full object-cover mx-1" src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=634&amp;q=80" alt="avatar">-->
            <!--                </figure>-->
            <!--                <p class="text-sm mx-2 w-full">-->
            <!--                    <span class="font-bold" href="#">Slick Net</span> start following you . 45m-->
            <!--                </p>-->
            <!--            </a>-->
            <!--            <a href="#" class="flex items-center px-4 py-3  hover:bg-base-100 -mx-2">-->
            <!--                <figure class="w-1/6">-->
            <!--                    <img class="h-8 w-8 rounded-full object-cover mx-1" src="https://images.unsplash.com/photo-1450297350677-623de575f31c?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80" alt="avatar">-->
            <!--                </figure>-->
            <!--                <p class="text-sm mx-2 w-full">-->
            <!--                    <span class="font-bold" href="#">Jane Doe</span> Like Your reply on <span class="font-bold " href="#">Test with TDD</span> artical . 1h-->
            <!--                </p>-->
            <!--            </a>-->
            <!--            <a href="#" class="flex items-center px-4 py-3  hover:bg-base-100 -mx-2">-->
            <!--                <figure class="w-1/6">-->
            <!--                    <img class="h-8 w-8 rounded-full object-cover mx-1" src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=398&amp;q=80" alt="avatar">-->
            <!--                </figure>-->
            <!--                <p class="text-sm mx-2 w-full">-->
            <!--                    <span class="font-bold" href="#">Abigail Bennett</span> start following you . 3h-->
            <!--                </p>-->
            <!--            </a>-->
        </div>
        <!-- End Notifications links -->
    </div>
    <!-- End Notifications -->
</template>

<script>
import { useNotifications } from "@/utils";
import CalendarEventNotification from "@/Components/Notifications/CalendarEventNotification";
import BaseNotification from "@/Components/Notifications/BaseNotification";

export default {
    name: "NotyBell",
    props: [],
    components: {
        BaseNotification,
        CalendarEventNotification
    },
    data() {
        return {
            showingNotificationDropdown: false,
        };
    },
    setup() {
        const { notifications, unreadCount, dismissNotification } =
            useNotifications();
        return {
            notifications,
            unreadCount,
            dismissNotification,
        };
    },
};
</script>

<style scoped></style>
