<template>
    <Card
        class="h-full"
        :options="{
            collapse: true,
            borderedTitle: true,
            favorite: true,
        }"
    >
        <template #cardTitle>
            <div class="text-base-content text-lg text-bold">
                Completed Tasks
            </div>
        </template>
        <div class="flex flex-row">
            <img :src="progress" class="w-fit h-fit" />
            <div class="flex flex-col space-y-2.5">
                <div
                    v-for="item in data"
                    :key="item.key"
                    class="text-secondary flex flex-row items-center space-x-2 pl-3"
                >
                    <div :class="item.class"></div>
                    <div>{{ item.label }} ({{ item.percent }}%)</div>
                </div>
                <Button size="xs" secondary class="self-end mt-6">View</Button>
            </div>
        </div>
    </Card>
</template>
<style scoped>
.badge {
    @apply w-4 h-5 rounded-full;
}
</style>
<script setup>
import Card from "@/Components/Card";
import Button from "@/Components/Button";

const props = defineProps({
    progress: {
        stype: String,
        default: "",
    },
});

const data = [
    {
        class: "badge bg-warning",
        key: "lead",
        label: "Leads",
        percent: 23,
    },
    {
        class: "badge bg-neutral-300",
        key: "calls",
        label: "Calls",
        percent: 34,
    },
    {
        class: "badge bg-secondary",
        key: "meetings",
        label: "Meetings",
        percent: 54,
    },
    {
        class: "badge bg-accent",
        key: "success",
        label: "Success",
        percent: 16,
    },
];
</script>
