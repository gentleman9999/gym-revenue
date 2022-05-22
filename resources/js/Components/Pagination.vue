<template>
    <div class="btn-group" v-if="links?.length > 3">
        <template v-for="(link, key) in links">
            <div
                v-if="link.url === null"
                class="btn btn-outline btn-disabled disabled"
                v-html="link.label"
            />
            <inertia-link
                v-else
                class="btn btn-outline"
                :class="{ 'bg-primary': link.active }"
                :href="link.url"
                v-html="link.label"
            />
        </template>
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        links: Array,
    },
});
</script>
