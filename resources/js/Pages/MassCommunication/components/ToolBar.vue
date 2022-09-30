<template>
    <div
        v-if="open"
        class="bg-primary w-full flex flex-col items-center"
        v-click-outside="open && closeToolbar"
    >
        <div class="w-full">
            <div class="flex flex-wrap gap-6 justify-center pt-6">
                <!-- campaigns / drip creator (new page/modal) -->
                <button
                    class="toolbar-btn"
                    @click="Inertia.visit(route('mass-comms.email-templates'))"
                >
                    Email Templates
                </button>
                <button
                    class="toolbar-btn"
                    @click="Inertia.visit(route('mass-comms.sms-templates'))"
                >
                    SMS Templates
                </button>
                <button
                    class="toolbar-btn"
                    @click="
                        Inertia.visit(
                            route('mass-comms.campaigns.dashboard', 'drip')
                        )
                    "
                >
                    Drip Campaigns
                </button>
                <button
                    class="toolbar-btn"
                    @click="
                        Inertia.visit(
                            route('mass-comms.campaigns.dashboard', 'scheduled')
                        )
                    "
                >
                    Scheduled Campaigns
                </button>
                <!-- <button class="bg-secondary">+ Add Lead</button> -->
                <button
                    class="toolbar-btn"
                    @click="
                        () => {
                            closeToolbar();
                            toggleDripBuilder();
                        }
                    "
                >
                    + Build Drip Campaign
                </button>
                <button
                    class="toolbar-btn"
                    @click="
                        () => {
                            closeToolbar();
                            toggleScheduleBuilder();
                        }
                    "
                >
                    + Build Scheduled Campaign
                </button>
                <!-- <button class="border border-secondary">+ Communication</button> -->
            </div>
        </div>
    </div>

    <!-- this is always visible - the bottom of the toolbar with toggle button -->
    <div class="w-full bg-primary flex justify-center relative h-6">
        <button
            @click="toggleToolbar"
            class="bg-primary h-8 !rounded-full absolute z-1 -bottom-3 w-32 text-center pt-1 px-0 pb-0 fill-primary-content group"
        >
            <UpChev
                :class="{ 'rotate-180': !open }"
                class="block mx-auto my-0 p-2 transition-transform group-hover:scale-150"
            />
        </button>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import UpChev from "./iconsvg/UpChev.vue";

const open = ref(props.startOpen);

const props = defineProps({
    toggleDripBuilder: {
        type: Function,
        default: () => {},
    },
    toggleScheduleBuilder: {
        type: Function,
        default: () => {},
    },
    startOpen: {
        type: Boolean,
        default: false,
    },
});

const toggleToolbar = () => {
    open.value = !open.value;
};

const closeToolbar = () => {
    open.value = false;
};

const openToolbar = () => {
    open.value = true;
};
</script>

<style scoped>
button {
    @apply text-base-content rounded-md px-2 py-1 text-base;
}

.toolbar-btn {
    @apply bg-secondary border border-base-content;
    @apply hover:bg-secondary-focus;
    @apply active:opacity-50;
}
</style>
