<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Invite</h2>
        </template>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            test
        </div>

    </app-layout>
</template>

<script>
import { defineComponent, watch, watchEffect, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import SweetModal from "@/Components/SweetModal3/SweetModal";
import { Inertia } from "@inertiajs/inertia";

export default defineComponent({
    components: {
        AppLayout,
        SweetModal,
    },
    props: [

    ],

    setup(props) {

        return {
            Inertia,
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
.fc .fc-daygrid-day-bottom{
    @apply text-xs;
}
.fc-theme-standard .fc-popover{
    @apply bg-base-300;
}
.fc-v-event .fc-event-title-container{
    @apply text-xs leading-tight;
}
</style>
