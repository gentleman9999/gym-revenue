<template>
    <div
        class="relative border border-secondary rounded flex flex-col h-full justify-between h-18 pt-3 pl-4"
        :class="
            statusSettings[status].bg
                ? statusSettings[status].bg
                : 'bg-neutral-900'
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
        badge: "bg-secondary",
    },
    booked: {
        label: "Booked Appointments",
        badge: "bg-accent-600",
    },
    confirmed: {
        label: "Confirmed Appointments",
        badge: "bg-secondary-300",
    },
    referrals: {
        label: "Referrals",
        badge: "bg-warning",
    },
    contacts: {
        label: "Contacts",
        badge: "bg-neutral-500",
    },
    active_booked: {
        label: "Booked Appointments",
        badge: "bg-neutral-100",
    },
    sales: {
        label: "Sales",
        badge: "bg-neutral-100",
        bg: "bg-accent-500",
    },
    rejections: {
        label: "Rejections",
        badge: "bg-neutral-100",
        bg: "bg-error",
    },
};
</script>
