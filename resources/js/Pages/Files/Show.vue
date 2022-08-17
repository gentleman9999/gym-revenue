<template>
    <LayoutHeader title="File Management">
        <h2 class="font-semibold text-xl leading-tight">File Manager</h2>
    </LayoutHeader>
    <div class="files-container">
        <div class="row">
            <file-actions />
            <file-display-mode
                :display-mode="displayMode"
                :handleChange="updateDisplayMode"
            />
        </div>
        <div class="row">
            <file-nav />
            <file-search />
        </div>
        <file-contents
            :files="files"
            :folders="$page.props.folders"
            :displayMode="displayMode"
            :handleRename="handleRename"
            :handlePermissions="handlePermissions"
            :handleTrash="handleTrash"
        />
    </div>

    <!-- Section for Modals -->
    <daisy-modal
        id="renameModal"
        ref="renameModal"
        @close="selectedItem = null"
    >
        <rename-form
            :item="selectedItem"
            v-if="selectedItem"
            @success="renameModal.close"
        />
    </daisy-modal>

    <daisy-modal
        ref="permissionsModal"
        id="permissionsModal"
        @close="selectedItemPermissions = null"
    >
        <h1 class="font-bold mb-4">Modify File Permissions</h1>
        <Permissions-Form
            :file="selectedItemPermissions"
            v-if="selectedItemPermissions"
            @success="permissionsModal.close"
        />
    </daisy-modal>
</template>

<style scoped>
.files-container {
    @apply lg:max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 position-unset relative;
}

.row {
    @apply flex flex-row justify-between items-center;
}
</style>

<script setup>
import { watchEffect, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import RenameForm from "./Partials/RenameForm.vue";
import PermissionsForm from "./Partials/PermissionsForm.vue";
import { Inertia } from "@inertiajs/inertia";
import DaisyModal from "@/Components/DaisyModal.vue";
import FileItem from "@/Components/FileItem/index.vue";
import Button from "@/Components/Button.vue";
import FileDisplayMode from "./Partials/FileDisplayMode.vue";
import FileActions from "./Partials/FileActions.vue";
import FileContents from "./Partials/FileContents.vue";
import FileSearch from "./Partials/FileSearch.vue";
import FileNav from "./Partials/FileNav.vue";
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

const selectedItem = ref(null);
const selectedItemPermissions = ref(null);

const handleRename = (data, type) => {
    selectedItem.value = data;
};

const handlePermissions = (data) => {
    selectedItemPermissions.value = data;
};

const handleTrash = (data, type) => {
    if (type === "file") {
        Inertia.delete(route("files.trash", data.id));
    } else {
        Inertia.delete(route("folders.delete", data.id));
    }
};

const renameModal = ref(null);
const permissionsModal = ref(null);

watchEffect(() => {
    if (selectedItem.value) {
        renameModal.value.open();
    }
    if (selectedItemPermissions.value) {
        permissionsModal.value.open();
    }
});

const displayMode = ref("desktop");

const updateDisplayMode = (value) => {
    displayMode.value = value;
};

const goRoot = () => {
    Inertia.get(route("files"));
};
</script>
