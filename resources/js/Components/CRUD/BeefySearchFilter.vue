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
        <div
            :class="{
                'filter-closed-container': !visible,
                'filter-drawer-hovered': !visible && isHovered,
                'filter-drawer-open': visible,
            }"
            @mouseenter="isHovered = true"
            @mouseleave="isHovered = false"
            class="filter-drawer"
        >
            <button
                v-if="visible"
                class="filter-on"
                @click="toggleFilterDrawer"
            >
                <font-awesome-icon :icon="['fas', 'chevron-right']" size="lg" />
            </button>
            <div
                id="filters-open"
                v-if="visible"
                class="w-full grid grid-cols-2 lg:block gap-4 col-span-3"
                :class="{ 'filters-open': visible }"
            >
                <slot />

                <button
                    class="btn btn-sm btn-outline self-end mt-4"
                    type="button"
                    @click="$emit('clear-filters')"
                >
                    Clear Filters
                </button>
            </div>
            <button
                v-if="!visible"
                class="toggle-filters"
                @click="toggleFilterDrawer"
            >
                <font-awesome-icon :icon="['fas', 'chevron-right']" size="lg" />
                <span class="hover-text">Filters</span>
            </button>
        </div>
    </teleport>
</template>

<style>
.filter-drawer #filters-open {
    .form-control {
        @apply mt-2 !important;
    }

    label {
        @apply text-base-content !important;
    }
}
</style>

<style scoped>
label {
    @apply label label-text py-0 text-xs text-white mt-2;
}
input {
    @apply input input-sm input-bordered;
}
select {
    @apply select select-sm select-bordered;
}

/** Default */
.filter-drawer {
    @apply w-[18rem] h-full absolute top-[1rem] left-0 bg-white rounded-r-lg transition-all whitespace-nowrap;
    grid-column: 1;
    grid-row: 1;

    .filters-open {
        @apply rounded-r-lg p-8 bg-secondary;
    }

    button.toggle-filters {
        @apply h-8 w-8 bg-white text-primary-200 rounded-3xl absolute right-[-0.75rem] top-[5rem] z-50 transition-all;

        > svg {
            @apply ml-2;
        }

        .hover-text {
            @apply text-primary-200 rotate-90 absolute top-5 -left-6 opacity-0 transition-all;
        }
    }
}

/** Hovered  */
.filter-drawer-hovered {
    @apply w-6 rounded-r-lg transition-all bg-gray-200 !important;

    button.toggle-filters {
        @apply h-16 rounded-md bg-gray-200 right-[-1.25rem] top-[4rem] transition-all;

        span.hover-text {
            @apply opacity-100;
        }
    }
}

.filter-closed-container {
    @apply w-4 rounded-r-lg;
}

/** Open */
.filter-drawer-open {
    @apply bg-transparent rounded-none;
}

button.filter-on {
    @apply absolute bg-secondary right-[0.75rem] top-[0.5rem] rotate-180 text-white !important;
}
</style>

<script>
import { defineComponent, ref } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faChevronUp, faChevronRight } from "@fortawesome/pro-solid-svg-icons";
library.add(faChevronRight);

export default defineComponent({
    components: {
        FontAwesomeIcon,
    },
    props: {
        modelValue: { String, required: true },
        maxWidth: {
            type: Number,
            default: 300,
        },
    },

    setup() {
        const visible = ref(false);
        const isHovered = ref(false);

        const toggleFilterDrawer = () => {
            visible.value = !visible.value;
        };

        return { visible, isHovered, toggleFilterDrawer };
    },
});
</script>
