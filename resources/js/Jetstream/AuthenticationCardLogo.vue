<template>
    <inertia-link :href="'/'">
        <jet-application-mark />
    </inertia-link>
    <div class="subtitle">
        <p><small>CRM - Conceptual Prototype</small></p>
    </div>
</template>

<script setup>
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
</script>

<style scoped>
.logo-e {
    fill: #0074c8;
}
.subtitle {
    width: 100%;
}
.subtitle p {
    text-align: center;
    font-weight: 800;
    text-transform: uppercase;
}

.subtitle small {
    font-size: 0.5rem;
    color: #d6d6d6;
}
</style>
