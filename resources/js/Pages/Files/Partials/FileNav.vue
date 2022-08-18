<template>
    <div class="row space-x-2 cursor-pointer">
        <span
            @click="goRoot()"
            class="hover:bg-base-content/20 rounded px-2 py-1"
        >
            <font-awesome-icon icon="home" class="text-base-content" />
        </span>
        <span class="pr-1" v-if="folderName">
            <font-awesome-icon icon="angle-right" class="text-base-content" />
        </span>
        <span v-if="folderName">{{ folderName }}</span>
    </div>
</template>
<style scoped>
.row {
    @apply flex flex-row justify-between items-center;
}
span {
    @apply text-xl;
}
</style>
<script setup>
import { Inertia } from "@inertiajs/inertia";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faHome, faAngleRight } from "@fortawesome/pro-solid-svg-icons";
library.add(faHome, faAngleRight);

const props = defineProps({
    folderName: {
        type: String,
    },
});

const goRoot = () => {
    Inertia.get(route("files"));
};
</script>
