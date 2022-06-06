<template>
    <maybe-auth-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Invite</h2>
        </template>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="card lg:card-side bg-base-100 shadow-xl">
                <figure>
                    <img
                        src="https://st.depositphotos.com/1056393/4630/i/600/depositphotos_46302587-stock-photo-speaker-at-business-conference-and.jpg"
                        alt="Album"
                    />
                </figure>
                <div class="card-body">
                    <h2 class="card-title">
                        {{ attendeeData.event.title }} Invitation
                    </h2>
                    <p
                        v-if="
                            attendeeData.entity_type ==
                            'App\\Models\\Endusers\\Lead'
                        "
                    >
                        <b
                            >{{ attendeeData.entity_data.first_name }}
                            {{ attendeeData.entity_data.last_name }}</b
                        >, would you like to join us for
                        {{ attendeeData.event.description }}?
                    </p>
                    <p v-else>
                        <b>{{ attendeeData.entity_data.name }}</b
                        >, would you like to join us for
                        {{ attendeeData.event.description }}?
                    </p>
                    <p>Event Start: {{ attendeeData.event.start }}</p>
                    <div>
                        <div class="badge badge-lg">
                            {{ attendeeData.invitation_status }}
                        </div>
                    </div>
                    <div class="card-actions justify-end">
                        <button class="btn btn-success" @click="accept()">
                            Accept
                        </button>
                        <button class="btn btn-error" @click="decline()">
                            Decline
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </maybe-auth-layout>
</template>

<script>
import { defineComponent, watch, watchEffect, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Inertia } from "@inertiajs/inertia";
import MaybeAuthLayout from "@/Layouts/MaybeAuthLayout";

export default defineComponent({
    components: {
        MaybeAuthLayout,
        AppLayout,
    },
    props: ["attendeeData"],

    setup(props) {
        function accept() {
            Inertia.post(
                route("invite.accept", {
                    attendeeData: props.attendeeData,
                })
            );
        }

        function decline() {
            Inertia.post(
                route("invite.decline", {
                    attendeeData: props.attendeeData,
                })
            );
        }
        return {
            Inertia,
            accept,
            decline,
        };
    },
});
</script>

<style>
.fc .fc-toolbar {
    @apply flex-col gap-2;
    @screen lg {
        @apply flex-row;
    }
}
.fc .fc-daygrid-day-bottom {
    @apply text-xs;
}
.fc-theme-standard .fc-popover {
    @apply bg-base-300;
}
.fc-v-event .fc-event-title-container {
    @apply text-xs leading-tight;
}
</style>
