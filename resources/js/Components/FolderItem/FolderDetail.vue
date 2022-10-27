<template>
    <div class="w-1/6 hidden md:visible">{{ folder.files.length }} file(s)</div>
    <div class="w-1/6 uppercase hidden md:visible">folder</div>
    <div class="w-2/6 hidden md:visible">
        {{ defaultTransform.created_at(folder.created_at) }}
    </div>
    <div class="md:w-2/6 w-3/6 flex-wrap">
        <Button ghost @click="handleRestore" v-if="!showTrash">  
            <font-awesome-icon icon="trash-restore" class="text-base-content" />
        </Button> 
        <Button ghost @click="handleTrash"  v-if="showTrash">
            <font-awesome-icon icon="trash" class="text-base-content" />
        </Button>
        <Button ghost @click="handleRename">
            <font-awesome-icon icon="edit" class="text-base-content" />
        </Button>
        <Button ghost @click="handlePermissions">
            <font-awesome-icon icon="sliders-h" class="text-base-content" />
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
import { faTrash, faEdit, faSlidersH, faTrashRestore } from "@fortawesome/pro-solid-svg-icons";

library.add(faTrash, faEdit, faSlidersH, faTrashRestore);

const props = defineProps({
    folder: {
        type: Object,
        required: true,
    },
    handleRename: {
        type: Function,
    },
    handlePermissions: {
        type: Function,
    },
    handleTrash: {
        type: Function,
    },
    handleRestore: {
        type: Function,
    },
});
</script>
