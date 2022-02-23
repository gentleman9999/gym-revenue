<template>
    <div class="relative col-span-3 lg:col-span-1">
        <input
            class="relative w-full px-6 py-3 rounded-r focus:ring !pr-8"
            autocomplete="off"
            type="text"
            name="search"
            placeholder="Search…"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <button
            class="flex items-center justify-center absolute inset-y-0 right-2 text-sm"
            @click="$emit('clear-search')"
        >
            X
        </button>
    </div>

    <div class="w-full grid grid-cols-2 lg:grid-cols-6 gap-4 col-span-3">
        <slot />

        <button
            class="btn btn-sm btn-outline self-end"
            type="button"
            @click="$emit('clear-filters')"
        >
            Clear Filters
        </button>
    </div>
</template>

<style scoped>
label {
    @apply label label-text py-0 text-xs text-gray-400;
}
input {
    @apply input input-sm input-bordered;
}
select {
    @apply select select-sm select-bordered;
}
</style>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        modelValue: { String, required: true },
        maxWidth: {
            type: Number,
            default: 300,
        },
    },
});
</script>
