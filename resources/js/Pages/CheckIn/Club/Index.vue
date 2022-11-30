<template>
    <div class="checkin-club-container">
        <application-logo class="checkin-logo" />
        <div class="checkin-acc-type-card">
            <club-card-header v-if="activeTab === null" />
            <club-card-tabs
                @toggle-tab="activeTab = $event"
                :active="activeTab"
            />
            <club-card-qr :description="descDict[activeTab]" />
            <club-alternative :tab="activeTab" v-if="activeTab" />
            <Button
                v-if="activeTab"
                secondary
                class="flex m-auto"
                size="md"
                @click="handleCheckIn()"
            >
                {{ activeTab !== "employee" ? "Check-In" : "Sign In" }}
            </Button>
        </div>
        <button
            class="checkin-club-search"
            v-if="activeTab === 'member' || activeTab === 'employee'"
        >
            Search
        </button>
    </div>
</template>
<style scoped>
.checkin-club-container {
    @apply flex flex-col items-center md:w-80 md:m-auto;
}
.checkin-logo {
    @apply h-10 w-auto text-base-content mb-10 mt-4;
}
.checkin-acc-type-card {
    @apply rounded border border-secondary bg-base-300 w-full pt-3 items-center pb-9;
}
.checkin-club-search {
    @apply bg-neutral-content/60 rounded w-44 h-10 mt-6 mb-12;
}
</style>
<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import ClubCardHeader from "./club-card-header.vue";
import ClubCardTabs from "./club-card-tabs.vue";
import ClubCardQr from "./club-card-qr.vue";
import ClubAlternative from "./club-alternative.vue";
import Button from "@/Components/Button.vue";
import { Inertia } from "@inertiajs/inertia";

const activeTab = ref(null);

const descDict = {
    member: "Scan Member ID",
    guest: "Scan Guest Pass",
    employee: "Scan Employee ID",
};

const handleCheckIn = () => {
    Inertia.visit(route("checkin.result"));
};
</script>
