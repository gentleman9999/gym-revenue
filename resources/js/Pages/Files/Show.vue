<template>
    <LayoutHeader title="File Management">
        <h2 class="font-semibold text-xl leading-tight">File Manager</h2>
    </LayoutHeader>
    <div class="files-container">
        <div class="flex flex-row justify-between">
            <Button
                primary
                size="sm"
                @click="Inertia.visitInModal(route('files.upload'))"
            >
                Upload
            </Button>
            <div class="space-x-3">
                <Button
                    :primary="displayMode === 'desktop'"
                    :ghost="displayMode !== 'desktop'"
                    @click="displayMode = 'desktop'"
                >
                    <font-awesome-icon
                        icon="desktop"
                        class="text-base-content"
                    />
                </Button>
                <Button
                    :primary="displayMode === 'list'"
                    :ghost="displayMode !== 'list'"
                    @click="displayMode = 'list'"
                >
                    <font-awesome-icon icon="list" class="text-base-content" />
                </Button>
            </div>
        </div>
        <div class="flex flex-row flex-wrap mt-4">
            <file-item
                v-for="file in files.data"
                :file="file"
                :key="file?.id"
                :mode="displayMode"
                :handleRename="handleRename"
                :handlePermissions="handlePermissions"
                :handleTrash="handleTrash"
            />
        </div>
    </div>
    <daisy-modal
        id="filenameModal"
        ref="filenameModal"
        @close="selectedFile = null"
    >
        <file-form
            :file="selectedFile"
            v-if="selectedFile"
            @success="filenameModal.close"
        />
    </daisy-modal>

    <daisy-modal
        ref="permissionsModal"
        id="permissionsModal"
        @close="selectedFilePermissions = null"
    >
        <h1 class="font-bold mb-4">Modify File Permissions</h1>
        <Permissions-Form
            :file="selectedFilePermissions"
            v-if="selectedFilePermissions"
            @success="permissionsModal.close"
        />
    </daisy-modal>
</template>

<style scoped>
.files-container {
    @apply lg:max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 position-unset relative;
}
</style>

<script setup>
import { watchEffect, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import FileForm from "./Partials/FileForm.vue";
import PermissionsForm from "./Partials/PermissionsForm.vue";
import { Inertia } from "@inertiajs/inertia";
import DaisyModal from "@/Components/DaisyModal.vue";
import FileItem from "@/Components/FileItem/index.vue";
import Button from "@/Components/Button.vue";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faList, faDesktop } from "@fortawesome/pro-solid-svg-icons";

library.add(faList, faDesktop);

const props = defineProps({
    sessions: {
        type: Array,
    },
    files: {
        type: Array,
    },
    title: {
        type: String,
    },
    isClientUser: {
        type: Boolean,
    },
    filters: {
        type: Array,
    },
});

const selectedFile = ref(null);
const selectedFilePermissions = ref(null);

const handleRename = (data) => {
    selectedFile.value = data;
};
const handlePermissions = (data) => {
    selectedFilePermissions.value = data;
};

const handleTrash = (data) => {
    Inertia.delete(route("files.trash", data.id));
};
const filenameModal = ref(null);
const permissionsModal = ref(null);

watchEffect(() => {
    if (selectedFile.value) {
        filenameModal.value.open();
    }
    if (selectedFilePermissions.value) {
        permissionsModal.value.open();
    }
});

const displayMode = ref("desktop");
</script>
