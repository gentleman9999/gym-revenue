<template>
    <div class="drawer drawer-end">
        <slot name="switch"></slot>
        <div class="drawer-content">
            <slot name="pageContent"></slot>
        </div>
        <div class="drawer-side">
            <label for="drawerSwitch" class="drawer-overlay"></label>
            <slot name="drawerContent"></slot>
        </div>
    </div>
</template>
