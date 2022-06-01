<template>
    <div class="w-full md:w-1/4 px-2">
        <Card :options="{ more: true }">
            <template #cardTitle>
                <div
                    class="text-base-content text-lg text-bold w-2/3 border-b border-neutral-500"
                >
                    {{ title }}
                </div>
            </template>
            <img :src="progress" class="w-fit m-auto mt-4.5" />
            <div class="flex flex-row justify-end mt-3">
                <Button secondary size="xs">Detail</Button>
            </div>
        </Card>
    </div>
</template>
<script setup>
import Card from "@/Components/Card";
import Button from "@/Components/Button";
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    progress: {
        title: String,
        default: "",
    },
});
</script>
