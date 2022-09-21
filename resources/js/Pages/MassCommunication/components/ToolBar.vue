<template>
    <div
        v-if="isOpen"
        class="bg-primary-600 w-full flex flex-col items-center"
        v-click-outside="maybeClose"
    >
        <button @click="toggleBar" class="font-bold text-base py-1">
            Tool Bar
        </button>
        <div class="max-w-7xl w-full">
            <!--            <div>-->
            <!--                <input type="text" class="block ml-auto text-base" />-->
            <!--            </div>-->
            <div class="flex flex-wrap gap-2 justify-end py-6">
                <!-- campaigns / drip creator (new page/modal) -->

                <button
                    class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
                    @click="Inertia.visit(route('mass-comms.email-templates'))"
                >
                    Email Templates
                </button>
                <button
                    class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
                    @click="Inertia.visit(route('mass-comms.sms-templates'))"
                >
                    SMS Templates
                </button>
                <button
                    class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
                    @click="
                        Inertia.visit(
                            route('mass-comms.campaigns.dashboard', 'drip')
                        )
                    "
                >
                    Drip Campaigns
                </button>
                <button
                    class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
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
                    @click="toggleDripBuilder"
                    class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
                >
                    + Build Drip Campaign
                </button>
                <button
                    @click="toggleScheduleBuilder"
                    class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
                >
                    + Build Scheduled Campaign
                </button>
                <!-- <button class="border border-secondary">+ Communication</button> -->
            </div>
        </div>
    </div>
    <div
        @click="toggleBar"
        v-if="!isOpen"
        class="w-full bg-primary-600 h-4"
    ></div>
</template>

<script setup>
import { ref, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
const isOpen = ref(false);

const props = defineProps({
    toggleDripBuilder: {
        type: Function,
        default: () => {},
    },
    toggleScheduleBuilder: {
        type: Function,
        default: () => {},
    },
});

const toggleBar = () => (isOpen.value = !isOpen.value);

const maybeClose = () => {
    isOpen.value = false;
};
</script>

<style scoped>
button {
    @apply text-base-content rounded-md px-2 py-1 ml-6 text-base;
}
</style>
