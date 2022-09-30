<template>
    <div
        class="recycle-icon-wrapper"
        :class="{
            'w-9 flex justify-center': iconSize == '2x',
            'w-min': iconSize == '3x',
        }"
    >
        <font-awesome-icon
            icon="trash"
            :size="iconSize"
            class="text-secondary"
        />
    </div>
</template>
<style scoped>
.recycle-icon-wrapper {
    @apply relative;
}
</style>
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTrash } from "@fortawesome/pro-solid-svg-icons";

library.add(faTrash);

const props = defineProps({
    iconSize: {
        type: String,
    },
});
</script>
