<template>
    <Button
        @click="Inertia.visit(route('checkin.club'))"
        class="menu-item my-10"
        accent
    >
        Club Check-In
    </Button>
    <div class="menu-container">
        <Button
            v-for="(item, ndx) in data"
            :key="ndx"
            @click="handleClick(item)"
            secondary
            class="menu-item"
        >
            {{ item.label }}
        </Button>
    </div>
</template>
<style scoped>
.menu-item {
    @apply w-64 text-xl font-semibold;
}
.menu-container {
    @apply flex flex-col items-center space-y-5 pb-14;
}
</style>

<script setup>
import Button from "@/Components/Button.vue";
import { Inertia } from "@inertiajs/inertia";
const data = [
    {
        label: "Account",
        link: "checkin.account",
    },
    {
        label: "Invite Friends",
        key: "invite",
    },
    {
        label: "Class Sign Up",
        key: "available-class",
    },
    {
        label: "Workout History",
        key: "workout",
    },
    {
        label: "Check In History",
        key: "checkin",
    },
    {
        label: "Payment History",
        key: "payment",
    },
];
const emit = defineEmits(["showModal"]);

const handleClick = (item) => {
    console.log("handleClick", item);

    if (item.link) {
        Inertia.visit(route(item.link));
    } else {
        emit("showModal", item.key);
    }
};
</script>
