<template>
    <div class="w-1/6">{{ folder.files.length }} file(s)</div>
    <div class="w-1/6 uppercase">folder</div>
    <div class="w-2/6">
        {{ defaultTransform.created_at(folder.created_at) }}
    </div>
    <div class="w-2/6">
        <Button ghost @click="handleTrash">
            <font-awesome-icon icon="trash" class="text-base-content" />
        </Button>
        <Button ghost @click="handleRename">
            <font-awesome-icon icon="edit" class="text-base-content" />
        </Button>
    </div>
</template>
<style scoped></style>
<script setup>
import prettyBytes from "pretty-bytes";
import { defaults as defaultTransform } from "@/Components/CRUD/helpers/transforms";
import Button from "@/Components/Button.vue";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTrash, faEdit, faSlidersH } from "@fortawesome/pro-solid-svg-icons";

library.add(faTrash, faEdit, faSlidersH);

const props = defineProps({
    folder: {
        type: Object,
        required: true,
    },
    handleRename: {
        type: Function,
    },
    handleTrash: {
        type: Function,
    },
});
</script>
