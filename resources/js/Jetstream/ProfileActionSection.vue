<template>
    <div class="col-span-2 sm:col-span-2">
        <jet-section-title>
            <template #title><slot name="title"></slot></template>
            <template #description><slot name="description"></slot></template>
        </jet-section-title>

        <div class="">
            <div
                class="px-4 py-5 sm:p-6 bg-base-300 border border-secondary border-1"
            >
                <slot name="content"></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import JetSectionTitle from "./SectionTitle.vue";
</script>
