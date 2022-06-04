<template>
    <Card
        :options="{
            collapse: true,
            borderedTitle: true,
            favorite: true,
        }"
        class="h-full"
    >
        <template #cardTitle>
            <div class="text-base-content text-lg text-bold">Email Stats</div>
        </template>
        <div class="flex flex-row justify-between">
            <div class="flex flex-col h-[160px] overflow-y-scroll pl-2 pr-4">
                <div v-for="(item, ndx) in goals" :key="ndx">
                    <div class="text-secondary text-base">{{ item.goal }}</div>
                    <div class="w-[13vw] text-sm">{{ item.content }}</div>
                </div>
            </div>
            <div class="flex flex-col justify-center">
                <div
                    v-for="item in statis_data"
                    :key="item.key"
                    class="text-secondary"
                    :class="{
                        'text-base': item.key !== 'total',
                        'text-2xl text-bold pt-2': item.key === 'total',
                    }"
                >
                    {{ item.label }} ({{ item.percent }}%)
                </div>
            </div>

            <img :src="progress" class="h-fit w-fit" />
        </div>
    </Card>
</template>

<style scoped>
div::-webkit-scrollbar {
    @apply w-0;
}
</style>

<script setup>
import Card from "@/Components/Card";

const props = defineProps({
    progress: {
        stype: String,
        default: "/img/mocks/clalendar_page_1.png",
    },
});

const goals = [
    {
        goal: "Goal One",
        content:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin quis scelerisque lectus.",
    },
    {
        goal: "Goal Two",
        content:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin quis scelerisque lectus.",
    },
    {
        goal: "Goal Three",
        content:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin quis scelerisque lectus.",
    },
];

const statis_data = [
    {
        key: "new",
        label: "New",
        percent: 23,
    },
    {
        key: "assigned",
        label: "Assigned",
        percent: 34,
    },
    {
        key: "success",
        label: "Success",
        percent: 54,
    },
    {
        key: "failure",
        label: "Failure",
        percent: 23,
    },
    {
        key: "total",
        label: "Total",
        percent: 100,
    },
];
</script>
