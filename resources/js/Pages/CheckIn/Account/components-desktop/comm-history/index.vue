<template>
    <card-with-title secondary title="Communication History">
        <div class="comm-history-list">
            <comm-history-item
                v-for="item in mock"
                :key="item.id"
                :data="item"
            />
        </div>
        <div class="comm-history-actions">
            <div class="flex flex-row space-x-2">
                <div>Septemper</div>
                <div class="text-base-content/50">August</div>
            </div>
            <div>All communications</div>
        </div>
    </card-with-title>
</template>
<style scoped>
.comm-history-list {
    @apply px-4 h-96 overflow-y-auto;
}
.comm-history-actions {
    @apply flex flex-row justify-between p-4;
}
</style>
<script setup>
import CardWithTitle from "@/Pages/CheckIn/components/card-with-title.vue";
import CommHistoryItem from "./comm-history-item.vue";
const mock = [
    {
        id: 1,
        content: "Called",
        status: "Answered",
        date: "09/25/22",
    },
    {
        id: 2,
        content: "Called",
        status: "Voicemail",
        date: "09/17/22",
    },
    {
        id: 3,
        content: "SMS",
        status: "Received",
        date: "09/05/22",
    },
    {
        id: 4,
        content: "E-mailed",
        status: "Received",
        date: "09/05/22",
    },
    {
        id: 5,
        content: "Called",
        status: "Answered",
        date: "09/25/22",
    },
    {
        id: 14,
        content: "E-mailed",
        status: "Received",
        date: "09/05/22",
    },
    {
        id: 15,
        content: "Called",
        status: "Answered",
        date: "09/25/22",
    },
];
</script>
