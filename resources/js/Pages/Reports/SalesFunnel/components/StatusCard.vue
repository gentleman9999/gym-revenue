<template>
    <div
        class="relative border border-secondary rounded flex flex-col h-full justify-between h-18 pt-3 pl-4"
        :class="
            statusSettings[status].bg
                ? statusSettings[status].bg
                : 'bg-base-100'
        "
    >
        <div class="status-badge" :class="statusSettings[status].badge"></div>
        <div class="status-text text-sm">
            {{ statusSettings[status].label }}
        </div>
        <div class="status-count text-right text-2xl pr-2.5">{{ count }}</div>
    </div>
</template>
<style scoped>
.status-badge {
    @apply absolute h-4 w-4 top-[-8px] left-[-8px] rounded-full;
}
</style>
<script setup>
const props = defineProps({
    status: {
        type: String,
        default: "",
    },
    count: {
        type: Number,
        default: "",
    },
});

const statusSettings = {
    leads: {
        label: "Leads",
        badge: "bg-primary-focus",
    },
    booked: {
        label: "Booked Appointments",
        badge: "bg-accent-focus",
    },
    confirmed: {
        label: "Confirmed Appointments",
        badge: "bg-secondary",
    },
    referrals: {
        label: "Referrals",
        badge: "bg-warning",
    },
    contacts: {
        label: "Contacts",
        badge: "bg-info",
    },
    active_booked: {
        label: "Booked Appointments",
        badge: "bg-base-content",
    },
    sales: {
        label: "Sales",
        badge: "bg-base-content",
        bg: "bg-accent",
    },
    rejections: {
        label: "Rejections",
        badge: "bg-base-content",
        bg: "bg-error",
    },
};
</script>
