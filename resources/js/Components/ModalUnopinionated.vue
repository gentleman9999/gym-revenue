<template>
    <teleport to="body">
        <div class="w-full h-full fixed z-[500]">
            <slot></slot>
        </div>
    </teleport>
</template>

<script setup>
import { ref } from "vue";

/**
 * Unopinionated Modal
 *
 * unopinionated modal to avoid fighting with styles & selector specification. instead style it how you want it in the
 * component - including any wrapper styles or layout styles. all this does is give you a blank canvas over the entire
 * screen to display elements the way they're needed.
 *
 * handle display via your component
 *
 */
</script>
