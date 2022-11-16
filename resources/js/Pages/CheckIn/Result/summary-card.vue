<template>
    <div class="summary-result-card">
        <div class="summary-title">Kevin Buchanan</div>
        <div class="text-accent summary-result-mark">
            <font-awesome-icon :icon="['fas', 'check']" size="lg" />
        </div>
        <div class="text-lg">You are checked in.</div>
        <div class="summary-welcome">Have a great workout!</div>
        <div
            class="summary-view-acc"
            @click="$inertia.visit(route('checkin.account'))"
        >
            View Account
        </div>
        <Button
            secondary
            class="flex m-auto"
            @click="$emit('showModal', 'add-guest')"
        >
            Check-In Guest
        </Button>
    </div>
</template>
<style scoped>
.summary-result-card {
    @apply w-full md:w-80 rounded border border-secondary bg-base-300 text-center py-6;
}
.summary-title,
.summary-welcome {
    @apply text-xl font-bold;
}
.summary-result-mark {
    @apply text-6xl py-6;
}
.summary-view-acc {
    @apply text-xs pt-4 pb-6;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";

import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faCheck } from "@fortawesome/pro-solid-svg-icons";
library.add(faCheck);
</script>
