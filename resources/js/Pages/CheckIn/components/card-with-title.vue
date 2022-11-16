<template>
    <div
        class="card-with-title"
        :class="{
            'border-error': error,
            'border-accent': accent,
            'border-secondary': secondary,
        }"
    >
        <div
            class="card-with-title-title"
            :class="{
                'bg-error': error,
                'bg-accent': accent,
                'bg-secondary': secondary,
                'text-md': size == 'sm',
                'text-2xl': size == 'md',
            }"
        >
            <div>{{ title }}</div>
        </div>
        <slot></slot>
    </div>
</template>
<style scoped>
.card-with-title {
    @apply rounded border w-full;
}
.card-with-title-title {
    @apply flex flex-col text-center justify-center text-base-content font-bold h-12;
}
</style>
<script setup>
const props = defineProps({
    title: String,
    error: Boolean,
    accent: Boolean,
    secondary: Boolean,
    size: {
        type: String,
        default: "sm",
    },
});
</script>
