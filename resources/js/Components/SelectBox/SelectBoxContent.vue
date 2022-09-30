<template>
    <div class="select-box-content text-secondary text-xs">
        <slot></slot>
    </div>
</template>
<style scoped>
.select-box-content {
    @apply bg-base-200 border border-secondary rounded cursor-pointer py-2 absolute z-20 right-0;
}
</style>
