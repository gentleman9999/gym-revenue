<template>
    <div class="flex flex-row flex-wrap justify-between" v-bind="$attrs">
        <div>
            <p>Dashboard - {{ teamName }}</p>
        </div>
        <div>
            <p>{{ accountName }}</p>
        </div>
        <slot />
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        teamName: {
            type: String,
            required: true,
        },
        accountName: {
            type: String,
            required: true,
        },
    },
});
</script>
