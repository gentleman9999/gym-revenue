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
    background: transparent;
    border-radius: 4px;
    border-width: 1px;
    overflow: hidden;
    padding-left: 8px;
    padding-right: 32px;
    text-align: right;
}
.search-icon {
    position: absolute;
    right: 10px;
    top: 4px;
}
.search-input:hover,
.search-input:focus {
    text-align: left;
    outline: none;
}
.search-input-wrapper:hover .search-icon,
.search-input-wrapper:focus .search-icon {
    display: none;
}
</style>
<script setup>
import SearchIcon from "./Icons/Search";
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
