<template>
    <LayoutHeader title="File Management">
        <h2 class="font-semibold text-xl leading-tight">File Manager</h2>
    </LayoutHeader>
    <div class="files-container">
        <div class="row">
            <file-actions :folderName="folderName" />
            <file-display-mode
                :display-mode="displayMode"
                :handleChange="updateDisplayMode"
            />
        </div>
        <div class="row">
            <file-nav :folderName="folderName" class="nav-desktop" />
            <file-search />
        </div>
        <div class="row">
            <file-nav :folderName="folderName" class="nav-mobile" />
        </div>
        <file-contents
            :files="files"
            :folders="$page.props.folders"
            :displayMode="displayMode"
            :handleRename="handleRename"
            :handlePermissions="handlePermissions"
            :handleTrash="handleTrash"
            :handleShare="handleShare"
        />
    </div>

    <!-- Section for Modals -->
    <daisy-modal id="confirmModal" ref="confirmModal">
        <confirm-modal :data="item2Remove" @success="confirmTrash" />
    </daisy-modal>
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
        <h1 class="font-bold mb-4">Modify Permissions</h1>
        <permissions-form
            :item="selectedItemPermissions"
            v-if="selectedItemPermissions"
            @success="permissionsModal.close"
        />
    </daisy-modal>
    <daisy-modal ref="shareModal" id="shareModal" @close="folder2Share = null">
        <h1 class="font-bold mb-4">Share with others</h1>
        <share-form
            :item="folder2Share"
            v-if="folder2Share"
            @success="shareModal.close"
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

.row.nav-desktop {
    @apply hidden md:flex;
}

.row.nav-mobile {
    @apply flex md:hidden mt-2;
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
import ShareForm from "./Partials/ShareForm.vue";
import Button from "@/Components/Button.vue";
import FileDisplayMode from "./Partials/FileDisplayMode.vue";
import FileActions from "./Partials/FileActions.vue";
import FileContents from "./Partials/FileContents.vue";
import FileSearch from "./Partials/FileSearch.vue";
import FileNav from "./Partials/FileNav.vue";
import ConfirmModal from "./Partials/ConfirmModal.vue";
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
    folderName: {
        type: String,
    },
});

const selectedItem = ref(null);
const selectedItemPermissions = ref(null);
const folder2Share = ref(null);
const item2Remove = ref(null);

const handleRename = (data, type) => {
    selectedItem.value = data;
};

const handlePermissions = (data) => {
    selectedItemPermissions.value = data;
};

const handleShare = (data) => {
    folder2Share.value = data;
};

const handleTrash = (data, type) => {
    confirmModal.value.open();
    item2Remove.value = {
        name: type === "file" ? data.filename : data.name,
        id: data.id,
        type: type,
        count: data.files?.length,
    };
};

const confirmTrash = () => {
    let id = item2Remove.value.id;
    if (item2Remove.value.type === "file") {
        Inertia.delete(route("files.trash", id));
    } else {
        Inertia.delete(route("folders.delete", id));
    }
    confirmModal.value.close();
};

const renameModal = ref(null);
const permissionsModal = ref(null);
const shareModal = ref(null);
const confirmModal = ref(null);

watchEffect(() => {
    if (selectedItem.value) {
        renameModal.value.open();
    }
    if (selectedItemPermissions.value) {
        permissionsModal.value.open();
    }
    if (folder2Share.value) {
        shareModal.value.open();
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
