<template>
    <div class="flex items-center col-span-3 lg:col-span-1" v-bind="$attrs">
        <div class="flex w-full gap-2 items-center shadow rounded">
            <dropdown align="start" width="60">
                <template #trigger>
                    <slot name="trigger">
                        <span class="inline-flex rounded-md h-full">
                            <button
                                type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-base-100 hover:bg-base-100 hover: focus:outline-none focus:bg-base-100 active:bg-base-100 transition"
                            >
                                Filter
                                <svg
                                    class="ml-2 -mr-0.5 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </span>
                    </slot>
                </template>

                <template #content>
                    <div class="w-60 mx-4 pt-2 pb-4">
                        <slot name="content">
                            <div
                                slot="dropdown"
                                class="shadow-xl rounded"
                                :style="{ maxWidth: `100%` }"
                            >
                                <slot />
                            </div>
                        </slot>
                        <button
                            @click="$emit('clear-filters')"
                            class="btn btn-sm btn-outline mt-4 w-full"
                        >
                            Clear Filters
                        </button>
                    </div>
                </template>
            </dropdown>
            <div class="relative flex-grow">
                <input
                    class="relative w-full px-6 py-3 rounded-r focus:ring"
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
                    aria-label="Close Filters"
                >
                    X
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
label {
    @apply label label-text py-0 text-xs text-base-content/40;
}
input {
    @apply input input-sm input-bordered;
}
select {
    @apply select select-sm select-bordered;
}
</style>

<script>
import Dropdown from "@/Components/Dropdown.vue";
import { defineComponent } from "vue";

export default defineComponent({
    components: {
        Dropdown,
    },
    props: {
        modelValue: String,
        maxWidth: {
            type: Number,
            default: 300,
        },
    },
});
</script>
