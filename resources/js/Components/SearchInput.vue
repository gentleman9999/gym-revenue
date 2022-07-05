<template>
    <div class="flex items-center relative search-input-wrapper">
        <input
            :class="{
                'search-input border-secondary': true,
                'h-6 text-sm': size === 'xs',
                'h-7 text-base': size === 'sm',
                'h-8 text-lg': size === 'lg',
            }"
            :placeholder="placeholder"
        />
        <div class="search-icon">
            <search-icon />
        </div>
    </div>
</template>
<style scoped>
.search-input {
    @apply bg-transparent rounded border overflow-hidden pl-2 pr-8 text-right;
}
.search-icon {
    @apply absolute right-2.5 top-1;
}
.search-input:hover,
.search-input:focus {
    @apply text-left outline-0;
}
.search-input-wrapper:hover .search-icon,
.search-input-wrapper:focus .search-icon {
    @apply hidden;
}
</style>
<script setup>
import SearchIcon from "./Icons/Search.vue";
const props = defineProps({
    placeholder: {
        type: String,
        default: "Search",
    },
    size: {
        type: String,
        default: "sm",
    },
});
</script>
