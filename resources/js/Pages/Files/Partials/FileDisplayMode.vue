<template>
    <div class="space-x-3">
        <Button
            :primary="displayMode === 'desktop'"
            :ghost="displayMode !== 'desktop'"
            @click="handleChange('desktop')"
        >
            <font-awesome-icon icon="desktop" class="text-base-content" />
        </Button>
        <Button
            :primary="displayMode === 'list'"
            :ghost="displayMode !== 'list'"
            @click="handleChange('list')"
        >
            <font-awesome-icon icon="list" class="text-base-content" />
        </Button>
    </div>
</template>
<script setup>
import Button from "@/Components/Button.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faList, faDesktop } from "@fortawesome/pro-solid-svg-icons";
library.add(faList, faDesktop);

const props = defineProps({
    displayMode: {
        type: Boolean,
    },
    handleChange: {
        type: Function,
    },
});
</script>
