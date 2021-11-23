<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links">
                <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4  border border-base-300 rounded"
                     v-html="link.label"/>
                <Link v-else
                      class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border border-base-300 rounded hover:bg-base-300 focus:border-primary focus:"
                      :class="{ 'bg-base-300': link.active }" :href="link.url" v-html="link.label"/>
            </template>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue';

export default defineComponent({
    props: {
        links: Array,
    },
});
</script>
