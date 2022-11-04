<template>
    <div class="class-signup-container">
        <div class="mb-7 font-semibold">{{ title }}</div>
        <div v-for="item in data" :key="item.id" class="class-signup-item">
            <div class="class-signup-label">{{ item.label }}</div>
            <input v-model="form[item.id]" class="class-signup-input" />
        </div>
        <Button secondary class="mx-auto mt-8 mb-3">Sign Up</Button>
    </div>
</template>
<style scoped>
.class-signup-container {
    @apply flex flex-col items-center text-lg;
}
.class-signup-item {
    @apply flex flex-row w-full justify-end space-x-3 mb-3 items-center;
}
.class-signup-label {
    @apply text-lg text-right;
}
.class-signup-input {
    @apply text-lg rounded h-10 pl-1 focus:outline-none bg-base-content/20 w-40 text-center;
    &:focus:focus {
        --tw-ring-shadow: transparent;
    }
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
import { useGymRevForm } from "@/utils";
const form = useGymRevForm({});

const props = defineProps({
    title: String,
});
const data = [
    {
        id: "trainer",
        label: "Trainer",
    },
    {
        id: "availability",
        label: "Availability",
    },
    {
        id: "level",
        label: "Level",
    },
];
</script>
