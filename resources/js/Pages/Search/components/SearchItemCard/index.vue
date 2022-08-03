<template>
    <div class="p-2">
        <div class="item-wrapper">
            <div class="item-type">
                <button @click="$inertia.visit(route(data.link, data.id))">
                    {{ data.type }}
                </button>
            </div>
            <div class="item-data">
                {{ data.name ? data.name : data.title }}
            </div>
            <div class="item-actions">
                <!--    <Button primary size="xs">Edit</Button>
                    <Button primary size="xs">Remove</Button>-->
            </div>
        </div>
    </div>
</template>
<style scoped>
.item-wrapper {
    @apply flex flex-row justify-between items-center border border-secondary rounded relative p-8 bg-neutral-900;
}
.item-type {
    @apply bg-info p-0.5 rounded px-2;
}
.item-actions {
    @apply flex flex-row space-x-2;
}
.item-data {
    @apply mt-2;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
const props = defineProps({
    data: {
        type: Object,
    },
});
</script>
