<template>
    <Link :href="'/'">
        <jet-application-mark />
    </Link>
    <div class="subtitle">
        <p><small>Conceptual Prototype</small></p>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import JetApplicationMark from "@/Jetstream/ApplicationMark";

    export default defineComponent({
        components: {
            JetApplicationMark
        },
    })
</script>

<style scoped>
.logo-e {
    fill: #0074C8;
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
    color: #D6D6D6;
}
</style>
