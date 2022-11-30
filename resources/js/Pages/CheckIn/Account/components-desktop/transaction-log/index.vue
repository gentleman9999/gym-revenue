<template>
    <card-with-title secondary title="Transaction Log">
        <div class="transaction-log-list">
            <transaction-log-item
                v-for="item in mock"
                :key="item.id"
                :data="item"
            />
        </div>
        <div class="transaction-log-actions">
            <div class="flex flex-row space-x-2">
                <div>Septemper</div>
                <div class="text-base-content/50">August</div>
            </div>
            <div>All communications</div>
        </div>
    </card-with-title>
</template>
<style scoped>
.transaction-log-list {
    @apply px-4 h-96 overflow-y-auto;
}
.transaction-log-actions {
    @apply flex flex-row justify-between p-4;
}
</style>
<script setup>
import CardWithTitle from "@/Pages/CheckIn/components/card-with-title.vue";
import TransactionLogItem from "./transaction-log-item.vue";
const mock = [
    {
        id: 1,
        title: "Lorem Ipsum",
        total_usd: 69.95,
        total_percent: 14,
        count: 16,
    },
    {
        id: 2,
        title: "Lorem Ipsum",
        total_usd: 69.95,
        total_percent: 14,
        count: 16,
    },
    {
        id: 3,
        title: "Lorem Ipsum",
        total_usd: 69.95,
        total_percent: 14,
        count: 16,
    },
    {
        id: 4,
        title: "Lorem Ipsum-mailed",
        total_usd: 69.95,
        total_percent: 14,
        count: 16,
    },
    {
        id: 5,
        title: "Lorem Ipsum",
        total_usd: 69.95,
        total_percent: 14,
        count: 16,
    },
    {
        id: 14,
        title: "Lorem Ipsum-mailed",
        total_usd: 69.95,
        total_percent: 14,
        count: 16,
    },
    {
        id: 15,
        title: "Lorem Ipsum",
        total_usd: 69.95,
        total_percent: 14,
        count: 16,
    },
];
</script>
