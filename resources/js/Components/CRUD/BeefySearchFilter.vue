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
    <teleport to="main">
        <div class="filter-drawer">
            <div
                v-if="visible"
                class="w-full grid grid-cols-2 lg:block gap-4 col-span-3"
                :class="{ 'filters-open': visible }"
            >
                <slot />

                <button
                    class="btn btn-sm btn-outline self-end"
                    type="button"
                    @click="$emit('clear-filters')"
                >
                    Clear Filters
                </button>
            </div>
            <button class="toggle-filters" @click="toggleFilterDrawer">
                Filter
            </button>
        </div>
    </teleport>
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

.filter-drawer {
    @apply max-w-sm relative bg-primary-200 bg-opacity-30 transition-transform;
    grid-column: 1;
    grid-row: 1;

    button.toggle-filters {
        @apply h-8 w-12 bg-red-400 rounded-r-lg absolute right-[-3rem] top-[5rem] z-50;
    }
}

.filters-open {
    @apply p-8;
}
</style>

<style>
main {
    @apply grid;
    grid-template-rows: 100%;
    grid-template-columns: auto 1fr;
}
</style>

<script>
import { defineComponent, ref } from "vue";

export default defineComponent({
    props: {
        modelValue: { String, required: true },
        maxWidth: {
            type: Number,
            default: 300,
        },
    },
    setup() {
        const visible = ref(false);

        const toggleFilterDrawer = () => {
            visible.value = !visible.value;
        };

        return { visible, toggleFilterDrawer };
    },
});
</script>
