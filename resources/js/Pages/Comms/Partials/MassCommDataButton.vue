<template>
    <inertia-link
        class="btn justify-self-end"
        :href="href"
    >
        <slot />
        <div class="m-2 flex gap-1">
            <span class="bg-info p-1" title="Total">{{ total }}</span>
            <span class="p-1 bg-success" title="Active">{{ active }}</span>
        </div>
    </inertia-link>
</template>

<style scoped>
a{
    @apply bg-base-100;
}
span {
    @apply rounded;
}
</style>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        total: {
            type: Number,
            required: true,
        },
        active: {
            type: Number,
            required: true,
        },
        href:{
            type: String,
            required: true,
        }
    },
});
</script>
