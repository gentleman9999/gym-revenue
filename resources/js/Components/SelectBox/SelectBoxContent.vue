<template>
    <div class="select-box-content text-secondary text-xs">
        <slot></slot>
    </div>
</template>
<style scoped>
.select-box-content {
    background-color: #ebf1fa;
    border: 1px solid;
    border-radius: 4px;
    cursor: pointer;
    padding: 8px 0px;
    position: absolute;
    right: 0;
    z-index: 10;
}
</style>
